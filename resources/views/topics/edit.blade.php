@include('partials/header');

<h1 style="margin-left: 7%;">Update Topic</h1>
<form action=" {{ route('topic.update' , $topic->id) }}" method="post" style="width: 70%;  margin-left:7% ">
    {{ csrf_field() }}
    @method('put')

    <div class="form-floating mb-3">
        <input type="text" class="form-control" value=" {{ $topic->name }}" name="name" id="name" placeholder="topic name">
        <label for="name">Class name</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" value=" {{ $topic->classroom_id }}" name="classroom_id" id="classroom_id" placeholder="classroom ID">
        <label for="classroom_id">Classroom ID</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control" value=" {{ $topic->user_id }}" name="user_id" id="user_id" placeholder="User ID">
        <label for="user_id">user ID</label>
    </div>
    </div>
    <button type="submit" name="create" class="btn btn-primary">Update Room</button>
</form>
</body>

</html>