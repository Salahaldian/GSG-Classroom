<?php

namespace App\Models;

use App\Concems\HasPrice;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class Subscription extends Model
{
    use HasFactory, HasPrice, Prunable;

    protected $fillable = [
        'plan_id', 'user_id', 'price', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::whereData('expires_at', '<=', now()->subYear());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
