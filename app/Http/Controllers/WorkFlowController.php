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

        $result = WorkFlow::create([
            'user_id' => \Auth::user()->id,
            'name' => $request->input('name'),
            'deadline' => $deadline,
            'memo' => $request->input('memo'),
            'color' => $request->input('color'),
        ]);

        return view('workflowRegistrationComplete', ['workflow_id' => $result->id]);

    }

    public function edit_form(Request $request) {
        return view('workflowEditForm', ['workflow_id' => $request->input('workflow_id'), 'contents_num' => '0', 'contents_data' => array(), 'connection' => array(), 'updated' => false]);
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
                $contents_data[strval($i)] = array(
                    "name" => $content_name,
                    "hour" => $request->input("field_content_".strval($i)."_time_hour_form"),
                    "minute" => $request->input("field_content_".strval($i)."_time_minute_form"),
                    "margin_left" => $content_margin_left,
                    "margin_top" => $content_margin_top
                );

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
