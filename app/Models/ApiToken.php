<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'name',
        'abilities',
        'expires_at'
    ];

    protected $casts = [
        'abilities' => 'array',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime'
    ];

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}