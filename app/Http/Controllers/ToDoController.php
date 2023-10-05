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
        if($request->deadline_date != NULL && $request->deadline_time != NULL) {
            $request->merge([ 'deadline' => $request->deadline_date.' '.$request->deadline_time.':00' ]);
        }
        else if($request->status != 'normal') {
            $request->merge([ 'deadline' => true ]);
        }
        else {
            $request->merge([ 'deadline' => NULL ]);
        }

        $deadline = date_create($request->deadline);

        if($request->repetition_deadline_time != NULL) {
            $request->merge([ 'repetition_deadline' => $request->repetition_deadline_time.':00' ]);
        }
        else if($request->status == 'normal') {
            $request->merge([ 'repetition_deadline' => true ]);
        }
        else {
            $request->merge([ 'repetition_deadline' => NULL ]);
        }

        $repetition_deadline = date_create($request->repetition_deadline);

        if($request->is_today) {
            $request->merge([ 'type' => 'today' ]);
        }
        else {
            $request->merge([ 'type' => 'deadline' ]);
        }

        if($request->required_hour != NULL && $request->required_minute != NULL) {
            $request->merge([ 'required_time' => true ]);
            $required_minutes = (int)$request->required_hour * 60 + (int)$request->required_minute;
        }

        $repetition_state = "";
        $days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        foreach($days as $day) {
            if($request->input('repetition_'.$day)) {
                $repetition_state .= "1";
            }
            else $repetition_state .= "0";
        }
        if($request->status != 'repetition') {
            $repetition_state = "0000000";
        }
        if($request->repetition_everyday) {
            $repetition_state = "1111111";
        }

        if($request->status == 'repetition' && $repetition_state == "0000000") {
            $request->merge([ 'repetition_setting' => NULL ]);
        }
        else {
            $request->merge([ 'repetition_setting' => true ]);
        }

        if($request->status != 'template') {
            $request->merge([ 'template_name' => '1' ]);
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['required'],
            'repetition_deadline' => ['required'],
            'required_time' => ['required'],
            'repetition_setting' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
            'template_name' => ['required', 'string', 'max:255'],
        ]);

        //新規登録する場合
        if(!$request->has('id')) {

            //通常のスケジュールとして登録する場合
            if($request->status == 'normal') {
                ToDo::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'normal',
                    'type' => $request->type,
                    'name' => $request->name,
                    'deadline' => $deadline,
                    'required_minutes' => $required_minutes,
                    'rest_minutes' => $required_minutes,
                    'memo' => $request->memo,
                    'priority_level' => (int)$request->priority_level,
                    'color' => $request->color,
                ]);
            }
            
            //繰り返しとして登録する場合
            else if($request->status == 'repetition') {
                ToDo::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'repetition',
                    'type' => $request->type,
                    'name' => $request->name,
                    'deadline' => $repetition_deadline,
                    'required_minutes' => $required_minutes,
                    'rest_minutes' => $required_minutes,
                    'repetition_state' => $repetition_state,
                    'memo' => $request->memo,
                    'priority_level' => (int)$request->priority_level,
                    'color' => $request->color,
                ]);
            }

            //テンプレートとして登録する場合
            else if($request->status == 'template') {
                ToDo::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'template',
                    'type' => $request->type,
                    'name' => $request->name,
                    'deadline' => $repetition_deadline,
                    'required_minutes' => $required_minutes,
                    'rest_minutes' => $required_minutes,
                    'memo' => $request->memo,
                    'priority_level' => (int)$request->priority_level,
                    'color' => $request->color,
                    'template_name' => $request->template_name,
                ]);
            }
        }

        return view('todoRegistrationComplete');

    }
}
