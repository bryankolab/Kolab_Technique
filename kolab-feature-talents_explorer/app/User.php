<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use Sqits\UserStamps\Concerns\HasUserStamps;

class User extends Authenticatable
{
    use Notifiable;

    //use HasUserStamps;
    use HasRoles;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 'phone', 'job_id', 'description', 'website', 'client_job', 'company'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['skills', 'job', 'tasks', 'role', 'portfolios', 'lastPortfolioMainMedia'];

    /**
     * @return BelongsToMany
     */
    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'skill_user', 'user_id', 'skill_id');
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'project_talent', 'user_id', 'project_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany('App\Models\Task', 'task_talent', 'user_id', 'task_id');
    }

    /**
     * @return BelongsToMany
     */
    public function portfolios()
    {
        return $this->belongsToMany('App\Models\Portfolio', 'portfolios_talents', 'user_id', 'portfolio_id');
    }

    /**
     * Return the skills attribute
     *
     * @return Collection|mixed
     */
    public function getSkillsAttribute()
    {
        return $this->skills()->getResults();
    }

    /**
     * Return the projects attribute
     *
     * @return Collection|mixed
     */
    public function getProjectsAttribute()
    {
        return $this->projects()->getResults();
    }

    /**
     * @return BelongsTo
     */
    public function job()
    {
        return $this->belongsTo('App\Models\Job');
    }

    /**
     * Return the job attribure
     *
     * @return Collection|mixed
     */
    public function getJobAttribute()
    {
        return $this->job()->first();
    }

    /**
     * Return the tasks attribute
     *
     * @return Collection|mixed
     */
    public function getTasksAttribute()
    {
        return $this->tasks()->getResults();
    }

    /**
     * Return the tasks attribute
     *
     * @return Collection|mixed
     */
    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }

    /**
     * Return the tasks attribute
     *
     * @return Collection|mixed
     */
    public function getPortfoliosAttribute()
    {
        return $this->portfolios()->getResults();
    }

    public function getLastPortfolioMainMediaAttribute()
    {
        $firstPortfolio = $this->portfolios()->first();
        if ($firstPortfolio == null) {
            return null;
        }

        $media = $firstPortfolio->mainMedia;

        if ($media == null || $media['path'] == '/images/explorer/kolab-logo.png') {
            return null;
        } else {
            return $media;
        }

    }
}
