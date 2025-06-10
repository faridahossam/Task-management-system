<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $guarded = [];

    /**
     * Get the tasks associated with the status.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
