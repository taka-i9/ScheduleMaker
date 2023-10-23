<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

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
        $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'color'])->where('user_id', \Auth::user()->id)->where('status', 'normal');
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
            ]);
        }

        //繰り返し要素の取り出し
        for($i = 0; $i < 7; $i++) {
            $data = Schedule::select(['id', 'name', 'begin_time', 'end_time', 'elapsed_days', 'color'])->where('user_id', \Auth::user()->id)->where('status', 'repetition');
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
                    ]);
                }
            }
        }

        return $list;
    }

    public function index(Request $request) {
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
        return view('representationTop', ['representation_style' => $request->representation_style, 'representation_data' => $representation_data, 'view_from' => $view_from, 'display_detail' => $display_detail]);
    }
}
