<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkFlow;

class WorkFlowController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function add(Request $request) {
        if($request->input('deadline_date')!=NULL && $request->input('deadline_time')!=NULL) {
            $request->merge([ 'deadline' => $request->input('deadline_date').' '.$request->input('deadline_time').':00' ]);
        }
        else {
            $request->merge([ 'deadline' => NULL ]);
        }

        $deadline = date_create($request->deadline);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
        ]);

        $result = WorkFlow::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->input('name'),
            'deadline' => $deadline,
            'memo' => $request->input('memo'),
            'color' => $request->input('color'),
        ]);

        return view('workflowRegistrationComplete', ['workflow_id' => $result->id]);

    }

    public function edit_form(Request $request) {
        return view('workflowEditForm', ['workflow_id' => $request->input('workflow_id')]);
    }
}
