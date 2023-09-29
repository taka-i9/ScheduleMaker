@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="GET" id="registration_form" action="{{ route('schedule.edit') }}" class="registration_form_content">
            @csrf

            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        スケジュール名<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="name"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="begin_time"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="end_time"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        メモ<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" style="scroll: auto" id="memo"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ToDoリストとの重複を許可<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value" id="is_duplication"></div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ラベルの色<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <input type="color" name="color" id="color" class="form_element_color" value="{{ old('color') == '' ? '#ffffff' : old('color') }}" disabled>
                    </div>
                </div>
            </div>
            <button type="button" id="edit_button" onclick="toEdit()">編集</button>
            &nbsp;
            <button type="button" onclick="backList()">一覧に戻る</button>

            <input type="hidden" name="id" id="id">
            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_display_style" id="list_display_style">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
        </form>
    </div>
</div>

<script>
    var data = {<?php 
        if(isset($data)) {
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
        }
    ?>};

    var list_status = "<?php if(isset($list_status)) echo $list_status; else echo old('list_status'); ?>";
    var list_display_style = "<?php if(isset($list_display_style)) echo $list_display_style; else echo old('list_display_style'); ?>";
    var list_begin = "<?php if(isset($list_begin)) echo $list_begin; else echo old('list_begin'); ?>";
    var list_end = "<?php if(isset($list_end)) echo $list_end; else echo old('list_end'); ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    window.onload = function() {
        var name = data['name'];
        var begin_date = data['begin_time'].substr(0, 10);
        var begin_time = data['begin_time'].substr(-8, 5);
        var end_date = data['end_time'].substr(0, 10);
        var end_time = data['end_time'].substr(-8, 5);
        var memo = data['memo'];
        var is_duplication = data['is_duplication'];
        var color = data['color'];
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + name + '</div>';
        document.getElementById('begin_time').innerHTML = '<div class="form_element_text">' + begin_date + ' ' + begin_time + '</div>';
        document.getElementById('end_time').innerHTML = '<div class="form_element_text">' + end_date + ' ' + end_time + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + memo + '</div>';
        document.getElementById('is_duplication').innerHTML = '<div class="form_element_text">' + (is_duplication!="" ? '許可する' : '許可しない') + '</div>';
        document.getElementById('color').value = color;
        
        document.getElementById('id').value = data['id'];
        document.getElementById('list_status').value = list_status;
        document.getElementById('list_display_style').value = list_display_style;
        document.getElementById('list_begin').value = list_begin;
        document.getElementById('list_end').value = list_end;

        if(updated) {
            alert("更新しました。");
        }
    };

    function toEdit() {
        let req_token = "_token=" + document.getElementsByName('csrf-token')[0].content;
        let req_id = "id=" + document.getElementById('id').value;
        let req_list_status = "list_status=" + document.getElementById('list_status').value;
        let req_list_display_style = "list_display_style=" + document.getElementById('list_display_style').value;
        let req_list_begin = "list_begin=" + document.getElementById('list_begin').value;
        let req_list_end = "list_end=" + document.getElementById('list_end').value;
        let url = "{{ route('schedule.edit')}}" + "?" + req_id + "&" + req_list_status + "&" + req_list_display_style + "&" + req_list_begin + "&" + req_list_end;
        location.href = url;
    }

    function backList() {
        document.getElementById('registration_form').action = "{{ route('schedule.list') }}";
        document.getElementById('registration_form').submit();
    }

    function changeEndDateLimit(){
        document.getElementById("begin_date").max = document.getElementById("end_date").value;
        document.getElementById("end_date").min = document.getElementById("begin_date").value;
    }

    function changeEndTimeLimit(){
        if(document.getElementById("begin_date").value === document.getElementById("end_date").value){
            document.getElementById("end_time").min = document.getElementById("begin_time").value;
        }
        else{
            document.getElementById("end_time").min = "";
        }
    }

    function changeEndLimit() {
        changeEndDateLimit();
        changeEndTimeLimit();
    }
</script>

@endsection