<x-main-layout title="Classrooms">
    <div class="container">
        <h1>{{ $classroom->name }} People</h1>
        <x-alert name="success" id="success" class="alert-success" />
        <x-alert name="error" id="error" class="alert-danger" />
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Name</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classroom->users()->orderBy('name')->get() as $user)
                    <tr>
                        <td></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->pivot->role }}</td>
                        <td>
                            <form action="{{ route('classrooms.people.destroy', $classroom->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-main-layout>
