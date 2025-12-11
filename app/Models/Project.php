<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    protected $guarded = [];  

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('project.status', 'active');
        });
         static::addGlobalScope('orderby', function (Builder $builder) {
            $builder->orderBy('orderby', 'asc');
        });
        
    }

    
}
