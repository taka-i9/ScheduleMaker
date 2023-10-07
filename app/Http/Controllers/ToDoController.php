<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToDo;

class ToDoController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    function getScheduleData($list_status, $list_display_style, $list_begin, $list_end, $list_repetition) {
        $schedule_data = array();
        $data = '';
        if($list_status == "normal") {
            $data = ToDo::select(['id', 'name', 'deadline', 'type'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
            if($list_display_style == 'from_now') {
                $now = date('Y-m-d H:i:s');
                $data = $data->where('deadline', '>=', $now);
            }
            else if($list_display_style != 'all') {
                $begin = new \DateTimeImmutable($list_begin." 0:00:00");
                $end = new \DateTimeImmutable($list_end." 0:00:00");
                $end = $end->modify("+1 day");
                $data = $data->where('deadline', '>=', $begin)->where('deadline', '<', $end);
            }
            $data = $data->orderBy('deadline')->get();

            foreach($data as $value) {
                array_push($schedule_data, [
                    'id' => $value->id,
                    'name' => $value->name,
                    'deadline' => $value->deadline,
                    'type' => $value->type,
                ]);
            }
        }
        else if($list_status == "repetition") {
            $data = ToDo::select(['id', 'name', 'repetition_state'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
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
            $data = ToDo::select(['id', 'template_name', 'repetition_state'])->where('user_id', \Auth::user()->id)->where('status', $list_status);
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
        return view('todoRegistrationForm');
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

        return view('todoListView', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'schedule_data' => $schedule_data, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'deleted' => $request->deleted]);
    }

    public function detail(Request $request) {
        if(!$request->has('updated')) {
            $request->merge(['updated' => '']);
        }

        $data = ToDo::select(['id', 'status', 'type', 'name', 'deadline', 'required_minutes', 'repetition_state', 'memo', 'priority_level', 'color', 'template_name'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
        $data = [
            'id' => $data->id,
            'status' => $data->status,
            'type' => $data->type,
            'name' => $data->name,
            'deadline' => $data->deadline,
            'required_minutes' => $data->required_minutes,
            'repetition_state' => $data->repetition_state,
            'memo' => $data->memo,
            'priority_level' => $data->priority_level,
            'color' => $data->color,
            'template_name' => $data->template_name,
        ];
        
        return view('todoDetail', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'data' => $data, 'updated' => $request->updated]);
    }

    public function delete(Request $request) {
        ToDo::where('user_id', \Auth::user()->id)->where('id', $request->id)->delete();
        $request->merge([ 'deleted' => true ]);
        $schedule_data = self::getScheduleData($request->list_status, $request->list_display_style, $request->list_begin, $request->list_end, $request->list_repetition);
        return redirect(route('todo.list', ['list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'deleted' => $request->deleted]));
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

            return view('todoRegistrationComplete');
        }
        //更新の場合
        else {
            $content = ToDo::where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            
            if($request->status == 'normal') {
                $content->type = $request->type;
                $content->name = $request->name;
                $content->deadline = $deadline;
                $content->required_minutes = $required_minutes;
                $content->rest_minutes = $required_minutes;
                $content->memo = $request->memo;
                $content->priority_level = (int)$request->priority_level;
                $content->color = $request->color;
                $content->save();
            }
            
            else if($request->status == 'repetition') {
                $content->type = $request->type;
                $content->name = $request->name;
                $content->deadline = $repetition_deadline;
                $content->required_minutes = $required_minutes;
                $content->rest_minutes = $required_minutes;
                $content->repetition_state = $repetition_state;
                $content->memo = $request->memo;
                $content->priority_level = (int)$request->priority_level;
                $content->color = $request->color;
                $content->save();
            }

            else if($request->status == 'template') {
                $content->type = $request->type;
                $content->name = $request->name;
                $content->deadline = $repetition_deadline;
                $content->required_minutes = $required_minutes;
                $content->rest_minutes = $required_minutes;
                $content->memo = $request->memo;
                $content->priority_level = (int)$request->priority_level;
                $content->color = $request->color;
                $content->template_name = $request->template_name;
                $content->save();
            }

            $request->merge(['updated' => true]);
            return redirect(route('todo.detail', ['id' => $request->id, 'list_status' => $request->list_status, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'list_repetition' => $request->list_repetition, 'updated' => $request->updated]));
        }
    }
}
