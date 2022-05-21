<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Sqits\UserStamps\Concerns\HasUserStamps;

class ExplorerMissionConversationMessage extends Model
{
    use HasUserStamps;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'explorer_mission_conversation_messages';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['createdByUsername', 'createdByAvatar', 'quote', 'driveLink', 'missionPropositionStatus'];

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
     * Return the conversation the message belongs to
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionConversation', 'id_conversation', 'id');
    }

    /**
     * Return the quote referenced in the message if is there one.
     * @return BelongsTo
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionQuote', 'id_quote');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo('App\User', 'created_by', "id");
    }

    public function driveLink(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerDriveLink', 'id_drive_link', "id");
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

    public function getCreatedByUsernameAttribute()
    {
        if ($this->createdByUser()->first() != null) {
            return $this->createdByUser()->first()->name;
        }

        return '';
    }

    public function getCreatedByAvatarAttribute()
    {
        if ($this->createdByUser()->first() != null) {
            return $this->createdByUser()->first()->avatar;
        }

        return '';
    }

    public function getQuoteAttribute()
    {
        return $this->quote()->first();
    }

    public function getDriveLinkAttribute()
    {
        return $this->driveLink()->first();
    }

    public function getMissionPropositionStatusAttribute()
    {
        return $this->conversation()->first()->proposition->status;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
