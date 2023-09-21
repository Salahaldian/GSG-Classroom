<?php

namespace App\Http\Controllers;

use App\Models\Classwork;
use App\Models\ClassworkUser;
use App\Models\Submission;
use App\Rules\ForbiddenFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SubmissionController extends Controller
{
    public function store(Request $request, Classwork $classwork)
    {
        Gate::authorize('submission.create', [$classwork]);
        $request->validate([
            'files' => 'required|array',
            'files.*' => ['file', new ForbiddenFile('text/x-php', 'application/x-msdownload', 'application/x-httpd-php')],
        ]);
        $assigned = $classwork->users()->where('id', '=', Auth::id())->exists();
        if (!$assigned) {
            abort(404);
        }
        DB::beginTransaction();
        try {
            $data = [];
            foreach ($request->file('files') as $file) {
                $data[] = [
                    'user_id' => Auth::id(),
                    'classwork_id' => $classwork->id ,
                    'content' => $file->store("submission/{$classwork->id}"),
                    'type' => 'file',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $user = Auth::user();
            $user->submissions()->createMany($data);
            ClassworkUser::where([
                'user_id' => $user->id,
                'classwork_id' => $classwork->id ,
            ])->update([
                'status' => 'submitted',
                'submitted_at' => now()
            ]);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return back()
                ->with('error', $e->getMessage());
        }

        return back()
            ->with('success', 'Work submitted');
    }

    public function file(Submission $submission)
    {
        $user = Auth::user();
        DB::select('SELECT * FROM classroom_user
        WHERE user_id = ?
        AND role = teacher
        AND EXISTS (
            SELECT 1 FROM classworks WHERE classworks.classroom_id = classroom_user.classroom_id
            AND EXISTS ( sSELECT 1 from submtission where submtission.classwork_id = classwork_.id id = ? )
            )
        )' , [$user->id, 'teacher', $submission->id]);

        $isTeavher = $submission->classwork->classroom->teachers()->where('id', $user->id)->exists();
        $isOwner = $submission->user_id == $user->id;
        if(!$isTeavher && !$isOwner){
            abort(403);
        }
        // return Storage::disk('local')->download($submission->content);
        return response()->file(storage_path('app/' . $submission->content));
    }
}
