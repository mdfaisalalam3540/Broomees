<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'username',
        'password',
        'age',
        'reputation_score',
        'version'
    ];

    protected $casts = [
        'id' => 'string',
        'reputation_score' => 'decimal:2',
        'version' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    // Friends where this user initiated
    public function friends()
    {
        return $this->belongsToMany(
            User::class,
            'relationships',
            'user_id',
            'friend_id'
        )->withTimestamps();
    }

    // Friends who initiated with this user
    public function friendOf()
    {
        return $this->belongsToMany(
            User::class,
            'relationships',
            'friend_id',
            'user_id'
        )->withTimestamps();
    }

    // All friends (both directions)
    public function allFriends()
    {
        return $this->friends->merge($this->friendOf);
    }

    // Hobbies
    public function hobbies(): BelongsToMany
    {
        return $this->belongsToMany(Hobby::class)
            ->withTimestamps();
    }

    // API Tokens
    public function apiTokens(): HasMany
    {
        return $this->hasMany(ApiToken::class);
    }

}