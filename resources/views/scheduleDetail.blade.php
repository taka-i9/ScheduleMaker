@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" action="{{ route('schedule.add') }}" class="registration_form_content" id="form">
            @csrf
        
            <input type="hidden" name="status" id="status">
            <div class="form_elements" id="template_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        テンプレート名<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="template_name_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="template_name"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        スケジュール名<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="name_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="name"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="normal_begin_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="normal_begin_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="begin"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="normal_end_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="normal_end_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="end"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_begin_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="repetition_begin_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="repetition_begin"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_end_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="repetition_end_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="repetition_end"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="repetition_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="repetition"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        メモ<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="memo_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="memo"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ToDoリストとの重複を許可<br>
                    </div>
                </div>
                <div class="form_element_input_no_error" id="is_duplication_base">
                    <div class="form_element_value" id="is_duplication"></div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ラベルの色<br>
                    </div>
                </div>
                <div class="form_element_input_no_error" id="color_base">
                    <div class="form_element_value">
                        <input type="color" name="color" id="color" class="form_element_color" disabled>
                    </div>
                </div>
            </div>
            <button type="button" id="edit_button" onclick="toEdit()">編集</button>
            <button type="button" id="submit_button" onclick="toSave()" hidden>保存</button>
            &nbsp;
            <button type="button" onclick="backList()">一覧に戻る</button>

            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_display_style" id="list_display_style">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
            <input type="hidden" name="list_repetition" id="list_repetition">
            <input type="hidden" name="representation_style" id="representation_style">
            <input type="hidden" name="view_from" id="view_from">
            <input type="hidden" name="display_detail" id="display_detail">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="GET" id="back" action="{{ route('schedule.list') }}">
            <input type="hidden" name="list_status" id="list_status_back">
            <input type="hidden" name="list_display_style" id="list_display_style_back">
            <input type="hidden" name="list_begin" id="list_begin_back">
            <input type="hidden" name="list_end" id="list_end_back">
            <input type="hidden" name="list_repetition" id="list_repetition_back">
            <input type="hidden" name="representation_style" id="representation_style_back">
            <input type="hidden" name="view_from" id="view_from_back">
            <input type="hidden" name="display_detail" id="display_detail_back">
        </form>
    </div>
</div>

