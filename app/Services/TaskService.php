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

    public function getAllTasks()
    {
        $allTasksQuery = QueryBuilder::for(Task::class)
        ->allowedFilters('user.id', 'user.name', 'status.id', 'status.name', AllowedFilter::scope('date.from', 'date_from'), AllowedFilter::scope('date.to', 'date_to'))
        ->allowedSorts('title')
        ->allowedIncludes('user', 'status', 'dependencies')
        ->defaultSort('due_date')
        ->paginate(24);

        return TaskResource::collection($allTasksQuery);
    }

    public function getSingleTask(Task $task)
    {
        $taskQuery = QueryBuilder::for(Task::class)
        ->allowedIncludes('user', 'status', 'dependencies')
        ->findOrFail($task->id);

        return new TaskResource($taskQuery);
    }

    public function getUserSingleTask(Task $task)
    {
        $user = \Auth::user();

        $taskQuery = QueryBuilder::for($user->tasks()->where('id', $task->id))
            ->allowedIncludes('user', 'status', 'dependencies')
            ->first();

        if ($taskQuery) {
            return new TaskResource($taskQuery);
        }

        return response()->json([
            'message' => "You don't have access to view this task",
        ], 403);
    }

    public function getCurrentUserTasks()
    {
        $user = \Auth::user();
        $currentUserTasks = $user->tasks();

        $tasksQuery = QueryBuilder::for($currentUserTasks)
        ->allowedFilters('user.id', 'user.name', 'status.id', 'status.name', AllowedFilter::scope('date.from', 'date_from'), AllowedFilter::scope('date.to', 'date_to'))
        ->allowedSorts('title')
        ->allowedIncludes('user', 'status', 'dependencies')
        ->defaultSort('due_date')
        ->paginate(24);

        return TaskResource::collection($tasksQuery);
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
            DB::beginTransaction();
            if (isset($data['dependencies_ids'])) {
                $this->validateDependencies($data['dependencies_ids'], $task->id);
            }
            $data['upated_by'] = \Auth::id();
            $data['updated_at'] = now();
            $task->update(collect($data)->except('dependencies_ids')->toArray());

            if (isset($data['dependencies_ids'])) {
                $task->dependencies()->syncWithoutDetaching($data['dependencies_ids']);
            }
            DB::commit();
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
        } catch(ValidationException $e) {
            throw $e;
        }
    }
}
