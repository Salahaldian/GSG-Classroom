<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name', 'classroom_id', 'user_id'
    ];

    public function classworks(): HasMany
    {
        return $this->hasMany(Classwork::class, 'topic_id', 'id');
    }
}
