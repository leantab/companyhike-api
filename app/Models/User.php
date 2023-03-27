<?php

namespace App\Models;

use Carbon\Carbon;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Repositories\SherpaRepository;
use Filament\Models\Contracts\FilamentUser;
use Laravel\Jetstream\HasProfilePhoto;

class User extends Authenticatable implements JWTSubject, FilamentUser
{
    use HasFactory, Notifiable, HasProfilePhoto;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ch_admin' => 'boolean',
        'is_active' => 'boolean',
        'user_data' => 'array',
    ];

    private SherpaRepository $sherpaRepository;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->sherpaRepository = new SherpaRepository();
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function communities()
    {
        return $this->belongsToMany(Community::class)->withPivot([
            'user_id', 'community_id', 'role_id', 'created_at', 'updated_at'
        ]);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_user')->withPivot([
            'company_name',
            'avatar',
            'bankrupt',
            'dismissed',
            'ceo_parameters',
            'results',
            'is_funded',
            'created_at',
            'updated_at',
        ]);
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastname;
    }

    public function isChAdmin(): bool
    {
        return (bool) $this->ch_admin;
    }

    public function canAccessFilament(): bool
    {
        return (bool) $this->ch_admin;
    }

    public function canAccessCommunity(Community $community)
    {
        return $this->communities()->where('community_id', $community->id)->exists();
    }

    public function allowAccessToCommunity(Community $community)
    {
        CommunityUser::create([
            'user_id' => $this->id,
            'community_id' => $community->id
        ]);
        // $this->communities()->attach($community);
    }

    public function isCommunityAdmin(Community $community)
    {
        $rel = $this->communities()->where('community_id', $community->id)->first();
        if ($rel == null) {
            return false;
        }
        if ($rel->pivot->role_id == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function makeCommunityAdmin(Community $community)
    {
        $this->communities()->updateExistingPivot($community, ['role_id' => 1]);
    }

    public function administratedCommunities()
    {
        return $this->belongsToMany(Community::class)->wherePivot('role_id', 1);
    }

    public function acceptCommunityInvitation($invitation_id)
    {
        $inv = CommunityInvitation::findOrFail($invitation_id);
        $com = Community::find($inv->community_id);

        $this->allowAccessToCommunity($com);

        $inv->accepted = true;
        $inv->accepted_at = Carbon::now();
        $inv->save();
    }

    public function get_open_games()
    {
        return $this->belongsToMany(Game::class)->where('is_open', true);
    }

    public function isGameAdmin(Game $game)
    {
        $game = $this->games()->where('game_id', $game->id)->first();
        if ($game == null) {
            return false;
        }else{
            if ($game->pivot->is_admin) {
                return true;
            }else{
                return false;
            }
        }
    }

    public function acceptGameInvitation(GameInvitation $invitation): void
    {
        $com = Community::find($invitation->community_id);

        $game = Game::find($invitation->game_id);

        if (!$this->canAccessCommunity($com)) {
            $this->allowAccessToCommunity($com);
        }

        $this->enterGame($game);

        $invitation->user_id = $this->id;
        $invitation->accepted = true;
        $invitation->accepted_at = Carbon::now();
        $invitation->save();
    }

    public function enterGame(Game $game)
    {
        $company_name = 'Ceo ' . rand(16, 99);
        $avatar = rand(1, 16);

        $this->sherpaRepository->addSimpleCeo($game->id, $this->id, $company_name, $avatar);
    }

}
