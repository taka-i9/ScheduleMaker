<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function list(Request $request) {
        
        if(!$request->has('status')) {
            $request->merge(['status' => 'normal', 'display_style' =>'from_now' ]);
        }

        $schedule_data = array();
        $data = Schedule::select(['id', 'name', 'begin_time', 'end_time'])->where('user_id', \Auth::user()->id)->where('status', $request->status);
        if($request->status == "normal") {
            if($request->display_style == 'from_now') {
                $now = date('Y-m-d H:i:s');
                $data = $data->where('begin_time', '>=', $now);
            }
            else if($request->display_style != 'all') {
                $begin = new \DateTimeImmutable($request->begin." 0:00:00");
                $end = new \DateTimeImmutable($request->end." 0:00:00");
                $end = $end->modify("+1 day");
                $data = $data->where('begin_time', '>=', $begin)->where('begin_time', '<', $end);
            }
            $data = $data->orderBy('begin_time')->get();

            foreach($data as $value) {
                array_push($schedule_data, [
                    'id' => $value->id,
                    'name' => $value->name,
                    'begin_time' => $value->begin_time,
                    'end_time' => $value->end_time,
                ]);
            }
        }
        else if($request->status == "repetition") {

        }
        else if($request->status == "template") {

        }

        if(!$request->has('begin')) {
            $request->merge(['begin' => date('Y-m-d')]);
        }
        else {
            $request->begin = new \DateTimeImmutable($request->begin);
            $request->begin = $request->begin->format('Y-m-d');
        }
        if(!$request->has('end')) {
            $request->merge(['end' => date('Y-m-d')]);
        }
        else {
            $request->end = new \DateTimeImmutable($request->end);
            $request->end = $request->end->format('Y-m-d');
        }

        return view('scheduleListView', ['status' => $request->status, 'display_style' => $request->display_style, 'schedule_data' => $schedule_data, 'begin' => $request->begin, 'end' => $request->end]);
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
            $repetition_state = 0;
        }
        if($request->input("repetition_everyday")) {
            $repetition_state = 127;
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

    public function detail(Request $request) {
        if($request->status == 'normal') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplecation', 'color'])->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'memo' => $data->memo,
                'is_duplecation' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleDetailNormal', ['status' => $request->status, 'display_style' => $request->display_style, 'begin' => $request->begin, 'end' => $request->end, 'data' => $data]);
        }
        else if($request->status == 'repetition') {

        }
        else if($request->status == 'template') {

        }
    }
}
