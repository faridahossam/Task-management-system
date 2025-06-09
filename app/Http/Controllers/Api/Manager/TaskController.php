<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CreateTask;
use App\Http\Requests\Task\UpdateTaskData;
use App\Http\Requests\Task\UpdateTaskStatus;
use App\Models\Task;
use App\Services\TaskService;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->taskService->getallTasks();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTask $request)
    {
        return $this->taskService->createTask($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->taskService->getSingleTask($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskData $request, Task $task)
    {
        return $this->taskService->updateTaskData($task, $request->validated());
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateTaskStatus(UpdateTaskStatus $request, Task $task)
    {
        return $this->taskService->updateTaskStatus($task, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
