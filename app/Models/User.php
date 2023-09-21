<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, HasLocalePreference
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // protected function email(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => strtoupper($value),
    //         set: fn ($value) => strtolower($value)
    //     );
    // }

    public function classrooms()
    {
        return $this->belongsToMany(
            Classroom::class, // Related model
            'classroom_user', // Pivot table
            'user_id', // Fk for current model in the pivot table
            'classroom_id', // Fk for related model in the pivot table
            'id', // Pk for current model
            'id' // Pk for related model
        )->withPivot(['role', 'created_at']);
    }

    // علاقة لمعرفة الكلاس رووم الي انا انشئتها
    public function createdClassrooms()
    {
        return $this->hasMany(Classroom::class, 'user_id');
    }

    public function classworks()
    {
        return $this->belongsToMany(Classwork::class)
            ->using(ClassworkUser::class)
            ->withPivot(['grade', 'status', 'submitted_at', 'created_at']);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id')
            ->withDefault();
    }

    public function subscriptions()
    {
        return $this->hasOne(Subscription::class);
    }

    public function receivedMessages()
    {
        return $this->morphMany(Message::class, 'recipient');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    public function receivesBroadcastNotificationsOn()
    {
        return 'Notifications.' . $this->id;
    }

    public function preferredLocale()
    {
        return $this->profile->locale;
    }

    public function routeNotificationForVonage($notification = null)
    {
        return '970562565171';
    }

    public function routeNotificationForHadara($notification = null)
    {
        return '970562565171';
    }

}
