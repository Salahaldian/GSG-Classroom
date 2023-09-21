<x-main-layout title="{{__('Classrooms')}}">
    <div class="container">
        <h1>{{__('Classrooms')}}</h1>
        <x-alert name="success" id="success" class="alert-success" />
        <x-alert name="error" id="error" class="alert-danger" />

        <ul id="classrooms"></ul>

        <div class="d-flex">
            <form action="{{ route('classrooms.create') }}" method="get">
                @csrf
                <button type="submit" class="btn btn-sm btn-success mb-3">{{ __('Create')}}</button>
            </form>
            <div class="mx-2"></div>
            <form action="{{ route('classrooms.trashed') }}" method="get">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger mb-3">{{__('Trashed')}}</button>
            </form>
        </div>
        <div class="row">
            @foreach ($classrooms as $classroom)
                <div class="col-md-3">
                    <div class="card">
                            <img src="{{$classroom->cover_image_url}}" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title">{{ $classroom->name }}</h5>
                            <p class="card-text">{{ $classroom->section }} - {{ $classroom->room }}</p>
                            <div class="d-flex justify-content-between">
                                <a href="{{ $classroom->url }}" class="btn btn-sm btn-primary">{{__('View')}}</a>
                                <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-sm btn-dark">{{__('Edit')}}</a>
                                <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="btn btn-sm btn-danger">{{__('Delete')}}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        </div>

    @push('scripts')
        <script>
            fetch('/api/v1/classrooms')
                .then(res => res.json())
                .then(json => {
                    let ul = document.getElementById('classrooms');
                    for (let i in json.data) {
                        ul.innerHTML += `<li>${json.data[i].name}</li>`
                    }
                })
        </script>
    @endpush
</x-main-layout>

