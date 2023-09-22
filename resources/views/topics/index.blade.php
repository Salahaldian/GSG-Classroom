@include('partials.header')
<div class="container">
    <div class="row">
        @if ($success)
            <div class="alert alert-success">
                {{ $success }}
            </div>
        @endif
        @foreach ($topics as $topic)
            <div class="col-md-3">
                <div class="card">
                    <img src="" class="card-img-top" alt="">
                    <div class="card-body">
                        <h5 class="card-title">{{ $topic->name }}</h5>
                        <p class="card-title">{{ $topic->subject }}</p>
                        <p class="card-title">Classroom: {{ optional($topic->classroom)->name }}</p>
                        <a href=" {{ route('topic.edit', $topic->id) }}" class="btn btn-primary">Edit</a>
                        <form method="post" action="{{ route('topic.destroy', $topic->id) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
