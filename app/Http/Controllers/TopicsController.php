<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::all()->where('user_id',Auth::id());
        $success = session('success');
        return view('topics.index', compact('topics', 'success'));
    }

    public function create()
    {
        $classrooms = Classroom::all();
        return view('topics.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $topic = new Topic();
        $topic->name = $request->post('name');
        $topic->classroom_id = $request->post('classroom_id');
        $topic->user_id = $request->post('user_id');
        $topic->save();

        return redirect(route('topic.index'));
    }
    public function edit($id)
    {
        $topic = Topic::find($id);
        return view('topics.edit', compact('topic'));
    }
    public function update(Request $request, $id)
    {
        $topic = Topic::find($id);
        $topic->name = $request->post('name');
        $topic->classroom_id = $request->post('classroom_id');
        $topic->user_id = $request->post('user_id');
        $topic->save();
        return redirect(route('topic.index'));
    }

    public function destroy($id)
    {
        Topic::destroy($id);
        return redirect(route('topic.index'));
    }
}
