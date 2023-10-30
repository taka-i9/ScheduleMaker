<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Todo;
use App\Models\WorkFlow;
use App\Models\WorkFlowContent;
use App\Models\WorkFlowLink;

class RepresentationController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    function getSchedule($begin_date, $end_date) {
        $list = array();

        //通常のスケジュールの取り出し
        $begin = date('Y-m-d 00:00:00', strtotime($begin_date));
        $end = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($end_date)));
        $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'color', 'is_duplication'])->where('user_id', \Auth::user()->id)->where('status', 'normal');
        $data = $data->where(function($query2)use($begin, $end) {
            $query2->where(function($query)use($begin, $end) {
                //期間内から始まるスケジュール
                $query->where('begin_time', '>=', $begin)->where('begin_time', '<', $end);
            })->orwhere(function($query)use($begin, $end) {
                //期間内で終わるスケジュール
                $query->where('end_time', '>=', $begin)->where('end_time', '<', $end);
            })->orwhere(function($query)use($begin, $end) {
                //期間内で継続するスケジュール
                $query->where('begin_time', '<', $begin)->where('end_time', '>=', $end);
            });
        });
        $data = $data->orderBy('begin_time')->get();
        foreach($data as $value) {
            $data_begin_date = substr($value->begin_time, 0, 10);
            $data_begin_time = substr($value->begin_time, -8, 5);
            $data_end_date = substr($value->end_time, 0, 10);
            $data_end_time = substr($value->end_time, -8, 5);
            $is_begin_out = false;
            $is_end_out = false;
            if($data_begin_date < $begin_date) {
                $is_begin_out = true;
                $data_begin_date = $begin_date;
                $data_begin_time = '00:00';
            }
            if($data_end_date > $end_date) {
                if(!(date('Y-m-d',strtotime('+1 day', strtotime($end_date))) == $data_end_date && $data_end_time == '00:00')) {
                    $is_end_out = true;
                }
                $data_end_date = $end_date;
                $data_end_time = '24:00';
            }
            array_push($list, [
                'id' => $value->id,
                'name' => $value->name,
                'begin_date' => $data_begin_date,
                'begin_time' => $data_begin_time,
                'end_date' => $data_end_date,
                'end_time' => $data_end_time,
                'color' => $value->color,
                'status' => 'normal',
                'is_begin_out' => $is_begin_out,
                'is_end_out' => $is_end_out,
                'is_duplication' => $value->is_duplication,
            ]);
        }

        //繰り返し要素の取り出し
        for($i = 0; $i < 7; $i++) {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'elapsed_days', 'color', 'is_duplication'])->where('user_id', \Auth::user()->id)->where('status', 'repetition');
            $pattern = str_repeat('_', $i).'1'.str_repeat('_', 7 - $i - 1);
            $data = $data->where('repetition_state', 'like', $pattern);
            $data = $data->get();
            foreach($data as $value) {
                $date = date('Y-m-d', strtotime('-'.(string)$value->elapsed_days.' days', strtotime($begin_date)));
                $day = (int)date('w', strtotime($date));
                $date = new \DateTimeImmutable($date);
                $date = $date->modify('+'.(string)((7 + $i - $day) % 7).' days');
                for(;$date->format('Y-m-d') <= $end_date; $date = $date->modify('+7 days')) {
                    $data_begin_date = $date->format('Y-m-d');
                    $data_begin_time = substr($value->begin_time, -8, 5);
                    $data_end_date = $date->modify('+'.(string)$value->elapsed_days.' days')->format('Y-m-d');
                    $data_end_time = substr($value->end_time, -8, 5);
                    $is_begin_out = false;
                    $is_end_out = false;
                    if($data_begin_date < $begin_date) {
                        $is_begin_out = true;
                        $data_begin_date = $begin_date;
                        $data_begin_time = '00:00';
                    }
                    if($data_end_date > $end_date) {
                        if(!(date('Y-m-d',strtotime('+1 day', strtotime($end_date))) == $data_end_date && $data_end_time == '00:00')) {
                            $is_end_out = true;
                        }
                        $data_end_date = $end_date;
                        $data_end_time = '24:00';
                    }
                    array_push($list, [
                        'id' => $value->id,
                        'name' => $value->name,
                        'begin_date' => $data_begin_date,
                        'begin_time' => $data_begin_time,
                        'end_date' => $data_end_date,
                        'end_time' => $data_end_time,
                        'color' => $value->color,
                        'status' => 'repetition',
                        'is_begin_out' => $is_begin_out,
                        'is_end_out' => $is_end_out,
                        'is_duplication' => $value->is_duplication,
                    ]);
                }
            }
        }

        return $list;
    }

    function getToDo() {
        $list = array();
        $now = date('Y-m-d h:i:s');

        //繰り返し要素については、日付が変わった時に実行するプログラムにおいて6日後のものを通常のものとして追加するため、ここでは処理を行わない
        //通常のToDoの取り出し
        $begin = date('Y-m-d 00:00:00', strtotime($now));
        $end = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($now)));
        $data = Todo::select(['id', 'name', 'type', 'deadline', 'rest_minutes', 'priority_level', 'color'])->where('user_id', \Auth::user()->id)->where('status', 'normal')->where('is_done', false);
        $data = $data->where(function($query2)use($begin, $end) {
            $query2->where('type', 'deadline')
            ->orwhere(function($query)use($begin, $end) {
                $query->where('type', 'today')->where('deadline', '>=', $begin)->where('deadline', '<', $end);
            });
        });
        $data = $data->orderBy('deadline')->get();
        foreach($data as $value) {
            $data_deadline_date = substr($value->deadline, 0, 10);
            $data_deadline_time = substr($value->deadline, -8, 5);
            $is_over = false;
            if($value->deadline < $now) {
                $is_over = true;
            }
            array_push($list, [
                'id' => $value->id,
                'name' => $value->name,
                'deadline_date' => $data_deadline_date,
                'deadline_time' => $data_deadline_time,
                'rest_minutes' => $value->rest_minutes,
                'priority_level' => $value->priority_level,
                'color' => $value->color,
                'status' => 'normal',
                'type' => $value->type,
                'is_over' => $is_over,
            ]);
        }

        return $list;
    }

    function getWorkFlow() {
        $now = date('Y-m-d h:i:s');
        $list = array();
        $workflow_data = WorkFlow::select(['id', 'name', 'deadline', 'color'])->where('user_id', \Auth::user()->id)->orderBy('deadline')->get();
        foreach($workflow_data as $workflow_value) {
            $data_deadline_date = substr($workflow_value->deadline, 0, 10);
            $data_deadline_time = substr($workflow_value->deadline, -8, 5);
            $is_over = false;
            if($workflow_value->deadline < $now) {
                $is_over = true;
            }
            array_push($list, [
                'id' => $workflow_value->id,
                'name' => $workflow_value->name,
                'deadline_date' => $data_deadline_date,
                'deadline_time' => $data_deadline_time,
                'color' => $workflow_value->color,
                'is_over' => $is_over,
                'content_list' => array(),
            ]);
            $workflow_content_data = WorkFlowContent::select(['id', 'name', 'rest_minutes'])
            ->where('user_id', \Auth::user()->id)->where('workflow_id', $workflow_value->id)->where('is_done', false)->get();
            //ある要素を行うために事前に行っている必要のある要素が完了しているかを確認
            $have_parent = array();
            foreach($workflow_content_data as $workflow_content_value) {
                $data = WorkFlowLink::select('end_id')->where('user_id', \Auth::user()->id)->where('start_id', $workflow_content_value->id)->get();
                foreach($data as $value) {
                    $have_parent[$value->end_id] = true;
                }
            }
            foreach($workflow_content_data as $workflow_content_value) {
                if(!array_key_exists($workflow_content_value->id, $have_parent)) {
                    array_push($list[array_key_last($list)]['content_list'], [
                        'id' => $workflow_content_value->id,
                        'name' => $workflow_content_value->name,
                        'rest_minutes' => $workflow_content_value->rest_minutes,
                    ]);
                }
            }
        }

        return $list;
    }

    public function schedule(Request $request) {
        $representation_data = ['schedule' => '', 'todo' => '', 'workflow' => ''];
        $now = date('Y-m-d H:i:s');
        $display_detail = '';
        if($request->has('display_detail')) {
            $display_detail = $request->display_detail;
            if($request->has('view_from')) $view_from = $request->view_from;
            else $view_from = date('Y-m-d');
            $begin = new \DateTimeImmutable($display_detail.' 00:00:00');
            $end = $begin;
        }
        else if($request->representation_style == 'month') {
            if($request->has('view_from')) $view_from = $request->view_from;
            else $view_from = date('Y-m');
            $begin = new \DateTimeImmutable($view_from.'-01 00:00:00');
            $end = $begin->modify('+1 month')->modify('-1 days');
        }
        else if($request->representation_style == 'week') {
            if($request->has('view_from')) $view_from = $request->view_from;
            else {
                $view_from = new \DateTimeImmutable(date('Y-m-d'));
                $view_from = $view_from->modify('-'.$view_from->format('w').' days')->format('Y-m-d');
            }
            $begin = new \DateTimeImmutable($view_from.' 00:00:00');
            $end = $begin->modify('+1 week')->modify('-1 days');
        }
        else if($request->representation_style == 'date') {
            if($request->has('view_from')) $view_from = $request->view_from;
            else $view_from = date('Y-m-d');
            $begin = new \DateTimeImmutable($view_from.' 00:00:00');
            $end = $begin;
        }
        $representation_data['schedule'] = self::getSchedule($begin->format('Y-m-d'), $end->format('Y-m-d'));
        return view('representationSchedule', ['representation_style' => $request->representation_style, 'representation_data' => $representation_data, 'view_from' => $view_from, 'display_detail' => $display_detail]);
    }

    public function todo(Request $request) {
        $representation_data = ['schedule' => '', 'todo' => '', 'workflow' => ''];
        $representation_data['todo'] = self::getToDo();
        $representation_data['workflow'] = self::getWorkFlow();
        return view('representationToDo', ['representation_data' => $representation_data]);
    }

    public function todo_update(Request $request) {
        //todoを更新する場合
        if($request->content_id == '-1') {
            $data = Todo::where('user_id', \Auth::user()->id)->where('id', (int)$request->id)->first();
            $data->rest_minutes = (int)$request->time;
            $data->save();
        }
        //ワークフローの要素を更新する場合
        else {
            $data = WorkFlowContent::where('user_id', \Auth::user()->id)->where('id', (int)$request->content_id)->first();
            $data->rest_minutes = (int)$request->time;
            $data->save();
        }
        return;
    }

    public function todo_done(Request $request) {
        //todoを更新する場合
        if($request->content_id == '-1') {
            $data = Todo::where('user_id', \Auth::user()->id)->where('id', (int)$request->id)->first();
            $data->is_done = true;
            $data->save();
        }
        //ワークフローの要素を更新する場合
        else {
            $data = WorkFlowContent::where('user_id', \Auth::user()->id)->where('id', (int)$request->content_id)->first();
            $data->is_done = true;
            $data->save();
        }
        return redirect(route('representation.todo'));
    }

    public function today(Request $request) {
        $representation_data = ['schedule' => array(), 'todo' => array(), 'workflow' => array()];
        $begin = new \DateTimeImmutable(date('Y-m-d').' 00:00:00');
        $end = $begin;

        $representation_data['schedule'] = self::getSchedule($begin->format('Y-m-d'), $end->format('Y-m-d'));
        $begin_list = array();
        $end_list = array();
        //設定で変更可能にする予定
        $minutes = (24 - (int)date('H')) * 30;
        //todoに用いることのできる時間を計算する
        foreach($representation_data['schedule'] as $data) {
            if(!$data['is_duplication']) {
                if($data['begin_date'] == date('Y-m-d', strtotime($request->date))) {
                    $time -= (int)substr($data['begin_time'], 0, 2) * 60 + (int)substr($data['begin_time'], -2, 2);
                }
                else $time = 0;
                array_push($begin_list, $time);
                if($data['end_date'] == date('Y-m-d', strtotime($request->date))) {
                    $time = (int)substr($data['end_time'], 0, 2) * 60 + (int)substr($data['end_time'], -2, 2);
                }
                else $time = 24 * 60;
                array_push($end_list, $time);
            }
        }
        sort($begin_list);
        sort($end_list);
        for($i = count($begin_list) - 1; $i >= 1; $i--) {
            if($begin_list[$i] <= $end_list[$i - 1]) {
                array_splice($begin_list, $i, 1);
                array_splice($end_list, $i - 1, 1);
            }
        }
        for($i = 0; $i < count($begin_list); $i++) {
            $minutes -= (min($end_list[$i], (int)date('H') * 60) - min($begin_list[$i], (int)date('H') * 60));
        }

        $todo_data = self::getToDo();
        for($todo_i = 0; $todo_i < count($todo_data); $todo_i++) {
            if($todo_data[$todo_i]['deadline_time'] <= date('Y-m-d')) {
                $minutes -= $todo_data[$todo_i]['rest_minutes'];
                array_push($representation_data['todo'], $todo_data[$todo_i]);
            }
            else break;
        }
        $workflow_data = self::getWorkFlow();
        for($workflow_i = 0; $workflow_i < count($workflow_data); $workflow_i++) {
            if(count($workflow_data[$workflow_i]['content_list']) == 0) continue;
            else if($workflow_data[$workflow_i]['deadline_time'] <= date('Y-m-d')) {
                foreach($workflow_data[$workflow_i]['content_list'] as $data) {
                    $minutes -= $data['rest_minutes'];
                }
                array_push($representation_data['workflow'], $workflow_data[$workflow_i]);
            }
            else break;
        }

        $minutes_todo = $minutes / 2;
        $minutes_workflow = $minutes / 2;
        for(; $todo_i < count($todo_data) && $minutes_todo > 0; $todo_i++) {
            $minutes_todo -= $todo_data[$todo_i]['rest_minutes'];
            array_push($representation_data['todo'], $todo_data[$todo_i]);
        }
        for(; $workflow_i < count($workflow_data) && $minutes_workflow > 0; $workflow_i++) {
            if(count($workflow_data[$workflow_i]['content_list']) == 0) continue;
            foreach($workflow_data[$workflow_i]['content_list'] as $data) {
                $minutes_workflow -= $data['rest_minutes'];
            }
            array_push($representation_data['workflow'], $workflow_data[$workflow_i]);
        }

        $minutes = $minutes - $minutes_todo - $minutes_workflow;
        if($minutes_todo > 0) {
            for(; $workflow_i < count($workflow_data) && $minutes > 0; $workflow_i++) {
                if(count($workflow_data[$workflow_i]['content_list']) == 0) continue;
                foreach($workflow_data[$workflow_i]['content_list'] as $data) {
                    $minutes -= $data['rest_minutes'];
                }
                array_push($representation_data['workflow'], $workflow_data[$workflow_i]);
            }
        }
        if($minutes_workflow > 0) {
            for(; $todo_i < count($todo_data) && $minutes > 0; $todo_i++) {
                $minutes -= $todo_data[$todo_i]['rest_minutes'];
                array_push($representation_data['todo'], $todo_data[$todo_i]);
            }
        }

        return view('representationToday', ['representation_data' => $representation_data]);
    }
}
