@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            ワークフロー 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" action="{{ route('workflow.add') }}" class="registration_form_content" id="form">
            @csrf
        
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
            <div class="form_elements" id="deadline_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限<br>
                    </div>
                </div>
                <div class="form_element_input_base" id="deadline_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="deadline"></div>
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

            <input type="hidden" name="list_display_style" id="list_display_style">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
            <input type="hidden" name="from_representation" id="from_representation">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="GET" id="back" action="{{ route('workflow.list') }}">
            <input type="hidden" name="list_display_style" id="list_display_style_back">
            <input type="hidden" name="list_begin" id="list_begin_back">
            <input type="hidden" name="list_end" id="list_end_back">
            <input type="hidden" name="from_representation" id="from_representation_back">
        </form>
    </div>
</div>

<script>
    var list_display_style = "<?php if(isset($list_display_style)) echo $list_display_style; ?>";
    var list_begin = "<?php if(isset($list_begin)) echo $list_begin; ?>";
    var list_end = "<?php if(isset($list_end)) echo $list_end; ?>";
    var list_repetition = "<?php if(isset($list_repetition)) echo $list_repetition; ?>";
    var from_representation = "<?php if(isset($from_representation)) echo $from_representation; ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    var name = "<?php if(array_key_exists('name', $data)) echo $data['name']; ?>";
    var deadline_date = "<?php if(array_key_exists('deadline', $data)) echo substr($data['deadline'], 0, 10); ?>";
    var deadline_time = "<?php if(array_key_exists('deadline', $data)) echo substr($data['deadline'], -8, 5); ?>";
    var memo = "<?php if(array_key_exists('memo', $data)) echo $data['memo']; ?>";
    var color = "<?php if(array_key_exists('color', $data)) echo $data['color']; ?>";

    window.onload = function() {
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + name + '</div>';
        document.getElementById('deadline').innerHTML = '<div class="form_element_text">' + deadline_date + ' ' + deadline_time + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + memo + '</div>';
        document.getElementById('color').value = color;

        document.getElementById('id').value = "<?php if(array_key_exists('id', $data)) echo $data['id']; else echo old('id'); ?>";
        document.getElementById('list_display_style').value = list_display_style;
        document.getElementById('list_begin').value = list_begin;
        document.getElementById('list_end').value = list_end;
        document.getElementById('list_display_style_back').value = list_display_style;
        document.getElementById('list_begin_back').value = list_begin;
        document.getElementById('list_end_back').value = list_end;

        document.getElementById('from_representation').value = from_representation;
        document.getElementById('from_representation_back').value = from_representation;

        <?php
        if(count($errors) != 0) echo 'changeEdit("'.old('name').'", "'.old('deadline_date').'", "'.old('deadline_time').'", "'.old('memo').'", "'.old('color').'")';
        else echo 'if(updated) {alert("更新しました。");}';
        ?>
    };

    function listSetting(type) {
        let list_display_style_element = document.getElementById('list_display_style' + type);
        let list_begin_element = document.getElementById('list_begin' + type);
        let list_end_element = document.getElementById('list_end' + type);
        let from_representation_element = document.getElementById('from_representation' + type);
        if("{{ $is_from_list }}") {
            from_representation_element.remove();
            if(list_display_style_element.value != 'custom') {
                list_begin_element.remove();
                list_end_element.remove();
            }
        }
        else {
            list_display_style_element.remove();
            list_begin_element.remove();
            list_end_element.remove();
            if(type == '_back') from_representation_element.remove();
            document.getElementById('back').action = "{{ route('representation.todo') }}";
        }
    }

    function toEdit() {
        changeEdit(name, deadline_date, deadline_time, memo, color);
    }

    function toSave() {
        listSetting('');
        document.getElementById('form').submit();
    }

    function backList() {
        listSetting('_back');
        document.getElementById('back').submit();
    }

    function changeEdit(name, deadline_date, deadline_time, memo, color) {
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

        let deadline_error = document.createElement('div');
        deadline_error.className = 'form_element_error';
        let deadline_error_content = document.createElement('span');
        deadline_error_content.style.color = 'red';
        deadline_error_content.role = 'alert';
        deadline_error_content.innerText = "<?php if($errors->has('deadline')) echo $errors->first('deadline'); ?>";
        deadline_error.appendChild(deadline_error_content);
        let deadline_base = document.createElement('div');
        deadline_base.className = 'form_element_input';
        let deadline_value = document.createElement('div');
        deadline_value.className = 'form_element_value';
        let deadline_time_display = document.createElement('div');
        deadline_time_display.className = 'form_element_time';
        let deadline_content_date = document.createElement('input');
        deadline_content_date.type = 'date';
        deadline_content_date.name = 'deadline_date';
        deadline_content_date.value = deadline_date;
        let deadline_space = document.createElement('span');
        deadline_space.innerHTML = '&nbsp;';
        let deadline_content_time = document.createElement('input');
        deadline_content_time.type = 'time';
        deadline_content_time.name = 'deadline_time';
        deadline_content_time.value = deadline_time;
        deadline_time_display.appendChild(deadline_content_date);
        deadline_time_display.appendChild(deadline_space);
        deadline_time_display.appendChild(deadline_content_time);
        deadline_value.appendChild(deadline_time_display);
        deadline_base.appendChild(deadline_value);
        document.getElementById('deadline_base').innerHTML = '';
        document.getElementById('deadline_base').appendChild(deadline_error);
        document.getElementById('deadline_base').appendChild(deadline_base);

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

        document.getElementById('color').value = color;
        document.getElementById('color').disabled = false;

        document.getElementById('edit_button').hidden = true;
        document.getElementById('submit_button').hidden = false;
    }
</script>

@endsection