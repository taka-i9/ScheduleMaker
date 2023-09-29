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
        
        if(!$request->has('list_status')) {
            $request->merge(['list_status' => 'normal', 'list_display_style' =>'from_now' ]);
        }

        $schedule_data = array();
        $data = Schedule::select(['id', 'name', 'begin_time', 'end_time'])->where('user_id', \Auth::user()->id)->where('status', $request->list_status);
        if($request->list_status == "normal") {
            if($request->list_display_style == 'from_now') {
                $now = date('Y-m-d H:i:s');
                $data = $data->where('begin_time', '>=', $now);
            }
            else if($request->list_display_style != 'all') {
                $begin = new \DateTimeImmutable($request->list_begin." 0:00:00");
                $end = new \DateTimeImmutable($request->list_end." 0:00:00");
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
        else if($request->list_status == "repetition") {

        }
        else if($request->list_status == "template") {

        }

        if(!$request->has('list_begin')) {
            $request->merge(['list_begin' => date('Y-m-d')]);
        }
        else {
            $request->list_begin = new \DateTimeImmutable($request->list_begin);
            $request->list_begin = $request->list_begin->format('Y-m-d');
        }
        if(!$request->has('list_end')) {
            $request->merge(['list_end' => date('Y-m-d')]);
        }
        else {
            $request->list_end = new \DateTimeImmutable($request->list_end);
            $request->list_end = $request->list_end->format('Y-m-d');
        }

        return view('scheduleListView', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'schedule_data' => $schedule_data, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end]);
    }

    public function detail(Request $request) {
        if(!$request->has('updated')) {
            $request->merge(['updated' => '']);
        }
        if($request->list_status == 'normal') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplication', 'color'])->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleDetailNormal', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'data' => $data, 'updated' => $request->updated]);
        }
        else if($request->list_status == 'repetition') {

        }
        else if($request->list_status == 'template') {

        }
    }

    public function edit(Request $request) {
        if($request->list_status == 'normal') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplication', 'color'])->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleEditNormal', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'data' => $data]);
        }
        else if($request->list_status == 'repetition') {

        }
        else if($request->list_status == 'template') {

        }
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

        if(!$request->has('template_name')) {
            $request->merge([ 'template_name' => '' ]);
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

        if($request->input('is_duplication') == NULL) $is_duplication = false;
        else $is_duplication = true;

        //新規登録の場合
        if(!$request->has('id')) {

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
                        'is_duplication' => $is_duplication,
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
                        'is_duplication' => $is_duplication,
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
                    'is_duplication' => $is_duplication,
                    'color' => $request->input('color'),
                ]);
            }

            return view('scheduleRegistrationComplete');
        }
        //更新の場合
        else {
            $content = Schedule::where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            if($request->list_status == 'normal') {
                $content->name = $request->name;
                $content->begin_time = $begin;
                $content->end_time = $end;
                $content->memo = $request->memo;
                $content->is_duplication = $is_duplication;
                $content->color = $request->color;
                $content->save();
                $request->merge(['updated' => true]);
                $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplication', 'color'])->where('id', $request->id)->first();
                $data = [
                    'id' => $data->id,
                    'name' => $data->name,
                    'begin_time' => $data->begin_time,
                    'end_time' => $data->end_time,
                    'memo' => $data->memo,
                    'is_duplication' => $data->is_duplication,
                    'color' => $data->color,
                ];
                return view('scheduleDetailNormal', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'data' => $data, 'updated' => $request->updated]);
            }
            else if($request->list_status == 'repetition') {

            }
            else if($request->list_status == 'template') {
    
            }
        }
    }
}
