<x-main-layout :title="$classroom->name">
    <div class="container">
        <h1>{{ $classroom->name }} (#{{ $classroom->id }}) </h1>
        <h3>Create Classwork</h3>
        <hr>
        <form action="{{ route('classrooms.classworks.store', [$classroom->id, 'type' => $type]) }}" method="post">
            @csrf
            @include('classworks._form')
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
</x-main-layout>
