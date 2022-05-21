<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Sqits\UserStamps\Concerns\HasUserStamps;

class Portfolio extends Model
{
    use HasUserStamps;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'portfolios';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['medias', 'mainMedia', 'userName'];

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
     * @return HasMany
     */
    public function medias()
    {
        return $this->hasMany(PortfolioMedia::class);
    }

    /**
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'portfolios_talents', 'portfolio_id', 'user_id');
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
    public function getMediasAttribute()
    {
        return $this->medias()->getResults();
    }

    public function getMainMediaAttribute()
    {
        $medias = $this->medias()->getResults();

        if ($medias != null) {
            foreach ($medias as $media) {
                if ($media->media_type == "image") {
                    return $media;
                }
            }
        }

        return ["path" => "/images/explorer/kolab-logo.png"];
    }

    public function getUserNameAttribute()
    {
        return $this->users()->first()->name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
