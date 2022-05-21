<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sqits\UserStamps\Concerns\HasUserStamps;

class Project extends Model
{
    //use HasUserStamps;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'projects';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['client', 'category','talents'];

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
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProjectCategory');
    }

    public function talents()
    {
        return $this->belongsToMany('App\User', 'project_talent');
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile');
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
    public function getClientAttribute()
    {
        return $this->client()->first();
    }

    public function getCategoryAttribute()
    {
        return $this->category()->first();
    }

    public function getTalentsAttribute()
    {
        return $this->talents()->getResults();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
