<?php

namespace App\Services;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use ValidationException;

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
        ->allowedIncludes('user', 'status')
        ->defaultSort('due_date')
        ->paginate(24);

        return TaskResource::collection($allTasksQuery);
    }

    public function getSingleTask(Task $task)
    {
        $taskQuery = QueryBuilder::for(Task::class)
        ->allowedIncludes('user', 'status')
        ->findOrFail($task->id);

        return new TaskResource($taskQuery);
    }

    public function getUserSingleTask(Task $task)
    {
        if ($task->user_id == \Auth::id()) {
            $taskQuery = QueryBuilder::for(Task::class)
            ->allowedIncludes('user', 'status')
            ->findOrFail($task->id);

            return new TaskResource($taskQuery);
        } else {
            return response()->json([
                'message' => "You don't have access to view this task",
            ], 403);
        }
    }

    public function getCurrentUserTasks()
    {
        $currentUserTasks = Task::where('user_id', \Auth::id());

        $tasksQuery = QueryBuilder::for($currentUserTasks)
        ->allowedFilters('user.id', 'user.name', 'status.id', 'status.name', AllowedFilter::scope('date.from', 'date_from'), AllowedFilter::scope('date.to', 'date_to'))
        ->allowedSorts('title')
        ->allowedIncludes('user', 'status')
        ->defaultSort('due_date')
        ->paginate(24);

        return TaskResource::collection($tasksQuery);
    }

    public function createTask(array $data)
    {
        try {
            $data['start_date'] = isset($data['start_date']) ? $data['start_date'] : now();
            $data['created_by'] = \Auth::id();
            $data['updated_by'] = \Auth::id();
            $data['created_at'] = now();
            $data['updated_at'] = now();
            $task = Task::create($data);

            return response()->json([
                'message' => 'Task created successfully',
                'data' => new TaskResource($task),
            ], 200);
        } catch(ValidationException $e) {
            throw $e;
        }
    }

    public function updateTaskData(Task $task, array $data)
    {
        try {
            $data['upated_by'] = \Auth::id();
            $data['updated_at'] = now();
            $task->update($data);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => new TaskResource($task->load('status', 'user')),
            ], 200);
        } catch(ValidationException $e) {
            throw $e;
        }
    }

    public function updateTaskStatus(Task $task, array $data)
    {
        try {
            $task->update([
                'updated_by' => \Auth::id(),
                'status_id' => $data['status_id'],
            ]);

            return response()->json([
                'message' => 'Task Status updated successfully',
                'data' => new TaskResource($task->load('status', 'user')),
            ], 200);
        } catch(ValidationException $e) {
            throw $e;
        }
    }
}
