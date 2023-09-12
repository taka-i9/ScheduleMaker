<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToDo;

class ToDoController extends Controller
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

        if($request->input('is_today')) {
            $request->merge([ 'type' => 'today' ]);
        }
        else {
            $request->merge([ 'type' => 'deadline' ]);
        }

        if($request->input('required_hour')!=NULL && $request->input('required_minute')!=NULL) {
            $request->merge([ 'required_time' => true ]);
            $required_minutes = (int)$request->input('required_hour') * 60 + (int)$request->input('required_minute');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['required'],
            'required_time' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
            'template_name' => ['nullable', 'string', 'max:255'],
        ]);

        $repetition_state = 0;
        $days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        foreach($days as $day) {
            $repetition_state *= 2;
            if($request->input('repetition_'.$day)) {
                $repetition_state++;
            }
        }
        if(!$request->input("is_repetition")) {
            $repetition_state = 0;
        }
        if($request->input("repetition_everyday")) {
            $repetition_state = 127;
        }

        if($request->input("is_repetition") || $request->input("is_template")) {
            
            //繰り返しとして登録する場合
            if($request->input("is_repetition")) {
                ToDo::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'repetition',
                    'type' => $request->input('type'),
                    'name' => $request->input('name'),
                    'deadline' => $deadline,
                    'required_minutes' => $required_minutes,
                    'rest_minutes' => $required_minutes,
                    'repetition_state' => $repetition_state,
                    'memo' => $request->input('memo'),
                    'priority_level' => (int)$request->input('priority_level'),
                    'color' => $request->input('color'),
                ]);
            }

            //テンプレートとして登録する場合
            if($request->input("is_template")) {
                ToDo::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'template',
                    'type' => $request->input('type'),
                    'name' => $request->input('name'),
                    'deadline' => $deadline,
                    'required_minutes' => $required_minutes,
                    'rest_minutes' => $required_minutes,
                    'repetition_state' => $repetition_state,
                    'memo' => $request->input('memo'),
                    'priority_level' => (int)$request->input('priority_level'),
                    'color' => $request->input('color'),
                    'template_name' => $request->input('template_name'),
                ]);
            }
        }

        //通常のスケジュールとして登録する場合
        else {
            ToDo::create([
                'user_id' => \Auth::user()->id,
                'status' => 'normal',
                'type' => $request->input('type'),
                'name' => $request->input('name'),
                'deadline' => $deadline,
                'required_minutes' => $required_minutes,
                'rest_minutes' => $required_minutes,
                'repetition_state' => $repetition_state,
                'memo' => $request->input('memo'),
                'priority_level' => (int)$request->input('priority_level'),
                'color' => $request->input('color'),
            ]);
        }

        return view('todoRegistrationComplete');

    }
}
