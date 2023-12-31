@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            ToDo 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" action="{{ route('todo.add') }}" class="registration_form_content" id="form">
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
            <div class="form_elements" id="normal_deadline_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="normal_deadline_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="deadline"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_deadline_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="repetition_deadline_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="repetition_deadline"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限日当日に行う<br>
                    </div>
                </div>
                <div class="form_element_input_no_error" id="is_today_base">
                    <div class="form_element_value" id="is_today"></div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        所要時間<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="required_time_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="required_time"></div>
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
                        <label for="priority_level">優先度</label><br>
                    </div>
                </div>
                <div class="form_element_input_no_error" id="priority_level_base">
                    <div class="form_element_value" id="priority_level"></div>
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
            <input type="hidden" name="from_representation" id="from_representation">
            <input type="hidden" name="display_today" id="display_today">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="GET" id="back" action="{{ route('todo.list') }}">
            <input type="hidden" name="list_status" id="list_status_back">
            <input type="hidden" name="list_display_style" id="list_display_style_back">
            <input type="hidden" name="list_begin" id="list_begin_back">
            <input type="hidden" name="list_end" id="list_end_back">
            <input type="hidden" name="list_repetition" id="list_repetition_back">
            <input type="hidden" name="from_representation" id="from_representation_back">
            <input type="hidden" name="display_today" id="display_today_back">
        </form>
    </div>
</div>

