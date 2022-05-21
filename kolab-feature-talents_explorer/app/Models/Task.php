<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Sqits\UserStamps\Concerns\HasUserStamps;

class Task extends Model
{
    //use HasUserStamps;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tasks';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['taskTypes'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    /**
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * @return BelongsToMany
     */
    public function taskTypes()
    {
        return $this->belongsToMany('App\Models\TaskType', 'task_type_task');
    }

    /**
     * The talents that part of the project.
     */
    public function talents()
    {
        return $this->belongsToMany('App\User', 'task_talent');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * @return Model|BelongsTo|object|null
     */
    public function getProjectAttribute(){
        return $this->project()->first();
    }

    /**
     * @return Collection|mixed
     */
    public function getTaskTypesAttribute(){
        return $this->taskTypes()->getResults();
    }

    /**
     * @return Collection|mixed
     */
    public function getTalentsAttribute(){
        return $this->talents()->getResults();
    }


    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
