<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getTasks()
    {
        $user = auth()->user();
        if ($user->hasRole('User')) {
            $tasks = $user->tasks()->getQuery();
        } elseif ($user->hasRole('Manager')) {
            $tasks = Task::query();
        } else {
            abort('403', 'Unauthorized');
        }
        $TasksQuery = QueryBuilder::for($tasks)
        ->allowedFilters(
            'user.id',
            'user.name',
            'status.id',
            'status.name',
            AllowedFilter::scope('date.from', 'date_from'),
            AllowedFilter::scope('date.to', 'date_to')
        )
        ->allowedSorts('title')
        ->allowedIncludes('user', 'status', 'dependencies')
        ->defaultSort('due_date')
        ->paginate(24);

        return TaskResource::collection($TasksQuery);
    }

    public function getSingleTask(Task $task)
    {
        $user = auth()->user();
        if ($user->hasRole('User')) {
            $task = $user->tasks()->where('id', $task->id);
        } elseif ($user->hasRole('Manager')) {
            $task = Task::where('id', $task->id);
        } else {
            abort('403', 'Unauthorized');
        }
        $taskQuery = QueryBuilder::for($task)
        ->allowedIncludes('user', 'status', 'dependencies')
        ->first();

        if (! $taskQuery) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot access this task'], 403);
        }

        return new TaskResource($taskQuery);
    }

    public function createTask(array $data)
    {
        try {
            DB::beginTransaction();
            $data['start_date'] = isset($data['start_date']) ? $data['start_date'] : now();
            $data['created_by'] = \Auth::id();
            $data['updated_by'] = \Auth::id();
            $data['created_at'] = now();
            $data['updated_at'] = now();

            $task = Task::create(collect($data)->except('dependencies_ids')->toArray());
            if (isset($data['dependencies_ids'])) {
                $this->validateDependencies($data['dependencies_ids'] ?? null, $task->id);
                $task->dependencies()->syncWithoutDetaching($data['dependencies_ids']);
            }
            DB::commit();
        } catch(ValidationException $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task->load('user', 'status', 'dependencies')),
        ], 200);
    }

    protected function validateDependencies(array $dependenciesIds, int $taskId): void
    {
        if ($dependenciesIds && $taskId && in_array($taskId, $dependenciesIds)) {
            throw ValidationException::withMessages([
                'dependencies_ids' => 'A task cannot depend on itself.',
            ]);
        }
    }

    public function updateTaskData(Task $task, array $data)
    {
        try {
            //update tasks only if the current user is a manager and the task is not completed yet
            if ($task->status->name === 'Completed') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This task is already completed!'], 403);
            }
            if (auth()->user()->hasRole('Manager') && $task->status->name != 'Completed') {
                DB::beginTransaction();
                if (isset($data['dependencies_ids'])) {
                    $this->validateDependencies($data['dependencies_ids'], $task->id);
                }
                $data['updated_by'] = \Auth::id();
                $data['updated_at'] = now();
                $task->update(collect($data)->except('dependencies_ids')->toArray());

                if (isset($data['dependencies_ids'])) {
                    $task->dependencies()->syncWithoutDetaching($data['dependencies_ids']);
                }
                DB::commit();
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have enough permissions to update this task'], 403);
                // abort(403, 'You do not have enough permissions to update this task');
            }
        } catch(ValidationException $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task->load('status', 'user')),
        ], 200);
    }

    public function updateTaskStatus(Task $task, array $data)
    {
        try {
            $user = auth()->user();
            if (($user->hasRole('User') && $user->tasks()->where('id', $task->id)->exists()) || $user->hasRole('Manager')) {
                //Check if the task dependencies are completed
                $incompleteDeps = $task->dependencies()->where('status_id', '!=', 2)->pluck('id');

                if ($incompleteDeps->isNotEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Cannot complete the task.Task dependencies are still not completed',
                    ], 400);
                } else {
                    $task->update([
                        'updated_by' => \Auth::id(),
                        'status_id' => $data['status_id'],
                    ]);

                    return response()->json([
                        'message' => 'Task Status updated successfully',
                        'data' => new TaskResource($task->load('status', 'user', 'dependencies')),
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You do not have enough permissions to update this task'], 403);
                // abort(403, 'You do not have enough permissions to update this task');
            }
        } catch(ValidationException $e) {
            throw $e;
        }
    }
}
