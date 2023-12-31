<x-main-layout :title="$classroom->name">
    <div class="container">
        <h1>{{ $classroom->name }} (#{{ $classroom->id }}) </h1>
        <h3>{{ $classwork->title }}</h3>
        <x-alert name="success" id="success" class="alert-success" />
        <x-alert name="error" id="success" class="alert-danger" />
        <hr>
        <div class="row">
            <div class="col-md-8">
                <div>
                    <p>{!! $classwork->description !!}</p>
                </div>
                <h4>Comments</h4>
                <form action="{{ route('comments.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $classwork->id }}">
                    <input type="hidden" name="type" value="classwork">
                    <div class="d-flex">
                        <div class="col-8">
                            <x-form.floating-control  name="description">
                                <x-slot:label>
                                    <label for="description">Comment</label>
                                </x-slot:label>
                                <x-form.textarea name="content" placeholder="Comment"/>
                            </x-form.floating-control>
                        </div>
                        <div class="ms-1">
                            <button type="submit" class="btn btn-primary">Create Comment</button>
                        </div>
                    </div>
                </form>
                <div class="mt-4">
                    @foreach ($classwork->comments as $comment)
                        <div class="row">
                            <div class="col-md-2">
                                <img src="https://ui-avatars.com/api/?name={{$comment->user->name}}&background=random" class="rounded-circle">
                            </div>
                            <div class="col-md-10" style="margin-left: -70px">
                                <p>By: {{$comment->user->name}}. Time: {{$comment->created_at->diffForHumans() }}</p>
                                <p>{{ $comment->content}}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-4">
                @can('submissions.create', [$classwork])
                <div class="borderd rounded p-3 pg-light">
                    <h4>Submission</h4>
                    @if ($submissions->count())
                        <ul>
                            @foreach ($submissions as $i => $submission)
                                <li> <a href="{{ route('submissions.file', $submission->id) }}">File #{{ $i }}</a> </li>
                            @endforeach
                        </ul>
                    @else
                    <form action="{{ route('submissions.store', $classwork->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <x-form.floating-control  name="files.0">
                            <x-slot:label>
                                <label for="files">Upload Files</label>
                            </x-slot:label>
                            <x-form.input type="file" name="files[]" multiple accept="image/*,application/pdf" placeholder="Select Files"/>
                        </x-form.floating-control>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    @endif
                </div>
                @endcan
            </div>
        </div>
    </div>
</x-main-layout>
