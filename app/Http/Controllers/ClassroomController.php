<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassroomRequest;
use App\Models\Classroom;
use App\Models\Scopes\UserClassroomScope;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function __construct()
    {
        $this->middleware('subscribed')->only('create','store');
        $this->authorizeResource(Classroom::class, 'classroom');
    }

    public function index(Request $request): Renderable
    {
        // $this->authorize('view-any', Classroom::class);
        $classrooms = Classroom::active()
            ->recent()
            ->orderBy('created_at', 'DESC')
            ->get();
        $success = session('success');
        return view('classrooms.index', compact('classrooms','success'));
    }

    public function create()
    {
        return view('classrooms.create', [
            'classroom' => new Classroom(),
        ]);
    }

    public function store(ClassroomRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $path = Classroom::uploadCoverImage($file);
            $validated['cover_image_path'] = $path;
        }
        // $validated['code'] = Str::random(8); // done make in classroom observer and use it in booted function
        // $validated['user_id'] = Auth::id(); // done make in classroom observer and use it in booted function
        DB::beginTransaction();
        try {
            $classroom = Classroom::create($validated);
            $classroom->join(Auth::id(), 'teacher');
            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
        return redirect()->route('classrooms.index')
            ->with('success', 'Classrooms created');
    }

    public function show(Classroom $classroom)
    {
        $invitation_link = URL::signedRoute('classrooms.join', [
            'classroom' => $classroom->id,
            'code' => $classroom->code
        ]);
        return View::make('classrooms.show')
            ->with([
                'classroom'=> $classroom,
                'invitation_link' => $invitation_link,
            ]);
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', [
            'classroom'=> $classroom,
        ]);
    }

    public function update(ClassroomRequest $request, Classroom $classroom)
    {
        $validated = $request->validated();
        if($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $path = Classroom::uploadCoverImage($file);
            $validated['cover_image_path'] = $path;
        }
        $old = $classroom->cover_image_path;
        $classroom->update( $validated );
        if($old && $old != $classroom->cover_image_path) {
            Classroom::deleteCoverImage($old);
        }

        Session::flash('success', 'Classrooms updated');
        Session::flash('error', 'Test for error');
        return Redirect::route('classrooms.index');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect( route('classrooms.index') )
            ->with('success', 'Classrooms deleted');
    }

    public function trashed()
    {
        $classrooms = Classroom::onlyTrashed()
            ->latest('deleted_at')
            ->get();
        return view('classrooms.trashed', compact('classrooms'));
    }

    public function restore($id)
    {
        $classroom = Classroom::onlyTrashed()->findOrFail($id);
        $classroom->restore();
        return redirect()
            ->route('classrooms.index')
            ->with('success', "Classrooms ({$classroom->name}) restored");
    }

    public function forceDelete($id)
    {
        $classroom = Classroom::withTrashed()->findOrFail($id);
        $classroom->forceDelete();
        // Classroom::deleteCoverImage($classroom->cover_image_path); // done make in classroom model in booted function
        return redirect()
            ->route('classrooms.trashed')
            ->with('success', "Classrooms ({$classroom->name}) deleted forever!");
    }

    public function chat(Classroom $classroom)
    {
        return view('classrooms.chat', [
            'classroom' => $classroom
        ]);
    }
}
