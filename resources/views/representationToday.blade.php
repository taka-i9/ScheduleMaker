@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            ToDo / ワークフロー 表示
        </div>
    </div>
    <div class="view_form_edit">
        <form method="GET" id="setting">
            <input type="hidden" name="from_representation" id="from_representation" value="1">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="POST" id="done" action="{{ route('representation.todo_done') }}">
            @csrf
            <input type="hidden" name="id" id="done_id">
            <input type="hidden" name="content_id" id="done_content_id">
        </form>
    </div>
    <div class="view_form_content" id="view_form_content">
    </div>
</div>

<script>
    let original_schedule_data = [<?php 
        foreach($representation_data['schedule'] as $data) {
            print '{';
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        }
    ?>];

    //並べ替える
    original_schedule_data.sort(function(a, b) {
        if(a['begin_date'] == b['begin_date']) {
            if(a['begin_time'] == b['begin_time']) {
                if(a['end_date'] == b['end_date']) {
                    return a['end_time'] < b['end_time'] ? -1 : 1;
                }
                else return a['end_date'] <= b['end_date'] ? -1 : 1 ;
            }
            else return a['begin_time'] < b['begin_time'] ? -1 : 1;
        }
        else return a['begin_date'] < b['begin_date'] ? -1 : 1;
    });

    let todo_data = [<?php 
        foreach($representation_data['todo'] as $data) {
            print '{';
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        }
    ?>];

    let workflow_data = [<?php 
        foreach($representation_data['workflow'] as $workflow_data) {
            print '{';
            foreach($workflow_data as $workflow_key => $workflow_value) {
                if($workflow_key !== 'content_list') print '"'.$workflow_key.'": "'.$workflow_value.'",';
                else {
                    print '"'.$workflow_key.'": [';
                    foreach($workflow_value as $data) {
                        print '{';
                        foreach($data as $key => $value) {
                            print '"'.$key.'": "'.$value.'",';
                        }
                        print '},';
                    } 
                    print '],';
                }
            }
            print '},';
        }
    ?>];    
</script>

@endsection