<?php

namespace App\Models;

use App\Enum\ExplorerMissionMessagesTypesEnum;
use App\Enum\ExplorerMissionPropositionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExplorerMissionConversation extends Model
{
    /*
|--------------------------------------------------------------------------
| GLOBAL VARIABLES
|--------------------------------------------------------------------------
*/

    protected $table = 'explorer_mission_conversations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['proposition', 'lastMessage', 'messageToDisableDate'];

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
     * Get the message of the conversation
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany('App\Models\ExplorerMissionConversationMessage', 'id_conversation');
    }

    /**
     * @return BelongsTo
     */
    public function proposition(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionProposition', 'id_proposition');
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
     * @return mixed
     */
    public function getPropositionAttribute()
    {
        return $this->proposition()->getResults();
    }

    /**
     * @return mixed
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }


    public function getMessageToDisableDateAttribute()
    {
        $missionProposition = $this->proposition;

        if ($missionProposition->status == ExplorerMissionPropositionStatusEnum::WAITING_PAYMENT) {
            return $this->messages->where("type","=", ExplorerMissionMessagesTypesEnum::USER_QUOTE)->first()->created_at;
        }

        if ($missionProposition->status == ExplorerMissionPropositionStatusEnum::WAITING_WORK) {
            return $this->messages->where("type","=", ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_QUOTE_PAID)->first()->created_at;
        }

        if ($missionProposition->status == ExplorerMissionPropositionStatusEnum::FINISHED_WAITING_CLOSING) {
            return $this->messages->where("type","=", ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_FINISHED)->first()->created_at;
        }

        if ($missionProposition->status == ExplorerMissionPropositionStatusEnum::CLOSED) {
            return $this->messages->where("type","=", ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CLOSED)->first()->created_at;
        }

        if ($missionProposition->status == ExplorerMissionPropositionStatusEnum::WAITING_QUOTE) {
            return $this->created_at;
        }

        return new \DateTime();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
