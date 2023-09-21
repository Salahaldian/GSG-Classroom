<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Notifications
        @if ($unreadCount)
        <span class="badge bg-primary"> {{ $unreadCount }} </span>
        @endif
    </a>
    <ul class="dropdown-menu">
        @foreach ($notifications as $notification)
            <li><a class="dropdown-item" href="{{ $notification->data['link'] }}?nid={{ $notification->id}}">
                @if ($notification->unread()) <b>*</b> @endif
                {{ $notification->data['body'] }}
                <br>
                <small class="text-muted"> {{ $notification->created_at->diffForHumans() }} </small>
            </a></li>
        @endforeach
    </ul>
</li>
