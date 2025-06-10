<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'user' => new UserResource($this->whenLoaded('user')),
            'status' => new StatusResource($this->whenLoaded('status')),
            'created_by' => new UserResource($this->creator),
            'dependencies_ids' => $this->whenLoaded('dependencies', function () {
                return $this->dependencies->map(fn ($task) => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date,
                ]);
            }),
        ];
    }
}
