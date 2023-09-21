<?php

namespace App\Listeners;

use App\Events\ClassworkCreated;
use App\Jobs\SendClassroomNotification;
use App\Models\User;
use App\Notifications\NewClassworkNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNotificationToAssignedStudents
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ClassworkCreated $event): void
    {
        // foreach ($event->classwork->users as $user) {
        //     $user->notify(new NewClassworkNotification($event->classwork));
        // }
        $classwork = $event->classwork;
        $job = new SendClassroomNotification(
            $classwork->users,
            new NewClassworkNotification($classwork));
        $job->onQueue('y');
        dispatch($job)->onQueue('z');
        // SendClassroomNotification::dispatch($classwork->users, new NewClassworkNotification($classwork));
    }
}
