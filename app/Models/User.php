<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'age',
        'gender',
        'phone',
        'alamat',
        'spesialis',
        'no_hp',
        'is_active',
        'status_dokter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'average_rating',
        'review_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAverageRatingAttribute()
    {
        if ($this->role !== 'dokter') {
            return 0;
        }
        return \App\Models\Konsultasi::where('dokter_id', $this->id)
            ->where('is_rated', true)
            ->avg('rating') ?: 0;
    }

    public function getReviewCountAttribute()
    {
        if ($this->role !== 'dokter') {
            return 0;
        }
        return \App\Models\Konsultasi::where('dokter_id', $this->id)
            ->where('is_rated', true)
            ->count();
    }

    public function getRatingReviewsAttribute()
    {
        if ($this->role !== 'dokter') {
            return collect();
        }
        return \App\Models\Konsultasi::where('dokter_id', $this->id)
            ->where('is_rated', true)
            ->whereNotNull('ulasan')
            ->where('ulasan', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}
