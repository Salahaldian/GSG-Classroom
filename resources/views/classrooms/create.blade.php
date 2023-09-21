@extends('layouts.master')

    @section('title', 'Create Classrooms')

    @section('content')
        <div class="container">
            <h1>Create Classroom</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('classrooms.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @include('classrooms._form', [
                    'button_label' => 'Create Room'
                ])
            </form>
        </div>
    @endsection
