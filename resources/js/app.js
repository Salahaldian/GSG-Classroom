import Echo from 'laravel-echo';
import './bootstrap';

// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

if (classsroomId) {
    Echo.private('classroom.' + classroomId)
    .listen('.classwork-created', function(event) {
        alert(event.title);
    });
}
Echo.private('Notifications.' + userId)
    .notification(function (event) {
        alert(event.body);
    });
