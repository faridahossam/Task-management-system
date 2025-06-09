<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    // use Paginator;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getCreatorAttribute()
    {
        return User::findOrFail($this->created_by);
    }

    public function scopeDateFrom(Builder $query, $date)
    {
        return $query->where('due_date', '>=', Carbon::parse($date));
    }

    public function scopeDateTo(Builder $query, $date)
    {
        isset($date) ? $date : now();

        return $query->where('due_date', '<=', Carbon::parse($date));
    }
}
