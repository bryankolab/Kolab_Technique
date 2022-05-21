<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sqits\UserStamps\Concerns\HasUserStamps;

class ExplorerMissionProposition extends Model
{
    use HasUserStamps;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'explorer_mission_propositions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['freelance', 'client', 'mission', 'drive', 'kolabProject'];

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

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function freelance(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Return the related explorer Mission
     * @return BelongsTo
     */
    public function explorerMission(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMission', 'id_mission');
    }

    /**
     * @return HasOne
     */
    public function conversation(): HasOne
    {
        return $this->hasOne('App\Models\ExplorerMissionConversation', 'id_proposition');
    }

    /**
     * @return HasOne
     */
    public function drive(): HasOne
    {
        return $this->hasOne('App\Models\ExplorerDrive', "id_proposition");
    }

    public function kolabProject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Project', 'kolab_project_id');
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
        return $this->client()->getResults();

    }

    public function getFreelanceAttribute()
    {
        return $this->freelance()->getResults();
    }

    public function getMissionAttribute()
    {
        return $this->explorerMission()->getResults();
    }

    public function getDriveAttribute()
    {
        return $this->drive()->getResults();
    }

    public function getKolabProjectAttribute()
    {
        return $this->kolabProject()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
