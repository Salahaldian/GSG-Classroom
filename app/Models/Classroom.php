<?php

namespace App\Models;

use App\Models\Scopes\UserClassroomScope;
use App\Observers\ClassroomObserver;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class Classroom extends Model
{
    use HasFactory , SoftDeletes;

    public static string $disk = 'public';

    protected $fillable = [
        'name', 'section', 'subject', 'room',
        'theme', 'cover_image_path', 'code',
        'user_id'
    ];

    protected $appends = [
        'cover_image_url',
    ];

    protected $hidden = [
        'cover_image_path',
        'deleted_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope(new UserClassroomScope);
        // Creating, Created, Updating, Updated, Saving, Saved, Deleting, Deleted, Restoring, Restored, ForceDeleting, ForceDeleted, Retrived
        static::observe(ClassroomObserver::class); // or in Providers/EventService/boot or like a protected
    }

    public function classworks(): HasMany
    {
        return $this->hasMany(Classwork::class, 'classroom_id', 'id');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class, 'classroom_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class, // Related model
            'classroom_user', // Pivot table
            'classroom_id', // Fk for current model in the pivot table
            'user_id', // Fk for related model in the pivot table
            'id', // Pk for current model
            'id' // Pk for related model
        )->withPivot(['role', 'created_at']);
    }

    public function teachers()
    {
        return $this->users()->wherePivot('role', '=', 'teacher');
    }

    public function students()
    {
        return $this->users()->wherePivot('role', '=', 'student');
    }

    public function streams()
    {
        return $this->hasMany(Stream::class)->latest();
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'recipient');
    }

    public function getRouteKeyName()
    {
        return "id";
    }

    public static function uploadCoverImage($file)
    {
        $path = $file->store('/covers', [
            'disk' => static::$disk,
        ]);
        return $path;
    }

    public static function deleteCoverImage($path)
    {
        if($path && Storage::disk(Classroom::$disk)->exists($path)){
            return Storage::disk(Classroom::$disk)->delete($path);
        }
    }

    // Local Scopes
    public function scopeActive(Builder $query)
    {
        $query->where('status', '=', 'active');
    }

    public function scopeRecent(Builder $query)
    {
        $query->orderByDesc('updated_at', 'DESC');
    }

    public function scopeStatus(Builder $query, $status = 'active')
    {
        $query->where('status', '=', $status);
    }

    // -----------------------------------------------------
    public function join($user_id, $role = 'student')
    {
        $exists = $this->users()->where('user_id', '=', $user_id)->exists();
        if($exists) {
            throw new Exception('User alrady joined the classroom');
        }
        return $this->users()->attach($user_id, [
            'role' => $role,
            'created_at' => now()
        ]); // insert
    }

    public function getNameAttribute($value)
    {
        return strtoupper($value);
    }

    // $classroom->cover_image_url
    public function getCoverImageUrlAttribute()
    {
        if($this->cover_image_path){
            return Storage::disk(static::$disk)->url($this->cover_image_path);
        }
        return 'https://placehold.co/800x300';
    }

    public function getUrlAttribute()
    {
        return route('classrooms.show', $this->id);
    }

}