<script>
    var list_status = "<?php if(isset($list_status)) echo $list_status; ?>";
    var list_display_style = "<?php if(isset($list_display_style)) echo $list_display_style; ?>";
    var list_begin = "<?php if(isset($list_begin)) echo $list_begin; ?>";
    var list_end = "<?php if(isset($list_end)) echo $list_end; ?>";
    var list_repetition = "<?php if(isset($list_repetition)) echo $list_repetition; ?>";
    var from_representation = "<?php if(isset($from_representation)) echo $from_representation; ?>";
    var display_today = "<?php if(isset($display_today)) echo $display_today; ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    changeStatus("{{ $data['status'] }}");

    var status = "<?php if(array_key_exists('status', $data)) echo $data['status']; ?>";
    var template_name = "<?php if(array_key_exists('template_name', $data)) echo $data['template_name']; ?>";
    var name = "<?php if(array_key_exists('name', $data)) echo $data['name']; ?>";
    var deadline_date = "<?php if(array_key_exists('deadline', $data)) echo substr($data['deadline'], 0, 10); ?>";
    var deadline_time = "<?php if(array_key_exists('deadline', $data)) echo substr($data['deadline'], -8, 5); ?>";
    var type = "<?php if(array_key_exists('type', $data)) echo $data['type']; ?>";
    var required_hour = <?php if(array_key_exists('required_minutes', $data)) echo (string)(ceil((int)$data['required_minutes'] / 60)); ?>;
    var required_minute = <?php if(array_key_exists('required_minutes', $data)) echo (string)((int)$data['required_minutes'] % 60); ?>;
    var repetition = "<?php if(array_key_exists('repetition', $data)) echo $data['repetition']; else echo '0000000'; ?>";
    var memo = "<?php if(array_key_exists('memo', $data)) echo $data['memo']; ?>";
    var priority_level = "<?php if(array_key_exists('priority_level', $data)) echo $data['priority_level']; ?>";
    var color = "<?php if(array_key_exists('color', $data)) echo $data['color']; ?>";

    window.onload = function() {
        document.getElementById('status').value = status;
        document.getElementById('template_name').innerHTML = '<div class="form_element_text">' + template_name + '</div>';
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + name + '</div>';
        document.getElementById('deadline').innerHTML = '<div class="form_element_text">' + deadline_date + ' ' + deadline_time + '</div>';
        document.getElementById('repetition_deadline').innerHTML = '<div class="form_element_text">' + deadline_time + '</div>';
        document.getElementById('is_today').innerHTML = '<div class="form_element_text">' + (type == 'today' ? 'はい' : 'いいえ') + '</div>';
        document.getElementById('required_time').innerHTML = '<div class="form_element_text">' + required_hour + ' 時間 ' + required_minute + ' 分' + '</div>';
        document.getElementById('repetition').innerHTML = '<div class="form_element_text">' + viewRepetitionState(repetition) + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + memo + '</div>';
        document.getElementById('priority_level').innerHTML = '<div class="form_element_text">' + priority_level + '</div>';
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

        document.getElementById('from_representation').value = from_representation;
        document.getElementById('from_representation_back').value = from_representation;
        document.getElementById('display_today').value = display_today;
        document.getElementById('display_today_back').value = display_today;

        <?php
        $deadline_time_value = old('status') == 'normal' ? old('deadline_time') : old('repetition_deadline_time');
        if(count($errors) != 0) echo 'changeEdit("'.old('template_name').'", "'.old('name').'", "'.old('deadline_date').'", "'.$deadline_time_value.'", "'.old('type').'", "'.old('required_hour').'", "'.old('required_minute').'", "'.old('repetition').'", "'.old('memo').'", "'.old('priority_level').'", "'.old('color').'")';
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

    function changeRequiredMinuteLimit() {
        if(document.getElementById("required_hour").value == 0) {
            document.getElementById("required_minute").min = 15;
            if(document.getElementById("required_minute").value == 0) {
                document.getElementById("required_minute").value = 15;
            }
        }
        else {
            document.getElementById("required_minute").min = 0;
        }
        if(document.getElementById("required_minute").value == "") {
            document.getElementById("required_minute").value = 0;
        }
    }

    function changeStatus(status) {
        if(status == 'repetition') {
            document.getElementById('normal_deadline_form').hidden = true;
            document.getElementById('repetition_form').hidden = false;
            document.getElementById('repetition_deadline_form').hidden = false;
        }
        else if(status == 'template') {
            document.getElementById('normal_deadline_form').hidden = true;
            document.getElementById('template_form').hidden = false;
            document.getElementById('repetition_deadline_form').hidden = false;
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
        let from_representation_element = document.getElementById('from_representation' + type);
        let display_today_element = document.getElementById('display_today' + type);
        if("{{ $is_from_list }}") {
            from_representation_element.remove();
            display_today_element.remove();
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
            if("{{ $is_from_schedule }}") {
                display_today_element.remove();
                if(type == '_back') from_representation_element.remove();
                document.getElementById('back').action = "{{ route('representation.todo') }}";
            }
            else {
                from_representation_element.remove();
                if(type == '_back') display_today_element.remove();
                document.getElementById('back').action = "{{ route('representation.today') }}";
            }
        }
    }

    function toEdit() {
        changeEdit(template_name, name, deadline_date, deadline_time, type, required_hour, required_minute, repetition, memo, priority_level, color);
    }

    function toSave() {
        listSetting('');
        document.getElementById('form').submit();
    }

    function backList() {
        listSetting('_back');
        document.getElementById('back').submit();
    }

    function changeEdit(template_name, name, deadline_date, deadline_time, type, required_hour, required_minute, repetition, memo, priority_level, color) {
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

        let normal_deadline_error = document.createElement('div');
        normal_deadline_error.className = 'form_element_error';
        let normal_deadline_error_content = document.createElement('span');
        normal_deadline_error_content.style.color = 'red';
        normal_deadline_error_content.role = 'alert';
        normal_deadline_error_content.innerText = "<?php if($errors->has('deadline')) echo $errors->first('deadline'); ?>";
        normal_deadline_error.appendChild(normal_deadline_error_content);
        let normal_deadline_base = document.createElement('div');
        normal_deadline_base.className = 'form_element_input';
        let normal_deadline_value = document.createElement('div');
        normal_deadline_value.className = 'form_element_value';
        let normal_deadline_time = document.createElement('div');
        normal_deadline_time.className = 'form_element_time';
        let normal_deadline_content_date = document.createElement('input');
        normal_deadline_content_date.type = 'date';
        normal_deadline_content_date.name = 'deadline_date';
        normal_deadline_content_date.value = deadline_date;
        let normal_deadline_space = document.createElement('span');
        normal_deadline_space.innerHTML = '&nbsp;';
        let normal_deadline_content_time = document.createElement('input');
        normal_deadline_content_time.type = 'time';
        normal_deadline_content_time.name = 'deadline_time';
        normal_deadline_content_time.value = deadline_time;
        normal_deadline_time.appendChild(normal_deadline_content_date);
        normal_deadline_time.appendChild(normal_deadline_space);
        normal_deadline_time.appendChild(normal_deadline_content_time);
        normal_deadline_value.appendChild(normal_deadline_time);
        normal_deadline_base.appendChild(normal_deadline_value);
        document.getElementById('normal_deadline_base').innerHTML = '';
        document.getElementById('normal_deadline_base').appendChild(normal_deadline_error);
        document.getElementById('normal_deadline_base').appendChild(normal_deadline_base);

        let repetition_deadline_error = document.createElement('div');
        repetition_deadline_error.className = 'form_element_error';
        let repetition_deadline_error_content = document.createElement('span');
        repetition_deadline_error_content.style.color = 'red';
        repetition_deadline_error_content.role = 'alert';
        repetition_deadline_error_content.innerText = "<?php if($errors->has('repetition_deadline')) echo $errors->first('repetition_deadline'); ?>";
        repetition_deadline_error.appendChild(repetition_deadline_error_content);
        let repetition_deadline_base = document.createElement('div');
        repetition_deadline_base.className = 'form_element_input';
        let repetition_deadline_value = document.createElement('div');
        repetition_deadline_value.className = 'form_element_value';
        let repetition_deadline_time = document.createElement('div');
        repetition_deadline_time.className = 'form_element_time';
        let repetition_deadline_content_time = document.createElement('input');
        repetition_deadline_content_time.type = 'time';
        repetition_deadline_content_time.name = 'repetition_deadline_time';
        repetition_deadline_content_time.value = deadline_time;
        repetition_deadline_time.appendChild(repetition_deadline_content_time);
        repetition_deadline_value.appendChild(repetition_deadline_time);
        repetition_deadline_base.appendChild(repetition_deadline_value);
        document.getElementById('repetition_deadline_base').innerHTML = '';
        document.getElementById('repetition_deadline_base').appendChild(repetition_deadline_error);
        document.getElementById('repetition_deadline_base').appendChild(repetition_deadline_base);

        let is_today_value = document.createElement('div');
        is_today_value.className = 'form_element_value';
        let is_today_content = document.createElement('input');
        is_today_content.type = 'checkbox';
        is_today_content.name = 'is_today';
        is_today_content.className = 'form_element_checkbox';
        is_today.checked = type == 'today';
        is_today_value.appendChild(is_today_content);
        document.getElementById('is_today_base').innerHTML = '';
        document.getElementById('is_today_base').appendChild(is_today_value);

        let required_time_error = document.createElement('div');
        required_time_error.className = 'form_element_error';
        let required_time_error_content = document.createElement('span');
        required_time_error_content.style.color = 'red';
        required_time_error_content.role = 'alert';
        required_time_error_content.innerText = "<?php if($errors->has('required_time')) echo $errors->first('required_time'); ?>";
        required_time_error.appendChild(required_time_error_content);
        let required_time_base = document.createElement('div');
        required_time_base.className = 'form_element_input';
        let required_time_value = document.createElement('div');
        required_time_value.className = 'form_element_value';
        let required_time_time = document.createElement('div');
        required_time_time.className = 'form_element_time';
        let required_time_content_hour = document.createElement('input');
        required_time_content_hour.type = 'number';
        required_time_content_hour.name = 'required_hour';
        required_time_content_hour.id = 'required_hour';
        required_time_content_hour.value = required_hour;
        required_time_content_hour.min = 0;
        required_time_content_hour.style.width = '20%';
        required_time_content_hour.onchange = 'changeRequiredMinuteLimit()';
        let required_time_hour_sub = document.createElement('span');
        required_time_hour_sub.innerHTML = '時間&nbsp;';
        let required_time_content_minute = document.createElement('input');
        required_time_content_minute.type = 'number';
        required_time_content_minute.name = 'required_minute';
        required_time_content_minute.id = 'required_minute';
        required_time_content_minute.value = required_minute;
        required_time_content_minute.min = 0;
        required_time_content_minute.max = 45;
        required_time_content_minute.step = 15;
        required_time_content_minute.style.width = '20%';
        let required_time_minute_sub = document.createElement('span');
        required_time_minute_sub.innerHTML = '分';
        required_time_time.appendChild(required_time_content_hour);
        required_time_time.appendChild(required_time_hour_sub);
        required_time_time.appendChild(required_time_content_minute);
        required_time_time.appendChild(required_time_minute_sub);
        required_time_value.appendChild(required_time_time);
        required_time_base.appendChild(required_time_value);
        document.getElementById('required_time_base').innerHTML = '';
        document.getElementById('required_time_base').appendChild(required_time_error);
        document.getElementById('required_time_base').appendChild(required_time_base);
        changeRequiredMinuteLimit();

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

        let priority_level_value = document.createElement('div');
        priority_level_value.className = 'form_element_value';
        let priority_level_list = document.createElement('select');
        priority_level_list.name = 'priority_level';
        priority_level_list.id = 'priority_level';
        priority_level_list.className = 'form_element_text';
        for(let i = 5; i >= 1; i--) {
            let priority_level_list_content = document.createElement('option');
            priority_level_list_content.value = String(i);
            priority_level_list_content.innerText = String(i);
            if(priority_level == String(i)) {
                priority_level_list_content.checked = true;
            }
            priority_level_list.appendChild(priority_level_list_content);
        }
        priority_level_value.appendChild(priority_level_list);
        document.getElementById('priority_level_base').innerHTML = '';
        document.getElementById('priority_level_base').appendChild(priority_level_value);

        document.getElementById('color').value = color;
        document.getElementById('color').disabled = false;

        document.getElementById('edit_button').hidden = true;
        document.getElementById('submit_button').hidden = false;
    }
</script>

@endsection