<script>
    var list_status = "<?php if(isset($list_status)) echo $list_status; ?>";
    var list_display_style = "<?php if(isset($list_display_style)) echo $list_display_style; ?>";
    var list_begin = "<?php if(isset($list_begin)) echo $list_begin; ?>";
    var list_end = "<?php if(isset($list_end)) echo $list_end; ?>";
    var list_repetition = "<?php if(isset($list_repetition)) echo $list_repetition; ?>";
    var representation_style = "<?php if(isset($representation_style)) echo $representation_style; ?>";
    var view_from = "<?php if(isset($view_from)) echo $view_from; ?>";
    var display_detail = "<?php if(isset($display_detail)) echo $display_detail; ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    changeStatus("{{ $data['status'] }}");

    var status = "<?php if(array_key_exists('status', $data)) echo $data['status']; ?>";
    var template_name = "<?php if(array_key_exists('template_name', $data)) echo $data['template_name']; ?>";
    var name = "<?php if(array_key_exists('name', $data)) echo $data['name']; ?>";
    var begin_date = "<?php if(array_key_exists('begin', $data)) echo substr($data['begin'], 0, 10); ?>";
    var begin_time = "<?php if(array_key_exists('begin', $data)) echo substr($data['begin'], -8, 5); ?>";
    var end_date = "<?php if(array_key_exists('end', $data)) echo substr($data['end'], 0, 10); ?>";
    var end_time = "<?php if(array_key_exists('end', $data)) echo substr($data['end'], -8, 5); ?>";
    var elapsed_days = "<?php if(array_key_exists('elapsed_days', $data)) echo $data['elapsed_days']; ?>";
    var repetition = "<?php if(array_key_exists('repetition', $data)) echo $data['repetition']; else echo '0000000'; ?>";
    var memo = "<?php if(array_key_exists('memo', $data)) echo $data['memo']; ?>";
    var is_duplication = "<?php if(array_key_exists('is_duplication', $data)) echo $data['is_duplication']; ?>";
    var color = "<?php if(array_key_exists('color', $data)) echo $data['color']; ?>";

    window.onload = function() {
        document.getElementById('status').value = status;
        document.getElementById('template_name').innerHTML = '<div class="form_element_text">' + template_name + '</div>';
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + name + '</div>';
        document.getElementById('begin').innerHTML = '<div class="form_element_text">' + begin_date + ' ' + begin_time + '</div>';
        document.getElementById('end').innerHTML = '<div class="form_element_text">' + end_date + ' ' + end_time + '</div>';
        document.getElementById('repetition_begin').innerHTML = '<div class="form_element_text">' + begin_time + '</div>';
        document.getElementById('repetition_end').innerHTML = '<div class="form_element_text">' + elapsed_days + ' 日後の ' + end_time + '</div>';
        document.getElementById('repetition').innerHTML = '<div class="form_element_text">' + viewRepetitionState(repetition) + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + memo + '</div>';
        document.getElementById('is_duplication').innerHTML = '<div class="form_element_text">' + (is_duplication ? 'はい' : 'いいえ') + '</div>';
        document.getElementById('color').value = color;

        document.getElementById('id').value = "<?php if(array_key_exists('id', $data)) echo $data['id']; else echo old('id'); ?>";
        document.getElementById('list_status').value = list_status;
        document.getElementById('list_display_style').value = list_display_style;
        document.getElementById('list_begin').value = list_begin;
        document.getElementById('list_end').value = list_end;
        document.getElementById('list_repetition').value = list_repetition;
        document.getElementById('list_status_back').value = list_status;
        document.getElementById('list_display_style_back').value = list_display_style;
        document.getElementById('list_begin_back').value = list_begin;
        document.getElementById('list_end_back').value = list_end;
        document.getElementById('list_repetition_back').value = list_repetition;

        document.getElementById('representation_style').value = representation_style;
        document.getElementById('view_from').value = view_from;
        document.getElementById('display_detail').value = display_detail;
        document.getElementById('representation_style_back').value = representation_style;
        document.getElementById('view_from_back').value = view_from;
        document.getElementById('display_detail_back').value = display_detail;

        <?php
        $begin_time_value = old('status') == 'normal' ? old('begin_time') : old('repetition_begin_time');
        $end_time_value = old('status') == 'normal' ? old('end_time') : old('repetition_end_time');
        if(count($errors) != 0) echo 'changeEdit("'.old('template_name').'", "'.old('name').'", "'.old('begin_date').'", "'.$begin_time_value.'", "'.old('end_date').'", "'.$end_time_value.'", "'.old('elapsed_days').'", "'.old('repetition').'", "'.old('memo').'", "'.old('is_duplication').'", "'.old('color').'")';
        else echo 'if(updated) {alert("更新しました。");}';
        ?>
    };

    function changeStateReptationEveryday(value){
        let days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        days.forEach(function(day) {
            document.getElementById("repetition_"+day).disabled = value.checked;
            document.getElementById("repetition_"+day).checked = value.checked;
        });
        if(value.checked) {
            document.getElementById('repetition').value = '1111111';
        }
        else {
            document.getElementById('repetition').value = '0000000';
        }
    }

    function changeStatus(status) {
        if(status == 'repetition') {
            document.getElementById('normal_begin_form').hidden = true;
            document.getElementById('normal_end_form').hidden = true;
            document.getElementById('repetition_form').hidden = false;
            document.getElementById('repetition_begin_form').hidden = false;
            document.getElementById('repetition_end_form').hidden = false;
        }
        else if(status == 'template') {
            document.getElementById('normal_begin_form').hidden = true;
            document.getElementById('normal_end_form').hidden = true;
            document.getElementById('template_form').hidden = false;
            document.getElementById('repetition_begin_form').hidden = false;
            document.getElementById('repetition_end_form').hidden = false;
        }
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

    function changeRepetitionEndLimit() {
        if(document.getElementById('repetition_end_date').value == 0) {
            document.getElementById("repetition_end_time").min = document.getElementById("repetition_begin_time").value;
        }
        else{
            document.getElementById("repetition_end_time").min = "";
        }
    }

    function changeRepetition(value, pos) {
        let prev = document.getElementById('list_repetition').value;
        let replaced = value.checked ? '1' : '0';
        document.getElementById('list_repetition').value = prev.slice(0, pos) + replaced + prev.slice(pos + 1);
    }

    function viewRepetitionState(repetition) {
        let days = ["日", "月", "火", "水", "木", "金", "土"];
        let state = "";
        days.forEach(function(day, index) {
            if(repetition.substr(index, 1) == '1') {
                state += day;
            }
            else {
                state += '　';
            }
            state += '　';
        });
        return state;
    }

    function listSetting(type) {
        let list_status_element = document.getElementById('list_status' + type);
        let list_display_style_element = document.getElementById('list_display_style' + type);
        let list_begin_element = document.getElementById('list_begin' + type);
        let list_end_element = document.getElementById('list_end' + type);
        let list_repetition_element = document.getElementById('list_repetition' + type);
        let representation_style_element = document.getElementById('representation_style' + type);
        let view_from_element = document.getElementById('view_from' + type);
        let display_detail_element = document.getElementById('display_detail' + type);
        if("{{ $is_from_list }}") {
            representation_style_element.remove();
            view_from_element.remove();
            display_detail_element.remove();
            if(list_status_element.value == 'normal') {
                if(list_display_style_element.value != 'custom') {
                    list_begin_element.remove();
                    list_end_element.remove();
                }
            }
            else {
                list_display_style_element.remove();
                list_begin_element.remove();
                list_end_element.remove();
            }
            if(list_status_element.value != 'repetition') {
                list_repetition_element.remove();
            }
        }
        else {
            list_status_element.remove();
            list_display_style_element.remove();
            list_begin_element.remove();
            list_end_element.remove();
            list_repetition_element.remove();
            if(representation_style_element.value == 'date') {
                display_detail_element.remove();
            }
            if(view_from_element.value === "") {
                view_from_element.remove();
            }
            document.getElementById('back').action = "{{ route('representation.index') }}";
        }
    }

    function toEdit() {
        changeEdit(template_name, name, begin_date, begin_time, end_date, end_time, elapsed_days, repetition, memo, is_duplication, color);
    }

    function toSave() {
        listSetting('');
        document.getElementById('form').submit();
    }

    function backList() {
        listSetting('_back');
        document.getElementById('back').submit();
    }

    function changeEdit(template_name, name, begin_date, begin_time, end_date, end_time, elapsed_days, repetition, memo, is_duplication, color) {
        let template_name_error = document.createElement('div');
        template_name_error.className = 'form_element_error';
        let template_name_error_content = document.createElement('span');
        template_name_error_content.style.color = 'red';
        template_name_error_content.role = 'alert';
        template_name_error_content.innerText = "<?php if($errors->has('template_name')) echo $errors->first('template_name'); ?>";
        template_name_error.appendChild(template_name_error_content);
        let template_name_base = document.createElement('div');
        template_name_base.className = 'form_element_input';
        let template_name_value = document.createElement('div');
        template_name_value.className = 'form_element_value';
        let template_name_content = document.createElement('input');
        template_name_content.type = 'text';
        template_name_content.name = 'template_name';
        template_name_content.className = 'form_element_text';
        template_name_content.value = template_name;
        template_name_value.appendChild(template_name_content);
        template_name_base.appendChild(template_name_value);
        document.getElementById('template_name_base').innerHTML = '';
        document.getElementById('template_name_base').appendChild(template_name_error);
        document.getElementById('template_name_base').appendChild(template_name_base);

        let name_error = document.createElement('div');
        name_error.className = 'form_element_error';
        let name_error_content = document.createElement('span');
        name_error_content.style.color = 'red';
        name_error_content.role = 'alert';
        name_error_content.innerText = "<?php if($errors->has('name')) echo $errors->first('name'); ?>";
        name_error.appendChild(name_error_content);
        let name_base = document.createElement('div');
        name_base.className = 'form_element_input';
        let name_value = document.createElement('div');
        name_value.className = 'form_element_value';
        let name_content = document.createElement('input');
        name_content.type = 'text';
        name_content.name = 'name';
        name_content.className = 'form_element_text';
        name_content.value = name;
        name_value.appendChild(name_content);
        name_base.appendChild(name_value);
        document.getElementById('name_base').innerHTML = '';
        document.getElementById('name_base').appendChild(name_error);
        document.getElementById('name_base').appendChild(name_base);

        let normal_begin_error = document.createElement('div');
        normal_begin_error.className = 'form_element_error';
        let normal_begin_error_content = document.createElement('span');
        normal_begin_error_content.style.color = 'red';
        normal_begin_error_content.role = 'alert';
        normal_begin_error_content.innerText = "<?php if($errors->has('begin')) echo $errors->first('begin'); ?>";
        normal_begin_error.appendChild(normal_begin_error_content);
        let normal_begin_base = document.createElement('div');
        normal_begin_base.className = 'form_element_input';
        let normal_begin_value = document.createElement('div');
        normal_begin_value.className = 'form_element_value';
        let normal_begin_time = document.createElement('div');
        normal_begin_time.className = 'form_element_time';
        let normal_begin_content_date = document.createElement('input');
        normal_begin_content_date.type = 'date';
        normal_begin_content_date.name = 'begin_date';
        normal_begin_content_date.value = begin_date;
        normal_begin_content_date.onchange = 'changeEndLimit()';
        let normal_begin_space = document.createElement('span');
        normal_begin_space.innerHTML = '&nbsp;';
        let normal_begin_content_time = document.createElement('input');
        normal_begin_content_time.type = 'time';
        normal_begin_content_time.name = 'begin_time';
        normal_begin_content_time.value = begin_time;
        normal_begin_content_time.onchange = 'changeEndLimit()';
        normal_begin_time.appendChild(normal_begin_content_date);
        normal_begin_time.appendChild(normal_begin_space);
        normal_begin_time.appendChild(normal_begin_content_time);
        normal_begin_value.appendChild(normal_begin_time);
        normal_begin_base.appendChild(normal_begin_value);
        document.getElementById('normal_begin_base').innerHTML = '';
        document.getElementById('normal_begin_base').appendChild(normal_begin_error);
        document.getElementById('normal_begin_base').appendChild(normal_begin_base);

        let normal_end_error = document.createElement('div');
        normal_end_error.className = 'form_element_error';
        let normal_end_error_content = document.createElement('span');
        normal_end_error_content.style.color = 'red';
        normal_end_error_content.role = 'alert';
        normal_end_error_content.innerText = "<?php if($errors->has('end')) echo $errors->first('end'); ?>";
        normal_end_error.appendChild(normal_end_error_content);
        let normal_end_base = document.createElement('div');
        normal_end_base.className = 'form_element_input';
        let normal_end_value = document.createElement('div');
        normal_end_value.className = 'form_element_value';
        let normal_end_time = document.createElement('div');
        normal_end_time.className = 'form_element_time';
        let normal_end_content_date = document.createElement('input');
        normal_end_content_date.type = 'date';
        normal_end_content_date.name = 'end_date';
        normal_end_content_date.value = end_date;
        normal_end_content_date.onchange = 'changeEndLimit()';
        let normal_end_space = document.createElement('span');
        normal_end_space.innerHTML = '&nbsp;';
        let normal_end_content_time = document.createElement('input');
        normal_end_content_time.type = 'time';
        normal_end_content_time.name = 'end_time';
        normal_end_content_time.value = end_time;
        normal_end_content_time.onchange = 'changeEndLimit()';
        normal_end_time.appendChild(normal_end_content_date);
        normal_end_time.appendChild(normal_end_space);
        normal_end_time.appendChild(normal_end_content_time);
        normal_end_value.appendChild(normal_end_time);
        normal_end_base.appendChild(normal_end_value);
        document.getElementById('normal_end_base').innerHTML = '';
        document.getElementById('normal_end_base').appendChild(normal_end_error);
        document.getElementById('normal_end_base').appendChild(normal_end_base);

        let repetition_begin_error = document.createElement('div');
        repetition_begin_error.className = 'form_element_error';
        let repetition_begin_error_content = document.createElement('span');
        repetition_begin_error_content.style.color = 'red';
        repetition_begin_error_content.role = 'alert';
        repetition_begin_error_content.innerText = "<?php if($errors->has('repetition_begin')) echo $errors->first('repetition_begin'); ?>";
        repetition_begin_error.appendChild(repetition_begin_error_content);
        let repetition_begin_base = document.createElement('div');
        repetition_begin_base.className = 'form_element_input';
        let repetition_begin_value = document.createElement('div');
        repetition_begin_value.className = 'form_element_value';
        let repetition_begin_time = document.createElement('div');
        repetition_begin_time.className = 'form_element_time';
        let repetition_begin_content_time = document.createElement('input');
        repetition_begin_content_time.type = 'time';
        repetition_begin_content_time.name = 'repetition_begin_time';
        repetition_begin_content_time.value = begin_time;
        repetition_begin_time.appendChild(repetition_begin_content_time);
        repetition_begin_value.appendChild(repetition_begin_time);
        repetition_begin_base.appendChild(repetition_begin_value);
        document.getElementById('repetition_begin_base').innerHTML = '';
        document.getElementById('repetition_begin_base').appendChild(repetition_begin_error);
        document.getElementById('repetition_begin_base').appendChild(repetition_begin_base);

        let repetition_end_error = document.createElement('div');
        repetition_end_error.className = 'form_element_error';
        let repetition_end_error_content = document.createElement('span');
        repetition_end_error_content.style.color = 'red';
        repetition_end_error_content.role = 'alert';
        repetition_end_error_content.innerText = "<?php if($errors->has('repetition_end')) echo $errors->first('repetition_end'); ?>";
        repetition_end_error.appendChild(repetition_end_error_content);
        let repetition_end_base = document.createElement('div');
        repetition_end_base.className = 'form_element_input';
        let repetition_end_value = document.createElement('div');
        repetition_end_value.className = 'form_element_value';
        let repetition_end_time = document.createElement('div');
        repetition_end_time.className = 'form_element_time';
        let repetition_end_content_date = document.createElement('input');
        repetition_end_content_date.type = 'number';
        repetition_end_content_date.name = 'repetition_end_date';
        repetition_end_content_date.value = elapsed_days;
        repetition_end_content_date.style.width = '20%';
        repetition_end_content_date.min = 0;
        repetition_end_content_date.onchange = 'changeRepetitionEndLimit()';
        let repetition_end_space = document.createElement('span');
        repetition_end_space.innerHTML = '&nbsp;日後の';
        let repetition_end_content_time = document.createElement('input');
        repetition_end_content_time.type = 'time';
        repetition_end_content_time.name = 'repetition_end_time';
        repetition_end_content_time.value = end_time;
        repetition_end_content_time.onchange = 'changeRepetitionEndLimit()';
        repetition_end_time.appendChild(repetition_end_content_date);
        repetition_end_time.appendChild(repetition_end_space);
        repetition_end_time.appendChild(repetition_end_content_time);
        repetition_end_value.appendChild(repetition_end_time);
        repetition_end_base.appendChild(repetition_end_value);
        document.getElementById('repetition_end_base').innerHTML = '';
        document.getElementById('repetition_end_base').appendChild(repetition_end_error);
        document.getElementById('repetition_end_base').appendChild(repetition_end_base);

        let repetition_error = document.createElement('div');
        repetition_error.className = 'form_element_error';
        let repetition_error_content = document.createElement('span');
        repetition_error_content.style.color = 'red';
        repetition_error_content.role = 'alert';
        repetition_error_content.innerText = "<?php if($errors->has('repetition_setting')) echo $errors->first('repetition_setting'); ?>";
        repetition_error.appendChild(repetition_error_content);
        let repetition_value = document.createElement('div');
        repetition_value.className = 'form_element_value';
        repetition_value.style.display = 'flex';
        let days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        let days_view = ["日", "月", "火", "水", "木", "金", "土"];
        days.forEach(function(day, index) {
            let repetition_value_content = document.createElement('div');
            repetition_value_content.className = 'form_element_checkbox';
            repetition_value_content.style.marginLeft = String(40 * index) + 'px';
            let repetition_value_content_checkbox = document.createElement('input');
            repetition_value_content_checkbox.type = 'checkbox';
            repetition_value_content_checkbox.name = 'repetition_' + day;
            repetition_value_content_checkbox.id = 'repetition_' + day;
            repetition_value_content_checkbox.checked = repetition.substr(index, 1) == '1';
            repetition_value_content_checkbox.onclick = 'changeRepetition(this, ' + String(index) +')';
            repetition_value_content.appendChild(repetition_value_content_checkbox);
            let repetition_value_content_label = document.createElement('label');
            repetition_value_content_label.htmlFor = 'repetition_' + day;
            repetition_value_content_label.innerHTML = ' ' + days_view[index];
            repetition_value_content.appendChild(repetition_value_content_label);
            repetition_value.appendChild(repetition_value_content);
        });
        let repetition_value_content_everyday = document.createElement('div');
        repetition_value_content_everyday.className = 'form_element_checkbox';
        repetition_value_content_everyday.style.marginLeft = '280px';
        let repetition_value_content_everyday_checkbox = document.createElement('input');
        repetition_value_content_everyday_checkbox.type = 'checkbox';
        repetition_value_content_everyday_checkbox.name = 'repetition_everyday';
        repetition_value_content_everyday_checkbox.checked = repetition == '1111111';
        repetition_value_content_everyday_checkbox.onclick = 'changeStateReptationEveryday(this)';
        repetition_value_content_everyday.appendChild(repetition_value_content_everyday_checkbox);
        let repetition_value_content_everyday_label = document.createElement('label');
        repetition_value_content_everyday_label.htmlFor = 'repetition_everyday';
        repetition_value_content_everyday_label.innerHTML = ' 毎日';
        repetition_value_content_everyday.appendChild(repetition_value_content_everyday_label);
        repetition_value.appendChild(repetition_value_content_everyday);
        let repetition_value_original = document.createElement('input');
        repetition_value_original.type = 'hidden';
        repetition_value_original.name = 'repetition';
        repetition_value_original.id = 'repetition';
        repetition_value_original.value = repetition;
        repetition_value.appendChild(repetition_value_original);
        document.getElementById('repetition_base').innerHTML = '';
        document.getElementById('repetition_base').appendChild(repetition_error);
        document.getElementById('repetition_base').appendChild(repetition_value);
        if(repetition == '1111111') {
            changeStateReptationEveryday(repetition_value_content_everyday_checkbox);
        }

        let memo_error = document.createElement('div');
        memo_error.className = 'form_element_error';
        let memo_error_content = document.createElement('span');
        memo_error_content.style.color = 'red';
        memo_error_content.role = 'alert';
        memo_error_content.innerText = "<?php if($errors->has('memo')) echo $errors->first('memo'); ?>";
        memo_error.appendChild(memo_error_content);
        let memo_base = document.createElement('div');
        memo_base.className = 'form_element_input';
        let memo_value = document.createElement('div');
        memo_value.className = 'form_element_value';
        let memo_content = document.createElement('input');
        memo_content.type = 'text';
        memo_content.name = 'memo';
        memo_content.className = 'form_element_text';
        memo_content.value = memo;
        memo_value.appendChild(memo_content);
        memo_base.appendChild(memo_value);
        document.getElementById('memo_base').innerHTML = '';
        document.getElementById('memo_base').appendChild(memo_error);
        document.getElementById('memo_base').appendChild(memo_base);

        let is_duplication_value = document.createElement('div');
        is_duplication_value.className = 'form_element_value';
        let is_duplication_content = document.createElement('input');
        is_duplication_content.type = 'checkbox';
        is_duplication_content.name = 'is_duplication';
        is_duplication_content.className = 'form_element_checkbox';
        is_duplication.checked = is_duplication ? true : false;
        is_duplication_value.appendChild(is_duplication_content);
        document.getElementById('is_duplication_base').innerHTML = '';
        document.getElementById('is_duplication_base').appendChild(is_duplication_value);

        document.getElementById('color').value = color;
        document.getElementById('color').disabled = false;

        document.getElementById('edit_button').hidden = true;
        document.getElementById('submit_button').hidden = false;
    }
</script>

@endsection