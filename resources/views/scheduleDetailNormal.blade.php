@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" id="registration_form" action="{{ route('schedule.add') }}" class="registration_form_content">
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
            <button type="button" id="update_button" onclick="update()" hidden disabled>保存</button>
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

    var name = "{{ old('name') }}";
    var begin_date = "{{ old('begin_date') }}";
    var begin_time = "{{ old('begin_time') }}";
    var end_date = "{{ old('end_date') }}";
    var end_time = "{{ old('end_time') }}";
    var memo = "{{ old('memo') }}";
    var is_duplication = "{{ old('is_duplication') }}";
    var color = "{{ old('color') }}";
    var list_status = "<?php if(isset($list_status)) echo $list_status; else echo old('list_status'); ?>";
    var list_display_style = "<?php if(isset($list_display_style)) echo $list_display_style; else echo old('list_display_style'); ?>";
    var list_begin = "<?php if(isset($list_begin)) echo $list_begin; else echo old('list_begin'); ?>";
    var list_end = "<?php if(isset($list_end)) echo $list_end; else echo old('list_end'); ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    window.onload = function() {
        if(data.length == 0) {
            alert("エラー");
            document.getElementById('id').value = "{{ old('id') }}";
            toEdit();
        }
        else {
            name = data['name'];
            begin_date = data['begin_time'].substr(0, 10);
            begin_time = data['begin_time'].substr(-8, 5);
            end_date = data['end_time'].substr(0, 10);
            end_time = data['end_time'].substr(-8, 5);
            memo = data['memo'];
            is_duplication = data['is_duplication'];
            color = data['color'];
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
        }
        if(updated) {
            alert("更新しました。");
        }
    };

    function toEdit() {
        let name_error = document.createElement('div');
        name_error.className = "form_element_error";
        name_error.insertHTML = '<span style="color: red;" role="alert">' + "{{ $errors->has('name') ? $errors->first('name') : '' }}" + '</span>';
        document.getElementById('name').parentNode.parentNode.prepend(name_error);
        document.getElementById('name').parentNode.className = 'form_element_input';
        document.getElementById('name').innerHTML = '<input type="text" name="name" class="form_element_text ' + "{{ $errors->has('name') ? 'is-invalid' : '' }}" +'" value="' + name +'">';
        let begin_time_error = document.createElement('div');
        begin_time_error.className = "form_element_error";
        begin_time_error.insertHTML = '<span style="color: red;" role="alert">' + "{{ $errors->has('begin') ? $errors->first('begin') : '' }}" + '</span>';
        document.getElementById('begin_time').parentNode.parentNode.prepend(begin_time_error);
        document.getElementById('begin_time').parentNode.className = 'form_element_input';
        document.getElementById('begin_time').innerHTML = '';
        let begin_time_input = document.createElement('div');
        begin_time_input.className = "form_element_time";
        begin_time_input.innerHTML = '<input type="date" name="begin_date" id="begin_date" value="' + begin_date + '" onchange="changeEndLimit()">&nbsp<input type="time" name="begin_time" id="begin_time" value="' + begin_time + '" onchange="changeEndLimit()">';
        document.getElementById('begin_time').appendChild(begin_time_input);
        let end_time_error = document.createElement('div');
        end_time_error.className = "form_element_error";
        end_time_error.insertHTML = '<span style="color: red;" role="alert">' + "{{ $errors->has('end') ? $errors->first('end') : '' }}" + '</span>';
        document.getElementById('end_time').parentNode.parentNode.prepend(end_time_error);
        document.getElementById('end_time').parentNode.className = 'form_element_input';
        document.getElementById('end_time').innerHTML = '';
        let end_time_input = document.createElement('div');
        end_time_input.className = "form_element_time";
        end_time_input.innerHTML = '<input type="date" name="end_date" id="end_date" value="' + end_date + '" onchange="changeEndLimit()">&nbsp<input type="time" name="end_time" id="end_time" value="' + end_time + '" onchange="changeEndLimit()">';
        document.getElementById('end_time').appendChild(end_time_input);
        let memo_error = document.createElement('div');
        memo_error.className = "form_element_error";
        memo_error.insertHTML = '<span style="color: red;" role="alert">' + "{{ $errors->has('memo') ? $errors->first('memo') : '' }}" + '</span>';
        document.getElementById('memo').parentNode.parentNode.prepend(memo_error);
        document.getElementById('memo').parentNode.className = 'form_element_input';
        document.getElementById('memo').innerHTML = '<input type="text" name="memo" class="form_element_text ' + "{{ $errors->has('memo') ? 'is-invalid' : '' }}" +'" value="' + memo +'">';
        document.getElementById('is_duplication').innerHTML = '<input type="checkbox" name="is_duplication" class="form_element_checkbox" ' + (is_duplication ? 'checked' : '') + '>';
        document.getElementById('color').disabled = false;
        document.getElementById('edit_button').hidden = true;
        document.getElementById('update_button').hidden = false;
        document.getElementById('update_button').disabled = false;
    }

    function update() {
        let edited = document.createElement('input');
        edited.type = 'hidden';
        edited.name = 'edited';
        edited.value = true;
        document.getElementById('registration_form').appendChild(edited);
        document.getElementById('registration_form').submit();
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