<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    function getScheduleData($list_status, $list_display_style, $list_begin, $list_end, $list_repetition) {
        $schedule_data = array();
        $data = '';
        if($list_status == "normal") {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
            if($list_display_style == 'from_now') {
                $now = date('Y-m-d H:i:s');
                $data = $data->where('begin_time', '>=', $now);
            }
            else if($list_display_style != 'all') {
                $begin = new \DateTimeImmutable($list_begin." 0:00:00");
                $end = new \DateTimeImmutable($list_end." 0:00:00");
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
        else if($list_status == "repetition") {
            $data = Schedule::select(['id', 'name', 'repetition_state'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
            $isFirst = true;
            for($i = 0; $i < strlen($list_repetition); $i++) {
                if(substr($list_repetition, $i, 1) == '1') {
                    $pattern = str_repeat('_', $i).'1'.str_repeat('_', strlen($list_repetition) - $i -1);
                    if($isFirst) {
                        $data->where('repetition_state', 'like', $pattern);
                        $isFirst = false;
                    }
                    else {
                        $data->orWhere('repetition_state', 'like', $pattern);
                    }
                }
            }
            $data = $data->orderBy('repetition_state')->get();

            foreach($data as $value) {
                array_push($schedule_data, [
                    'id' => $value->id,
                    'name' => $value->name,
                    'repetition' => $value->repetition_state,
                ]);
            }
        }
        else if($list_status == "template") {
            $data = Schedule::select(['id', 'template_name', 'repetition_state'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
            $data = $data->orderBy('created_at')->get();

            foreach($data as $value) {
                array_push($schedule_data, [
                    'id' => $value->id,
                    'name' => $value->template_name,
                ]);
            }
        }
        return $schedule_data;
    }

    public function new(Request $request) {
        return view('scheduleRegistrationForm');
    }

    public function list(Request $request) {
        
        if(!$request->has('list_status')) {
            $request->merge(['list_status' => 'normal', 'list_display_style' =>'from_now' ]);
        }
        if(!$request->has('deleted')) {
            $request->merge(['deleted' => '']);
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
        if(!$request->has('list_repetition')) {
            $request->merge(['list_repetition' => '0000000']);
        }

        if($request->list_display_style == 'this_week') {
            $request->list_begin = new \DateTimeImmutable($request->list_begin);
            $request->list_end = new \DateTimeImmutable($request->list_end);
            $request->list_begin = $request->list_begin->modify("-".strval(date('w'))." day");
            $request->list_end = $request->list_end->modify("+".strval(6 - date('w'))." day");
            $request->list_begin = $request->list_begin->format('Y-m-d');
            $request->list_end = $request->list_end->format('Y-m-d');
        }
        else if($request->list_display_style == 'this_month') {
            $request->list_begin = new \DateTimeImmutable($request->list_begin);
            $request->list_end = new \DateTimeImmutable($request->list_end);
            $request->list_begin = $request->list_begin->modify("-".strval(date('d') - 1)." day");
            $request->list_end = $request->list_begin->modify("+1 month")->modify("-1 day");
            $request->list_begin = $request->list_begin->format('Y-m-d');
            $request->list_end = $request->list_end->format('Y-m-d');
        }

        $schedule_data = self::getScheduleData($request->list_status, $request->list_display_style, $request->list_begin, $request->list_end, $request->list_repetition);

        return view('scheduleListView', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'schedule_data' => $schedule_data, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'deleted' => $request->deleted]);
    }

    public function detail(Request $request) {
        if(!$request->has('updated')) {
            $request->merge(['updated' => '']);
        }
        if($request->list_status == 'normal') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplication', 'color'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleDetailNormal', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data, 'updated' => $request->updated]);
        }
        else if($request->list_status == 'repetition') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'repetition_state', 'elapsed_days', 'memo', 'is_duplication', 'color'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'repetition' => $data->repetition_state,
                'elapsed_days' => $data->elapsed_days,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleDetailRepetition', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data, 'updated' => $request->updated]);
        }
        else if($request->list_status == 'template') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'elapsed_days', 'memo', 'is_duplication', 'color', 'template_name'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'elapsed_days' => $data->elapsed_days,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
                'template_name' => $data->template_name,
            ];
            return view('scheduleDetailTemplate', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data, 'updated' => $request->updated]);
        }
    }

    public function edit(Request $request) {
        if($request->list_status == 'normal') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'memo', 'is_duplication', 'color'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleEditNormal', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data]);
        }
        else if($request->list_status == 'repetition') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'repetition_state', 'elapsed_days', 'memo', 'is_duplication', 'color'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'repetition' => $data->repetition_state,
                'elapsed_days' => $data->elapsed_days,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
            ];
            return view('scheduleEditRepetition', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data]);
        }
        else if($request->list_status == 'template') {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'elapsed_days', 'memo', 'is_duplication', 'color', 'template_name'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            $data = [
                'id' => $data->id,
                'name' => $data->name,
                'begin_time' => $data->begin_time,
                'end_time' => $data->end_time,
                'elapsed_days' => $data->elapsed_days,
                'memo' => $data->memo,
                'is_duplication' => $data->is_duplication,
                'color' => $data->color,
                'template_name' => $data->template_name,
            ];
            return view('scheduleEditTemplate', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data]);
        }
    }

    public function delete(Request $request) {
        Schedule::where('user_id', \Auth::user()->id)->where('id', $request->id)->delete();
        $request->merge([ 'deleted' => true ]);
        $schedule_data = self::getScheduleData($request->list_status, $request->list_display_style, $request->list_begin, $request->list_end, $request->list_repetition);
        return redirect(route('schedule.list', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'deleted' => $request->deleted]));
    }

    public function add(Request $request) {
        if($request->begin_date != NULL && $request->begin_time != NULL) {
            $request->merge([ 'begin' => $request->begin_date.' '.$request->begin_time.':00' ]);
        }
        else if($request->status != 'normal') {
            $request->merge([ 'begin' => true ]);
        }
        else {
            $request->merge([ 'begin' => NULL ]);
        }

        if($request->end_date != NULL && $request->end_time != NULL) {
            $request->merge([ 'end' => $request->end_date.' '.$request->end_time.':00' ]);
        }
        else if($request->status != 'normal') {
            $request->merge([ 'end' => true ]);
        }
        else {
            $request->merge([ 'end' => NULL ]);
        }

        $begin = date_create($request->begin);
        $end = date_create($request->end);

        if($request->repetition_begin_time != NULL) {
            $request->merge([ 'repetition_begin' => $request->repetition_begin_time.':00' ]);
        }
        else if($request->status == 'normal') {
            $request->merge([ 'repetition_begin' => true ]);
        }
        else {
            $request->merge([ 'repetition_begin' => NULL ]);
        }

        if($request->repetition_end_time != NULL) {
            $request->merge([ 'repetition_end' => $request->repetition_end_time.':00' ]);
        }
        else if($request->status == 'normal') {
            $request->merge([ 'repetition_end' => true ]);
        }
        else {
            $request->merge([ 'repetition_end' => NULL ]);
        }

        $repetition_begin = date_create($request->repetition_begin);
        $repetition_end = date_create($request->repetition_end);

        if(!$request->has('template_name')) {
            $request->merge([ 'template_name' => '' ]);
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
        if($request->input("repetition_everyday")) {
            $repetition_state = "1111111";
        }

        if($request->status == 'repetition' && $repetition_state == "0000000") {
            $request->merge([ 'repetition_setting' => NULL ]);
        }
        else {
            $request->merge([ 'repetition_setting' => true ]);
        }

        if($request->input('is_duplication') == NULL) $is_duplication = false;
        else $is_duplication = true;

        if($request->status != 'template') {
            $request->merge([ 'template_name' => '1' ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'begin' => ['required'],
            'end' => ['required'],
            'repetition_begin' => ['required'],
            'repetition_end' => ['required'],
            'repetition_setting' => ['required'],
            'memo' => ['nullable', 'string', 'max:255'],
            'template_name' => ['required', 'string', 'max:255'],
        ]);

        //新規登録の場合
        if(!$request->has('id')) {

            //通常のスケジュールとして登録する場合
            if($request->status == 'normal') {
                Schedule::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'normal',
                    'name' => $request->name,
                    'begin_time' => $begin,
                    'end_time' => $end,
                    'repetition_state' => $repetition_state,
                    'memo' => $request->memo,
                    'is_duplication' => $is_duplication,
                    'color' => $request->color,
                ]);
            }

            //繰り返しとして登録する場合
            else if($request->status == 'repetition') {
                Schedule::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'repetition',
                    'name' => $request->name,
                    'begin_time' => $repetition_begin,
                    'end_time' => $repetition_end,
                    'repetition_state' => $repetition_state,
                    'elapsed_days' => $request->repetition_end_date,
                    'memo' => $request->memo,
                    'is_duplication' => $is_duplication,
                    'color' => $request->color,
                ]);
            }

            //テンプレートとして登録する場合
            else if($request->status == 'template') {
                Schedule::create([
                    'user_id' => \Auth::user()->id,
                    'status' => 'template',
                    'name' => $request->name,
                    'begin_time' => $repetition_begin,
                    'end_time' => $repetition_end,
                    'repetition_state' => $repetition_state,
                    'elapsed_days' => $request->repetition_end_date,
                    'memo' => $request->memo,
                    'is_duplication' => $is_duplication,
                    'color' => $request->color,
                    'template_name' => $request->template_name,
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
            }
            else if($request->list_status == 'repetition') {
                $content->name = $request->name;
                $content->begin_time = $repetition_begin;
                $content->end_time = $repetition_end;
                $content->repetition_state = $repetition_state;
                $content->elapsed_days = $request->repetition_end_date;
                $content->memo = $request->memo;
                $content->is_duplication = $is_duplication;
                $content->color = $request->color;
                $content->save();
            }
            else if($request->list_status == 'template') {
                $content->name = $request->name;
                $content->begin_time = $repetition_begin;
                $content->end_time = $repetition_end;
                $content->elapsed_days = $request->repetition_end_date;
                $content->memo = $request->memo;
                $content->is_duplication = $is_duplication;
                $content->color = $request->color;
                $content->template_name = $request->template_name;
                $content->save();
            }

            $request->merge(['updated' => true]);
            return redirect(route('schedule.detail', ['id' => $request->id, 'list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'updated' => $request->updated]));
        }
    }
}
