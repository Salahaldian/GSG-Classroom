<?php

namespace App\Http\Controllers;

use App\Enums\classworkType;
use App\Events\ClassworkCreated;
use App\Models\Classroom;
use App\Models\Classwork;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ClassworkController extends Controller
{
    protected function getType(Request $request)
    {
        try {
            return ClassworkType::from($request->query('type'));
        } catch (Error $e) {
            return Classwork::TYPE_ASSIGNMENT;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Classroom $classroom)
    {
        $this->authorize('view-any', [Classwork::class, $classroom]);
        $classworks = $classroom->classworks()
            ->with('topic') // Eager load
            ->withCount([
                'users',
                'users as assigned_count' => function($query) {
                    $query->where('classwork_user.status', '=', 'submitted');
                },
                'users as graded_count' => function($query) {
                    $query->whereNotNull('classwork_user.grade');
                },
            ])
            ->filter($request->query())
            ->latest()
            ->where(function($query) {
                $query->whereHas('users', function ($query) {
                    $query->where('id', '=', Auth::id());
                })
                ->orWhereHas('classroom.teachers', function ($query) {
                    $query->where('id', '=', Auth::id());
                });
            })
            ->paginate(15);
        return view('classworks.index', [
            'classroom' => $classroom,
            'classworks'=> $classworks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Classroom $classroom)
    {
        $this->authorize('create', [Classwork::class, $classroom]);
        $type = $this->getType($request)->value;
        $classwork = new Classwork();
        return view('classworks.create', compact('classroom', 'classwork', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom)
    {
        $this->authorize('create', [Classwork::class, $classroom]);
        $type = $this->getType($request);
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable','string'] ,
            'topic_id' => ['nullable','int', 'exists:topics,id'],
            'options.grade' => [Rule::requiredIf(fn() => $type == 'assignment'), 'nullable', 'numeric', 'min:0'],
            'options.due' => ['nullable', 'date', 'after:published_at'],
        ]);
        $request->merge([
            'user_id' =>  Auth::id(),
            'type' => $type->value,
            // 'classroom_id' => $classroom->id, // have a value by default when use relation
        ]);
        try {
            DB::transaction(function () use ($classroom, $request, $type) {
                $classwork = $classroom->classworks()->create($request->all());
                $classwork->users()->attach($request->input('students'));
                // event(new ClassworkCreated($classwork));
                ClassworkCreated::dispatch($classwork);
            });
        } catch (\Exception $e){
            return back()
                ->with('error', $e->getMessage());
        }
        return redirect()
            ->route('classrooms.classworks.index', $classroom->id)
            ->with('success', __('Classwork created!'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom, Classwork $classwork)
    {
        $this->authorize('view', $classwork);
        // Gate::authorize('classworks.view', [$classwork]);
        $submissions = Auth::user()->submissions()->where('classwork_id', $classwork->id)->get();
        return view('classworks.show', compact('classroom', 'classwork', 'submissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Classroom $classroom, Classwork $classwork)
    {
        $this->authorize('update', $classwork);
        $type = $classwork->type->value;
        $assigned = $classwork->users()->pluck('id')->toArray();
        return view('classworks.edit', compact('classroom', 'classwork', 'type', 'assigned'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom, Classwork $classwork)
    {
        $this->authorize('update', $classwork);
        $type = $classwork->type;
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable','string'] ,
            'topic_id' => ['nullable','int', 'exists:topics,id'],
            'options.grade' => [Rule::requiredIf(fn() => $type == 'assignment'), 'nullable', 'numeric', 'min:0'],
            'options.due' => ['nullable', 'date', 'after:published_at'],
        ]);
        $classwork->update( $request->all() );
        $classwork->users()->sync( $request->input('students') );
        return back()
            ->with('success', __('Classwork updated!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom, Classwork $classwork)
    {
        $this->authorize('delete', $classwork);
    }
}
