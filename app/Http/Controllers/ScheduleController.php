<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    

    public function add(Request $request) {
        if($request->input('begin_date')!=NULL && $request->input('begin_time')!=NULL) {
            $request->merge([ 'begin' => $request->input('begin_date').' '.$request->input('begin_time').':00' ]);
        }
        else {
            $request->merge([ 'begin' => NULL ]);
        }

        if($request->input('end_date')!=NULL && $request->input('end_time')!=NULL) {
            $request->merge([ 'end' => $request->input('end_date').' '.$request->input('end_time').':00' ]);
        }
        else {
            $request->merge([ 'end' => NULL ]);
        }

        $begin = date_create($request->begin);
        $end = date_create($request->end);

        if($begin < $end) {
            $request->merge([ 'time_comparison' => true ]);
        }
        else {
            $request->merge([ 'time_comparison' => NULL ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'begin' => ['required'],
            'end' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
            'template_name' => ['nullable', 'string', 'max:255'],
            'time_comparison' => ['required']
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
            $repetation_state = 0;
        }

        if($request->input('is_duplecation') == NULL) $is_duplication = false;
        else $is_duplication = true;

        if($request->input("is_repetition") || $request->input("is_template")) {
            $elapsed_days = (int)date_create(date_format($begin, 'Y-m-d'))->diff(date_create(date_format($end, 'Y-m-d')))->format('%a');

            //繰り返しとして登録する場合
            if($request->input("is_repetition")) {
                Schedule::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'repetition',
                    'name' => $request->input('name'),
                    'begin_time' => $begin,
                    'end_time' => $end,
                    'repetition_state' => $repetition_state,
                    'elapsed_days' => $elapsed_days,
                    'memo' => $request->input('memo'),
                    'is_duplecation' => $is_duplication,
                    'color' => $request->input('color'),
                ]);
            }

            //テンプレートとして登録する場合
            if($request->input("is_template")) {
                Schedule::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'template',
                    'name' => $request->input('name'),
                    'begin_time' => $begin,
                    'end_time' => $end,
                    'repetition_state' => $repetition_state,
                    'elapsed_days' => $elapsed_days,
                    'memo' => $request->input('memo'),
                    'is_duplecation' => $is_duplication,
                    'color' => $request->input('color'),
                    'template_name' => $request->input('template_name'),
                ]);
            }
        }

        //通常のスケジュールとして登録する場合
        else {
            Schedule::create([
                'user_id' => \Auth::user()->id,
                'status' => 'normal',
                'name' => $request->input('name'),
                'begin_time' => $begin,
                'end_time' => $end,
                'repetition_state' => $repetition_state,
                'memo' => $request->input('memo'),
                'is_duplecation' => $is_duplication,
                'color' => $request->input('color'),
            ]);
        }

        return view('scheduleRegistrationComplete');
        
    }
}
