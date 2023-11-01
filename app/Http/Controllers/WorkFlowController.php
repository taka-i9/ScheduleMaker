<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkFlow;
use App\Models\WorkFlowContent;
use App\Models\WorkFlowLink;

class WorkFlowController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    function getWorkflowData($list_display_style, $list_begin, $list_end) {
        $workflow_data = array();
        $data = WorkFlow::select(['id', 'name', 'deadline'])->where('user_id', \Auth::user()->id);
        $data = $data->orderBy('deadline')->get();

        foreach($data as $value) {
            array_push($workflow_data, [
                'id' => $value->id,
                'name' => $value->name,
                'deadline' => $value->deadline,
            ]);
        }
        return $workflow_data;
    }

    public function new(Request $request) {
        return view('workflowRegistrationForm');
    }

    public function list(Request $request) {
        if(!$request->has('list_display_style')) {
            $request->merge([ 'list_display_style' =>'from_now' ]);
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

        $workflow_data = self::getWorkflowData($request->list_display_style, $request->list_begin, $request->list_end);
        return view('workflowListView', ['list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'workflow_data' => $workflow_data, 'deleted' => $request->deleted]);
    }

    public function detail(Request $request) {
        if(!$request->has('updated')) {
            $request->merge(['updated' => '']);
        }

        if((!$request->has('representation_style') || $request->representation_style == "") && (!$request->has('display_today') || $request->display_today == "")) {
            $is_from_list = true;
            $is_from_schedule = false;
        }
        else {
            $is_from_list = false;
            if(!$request->has('display_today') || $request->display_today == "") {
                $is_from_schedule = true;
            }
            else {
                $is_from_schedule = false;
            }
        }

        $data = WorkFlow::select(['id', 'name', 'deadline', 'memo', 'color'])->where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
        $data = [
            'id' => $data->id,
            'name' => $data->name,
            'deadline' => $data->deadline,
            'memo' => $data->memo,
            'color' => $data->color,
        ];
        
        return view('workflowDetail', ['list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'from_representation' => $request->from_representation, 'display_today' => $request->display_today, 'data' => $data, 'updated' => $request->updated, 'is_from_list' => $is_from_list, 'is_from_schedule' => $is_from_schedule]);
    }

    public function delete(Request $request) {
        WorkFlow::where('user_id', \Auth::user()->id)->where('id', $request->id)->delete();
        $request->merge([ 'deleted' => true ]);
        return redirect(route('workflow.list', ['list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'deleted' => $request->deleted]));
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

        //新規登録する場合
        if(!$request->has('id')) {
            $result = WorkFlow::create([
                'user_id' => \Auth::user()->id,
                'name' => $request->input('name'),
                'deadline' => $deadline,
                'memo' => $request->input('memo'),
                'color' => $request->input('color'),
            ]);

            return view('workflowRegistrationComplete', ['id' => $result->id]);
        }
        //更新の場合
        else {
            $content = WorkFlow::where('user_id', \Auth::user()->id)->where('id', $request->id)->first();
            
            $content->name = $request->name;
            $content->deadline = $deadline;
            $content->memo = $request->memo;
            $content->color = $request->color;
            $content->save();

            $request->merge(['updated' => true]);
            return redirect(route('workflow.detail', ['id' => $request->id, 'list_display_style' => $request->list_display_style, 'list_begin' => $request->list_begin, 'list_end' => $request->list_end, 'from_representation' => $request->from_representation, 'display_today' => $request->display_today, 'updated' => $request->updated]));
        }

    }

    public function edit_form(Request $request) {
        $data = WorkFlow::select(['contents_num'])->where('user_id', \Auth::user()->id)->where('id', (int)$request->id)->first();
        $contents_num = $data->contents_num;
        $contents_data = array();
        for($i = 1; $i <= $contents_num; $i++) {
            if(WorkFlowContent::where('user_id', \Auth::user()->id)->where('workflow_id', $request->id)->where('contents_id', $i)->exists()) {
                $data = WorkFlowContent::select(['name', 'required_minutes', 'margin_left', 'margin_top', 'is_done'])->where('user_id', \Auth::user()->id)->where('workflow_id', $request->id)->where('contents_id', $i)->first();

                array_push($contents_data, [
                    "id" => strval($i),
                    "name" => $data->name,
                    "hour" => ceil((int)$data->required_minutes / 60),
                    "minute" => $data->required_minutes % 60,
                    "margin_left" => $data->margin_left,
                    "margin_top" => $data->margin_top,
                    "is_done" => $data->is_done,
                ]);
            }
        }

        $connection = array();
        $data = WorkFlowLink::where('user_id',\Auth::user()->id)->where('workflow_id', $request->id)->get();
        foreach($data as $value) {
            $start_id = WorkFlowContent::select(['contents_id'])->where('user_id',\Auth::user()->id)->where('id', $value->start_id)->first();
            $start_id = $start_id->contents_id;
            $end_id = WorkFlowContent::select(['contents_id'])->where('user_id',\Auth::user()->id)->where('id', $value->end_id)->first();
            $end_id = $end_id->contents_id;
            if(!array_key_exists(strval($start_id), $connection)) {
                $connection[strval($start_id)] = array();
            }
            $connection[strval($start_id)][strval($end_id)] = true;
        }

        return view('workflowEditForm', ['workflow_id' => $request->id, 'contents_num' => $contents_num, 'contents_data' => $contents_data, 'connection' => $connection, 'updated' => false]);
        //return view('workflowEditForm', ['workflow_id' => $request->input('id'), 'contents_num' => '0', 'contents_data' => array(), 'connection' => array(), 'updated' => false]);

    }

    public function update(Request $request) {

        $contents_data = array();
        
        //リンクが存在しない場合は、NULLを渡すのを防ぐために、空の配列を用意する。
        if(!$request->has("connection")) {
            $request->merge(["connection" => array()]);
        }
        
        //contents_numの更新
        $workflow = WorkFlow::whereId($request->workflow_id)->first();
        $workflow->contents_num = $request->contents_num;
        $workflow->update();

        //各要素の更新
        for($i = 1; $i <= (int)$request->contents_num; $i++) {
            $recorded_content = WorkFlowContent::where('user_id', \Auth::user()->id)->where('workflow_id', $request->workflow_id)->where('contents_id', strval($i));
            $count = count($recorded_content->get());

            if($request->has("field_content_".strval($i)."_title_form")) {
                $content_name = $request->input("field_content_".strval($i)."_title_form");
                $content_time = (int)$request->input("field_content_".strval($i)."_time_hour_form") * 60 + (int)$request->input("field_content_".strval($i)."_time_minute_form");
                $content_margin_left = $request->input("field_content_".strval($i)."_margin_left_form");
                $content_margin_top = $request->input("field_content_".strval($i)."_margin_top_form");
                array_push($contents_data, [
                    "id" => strval($i),
                    "name" => $content_name,
                    "hour" => $request->input("field_content_".strval($i)."_time_hour_form"),
                    "minute" => $request->input("field_content_".strval($i)."_time_minute_form"),
                    "margin_left" => $content_margin_left,
                    "margin_top" => $content_margin_top
                ]);

                //更新する場合
                if($count >= 1) {
                    $content = $recorded_content->first();
                    $content->name = $content_name;
                    $content->rest_minutes = ($content->required_minutes > $content_time ? min($content->rest_minutes, $content->required_minutes) : $content->rest_minutes + $content_time - $content->required_minutes);
                    $content->required_minutes = $content_time;
                    $content->margin_left = $content_margin_left;
                    $content->margin_top = $content_margin_top;
                    $content->save();
                }
                //新規に追加する場合
                else {
                    WorkFlowContent::create([
                        'user_id' => \Auth::user()->id,
                        'workflow_id' => $request->workflow_id,
                        'contents_id' => strval($i),
                        'name' => $content_name,
                        'required_minutes' => $content_time,
                        'rest_minutes' => $content_time,
                        'margin_left' => $content_margin_left,
                        'margin_top' => $content_margin_top,
                    ]);
                }
            }
            //削除する場合
            else if($count >= 1) {
                $recorded_content->delete();
            }
        }

        //各リンクの更新
        for($i = 1; $i <= (int)$request->contents_num; $i++) {
            $start_id = "";
            $end_id = "";
            $start = WorkFlowContent::where('user_id', \Auth::user()->id)->where('workflow_id', $request->workflow_id)->where('contents_id', strval($i));
            
            if(count($start->get()) >= 1) {
                $start_id = $start->first()->id;
            }
            else continue;

            for($j = 1; $j <= (int)$request->contents_num; $j++) {
                $end = WorkFlowContent::where('user_id', \Auth::user()->id)->where('workflow_id', $request->workflow_id)->where('contents_id', strval($j));

                if(count($end->get()) >= 1) {
                    $end_id = $end->first()->id;
                }
                else continue;
                
                $recorded_link = WorkFlowLink::where('user_id', \Auth::user()->id)->where('workflow_id', $request->workflow_id)->where('start_id', $start_id)->where('end_id', $end_id);
                $count = count($recorded_link->get());
                $check = array_key_exists(strval($i), $request->connection) && array_key_exists(strval($j), $request->connection[strval($i)]); 

                //新規に追加する場合
                if($check && $count == 0) {
                    WorkFlowLink::create([
                        'user_id' => \Auth::user()->id,
                        'workflow_id' => $request->workflow_id,
                        'start_id' => $start_id,
                        'end_id' => $end_id,
                    ]);
                }
                else if(!$check && $count >= 1) {
                    $recorded_link->delete();
                }
            }
        }

        return view('workflowEditForm', ['workflow_id' => $request->workflow_id, 'contents_num' => $request->contents_num, 'contents_data' => $contents_data, 'connection' => $request->connection, 'updated' => true]);
    }
}
