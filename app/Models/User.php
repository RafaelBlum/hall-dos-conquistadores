<?php
declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\PanelTypeEnum;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasAvatar, FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'panel',
        'avatar'
    ];

    protected $hidden = ['password', 'remember_token'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function channel(): HasOne
    {
        return $this->hasOne(Channel::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(Channel::class, 'channel_follower')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'panel' => PanelTypeEnum::class,
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->panel, [
            PanelTypeEnum::CHANNEL,
            PanelTypeEnum::SUPER_ADMIN
        ]);
    }
}
