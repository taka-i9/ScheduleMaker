@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            ワークフロー 編集・管理
        </div>
    </div>
    <div class="view_form_edit">
        <form method="get" id="list_setting" action="{{ route('workflow.list') }}">
            <div style="display: flex;">
                <input type="submit" onclick="listSetting('')" value="更新">
            </div>
            <div id="detail_normal" style="display: flex;" hidden>
                <input type="checkbox" id="from_now" onclick="changeDisplayStyle('from_now')"><label for="from_now">現在以降</label>
                &nbsp;
                <input type="checkbox" id="today" onclick="changeDisplayStyle('today')"><label for="today">今日</label>
                &nbsp;
                <input type="checkbox" id="this_week" onclick="changeDisplayStyle('this_week')"><label for="this_week">今週</label>
                &nbsp;
                <input type="checkbox" id="this_month" onclick="changeDisplayStyle('this_month')"><label for="this_month">今月</label>
                &nbsp;
                <input type="checkbox" id="all" onclick="changeDisplayStyle('all')"><label for="all">全て</label>
                &nbsp;
                <input type="checkbox" id="custom" onclick="changeDisplayStyle('custom')"><label for="custom">カスタム</label>
            </div>
            <div id="detail_custom" style="display: flex;" hidden>
                <input type="date" id="custom_begin" onchange="changeCustomBegin()">
                ~
                <input type="date" id="custom_end" onchange="changeCustomEnd()">
            </div>
            <input type="hidden" name="list_display_style" id="list_display_style" value="from_now">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
        </form>
    </div>
    <div class="view_form_content" id="view_form_content">
        <form method="get" id="form_detail" action="{{ route('workflow.detail') }}">
            <input type="hidden" name="list_display_style" id="list_display_style_detail">
            <input type="hidden" name="list_begin" id="list_begin_detail">
            <input type="hidden" name="list_end" id="list_end_detail">
        </form>
        <form method="post" id="form_delete" action="{{ route('workflow.delete') }}">
            @csrf
            <input type="hidden" name="list_display_style" id="list_display_style_delete">
            <input type="hidden" name="list_begin" id="list_begin_delete">
            <input type="hidden" name="list_end" id="list_end_delete">
        </form>
        <form method="post" id="form_edit" action="{{ route('workflow.edit_form') }}">
            @csrf
        </form>
    </div>

</div>

<script>
    let workflow_data = [<?php 
        for($i = 0; $i < count($workflow_data); $i++) {
            print '{';
            foreach($workflow_data[$i] as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        } 
    ?>];

    window.onload = function() {
        document.getElementById('{{ $list_display_style }}').checked = true;
        document.getElementById('detail_normal').hidden = false;
        document.getElementById('list_display_style').value = '{{ $list_display_style }}';
        if('{{ $list_display_style }}' == 'custom') {
            document.getElementById('detail_custom').hidden = false;    
        }
        document.getElementById('list_begin').value = '{{ $list_begin }}';
        document.getElementById('list_end').value = '{{ $list_end }}';
        document.getElementById('custom_begin').value = '{{ $list_begin }}';
        document.getElementById('custom_end').value = '{{ $list_end }}';
        changeCustomBegin();
        changeCustomEnd();
        displayNormal('ワークフロー名', '期限', '0');
        for(let i = 0; i < workflow_data.length; i++) {
            displayNormal(workflow_data[i]['name'], workflow_data[i]['deadline'], workflow_data[i]['id']);
        }

        document.getElementById('list_display_style_detail').value = '{{ $list_display_style }}';
        document.getElementById('list_begin_detail').value = '{{ $list_begin }}';
        document.getElementById('list_end_detail').value = '{{ $list_end }}';

        document.getElementById('list_display_style_delete').value = '{{ $list_display_style }}';
        document.getElementById('list_begin_delete').value = '{{ $list_begin }}';
        document.getElementById('list_end_delete').value = '{{ $list_end }}';

        if("{{ $deleted }}") {
            alert("削除が完了しました。");
        }
    };

    function display_date(date) {
        return date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate();
    }

    function changeDisplayStyle(type) {
        let list_display_style = document.getElementById('list_display_style');
        let prev = document.getElementById(list_display_style.value);
        let list_begin = document.getElementById('list_begin');
        let list_end = document.getElementById('list_end');
        if(list_display_style.value == type) return;
        prev.checked = false;
        if(list_display_style.value == 'custom') {
            document.getElementById('detail_custom').hidden = true;
        }
        list_display_style.value = type;
        let today = new Date();
        let begin_date = new Date();
        let end_date = new Date();
        if(type == 'this_week') {
            begin_date.setDate(today.getDate() - today.getDay());
            end_date.setDate(today.getDate() + 6 - today.getDay());
        }
        else if(type == 'this_month') {
            begin_date.setDate(today.getDate() - (today.getDate() - 1));
            end_date.setMonth(today.getMonth() + 1);
            end_date.setDate(end_date.getDate() - end_date.getDate());
        }
        else if(type == 'custom') {
            begin_date = new Date(document.getElementById('custom_begin').value);
            end_date = new Date(document.getElementById('custom_end').value);
            document.getElementById('detail_custom').hidden = false;
        }
        list_begin.value = display_date(begin_date);
        list_end.value = display_date(end_date);
    }

    function changeCustomBegin() {
        document.getElementById('custom_end').min = document.getElementById('custom_begin').value;
        if(new Date(document.getElementById('custom_end').min) > new Date(document.getElementById('custom_end').value)) {
            document.getElementById('custom_end').value = document.getElementById('custom_end').min;
        }
        if(document.getElementById('list_display_style').value == 'custom') {
            document.getElementById('list_begin').value = document.getElementById('custom_begin').value;
        }
    }

    function changeCustomEnd() {
        document.getElementById('custom_begin').max = document.getElementById('custom_end').value;
        if(new Date(document.getElementById('custom_begin').max) < new Date(document.getElementById('custom_begin').value)) {
            document.getElementById('custom_begin').value = document.getElementById('custom_begin').max;
        }
        if(document.getElementById('list_display_style').value == 'custom') {
            document.getElementById('list_end').value = document.getElementById('custom_end').value;
        }
    }

    function listSetting(type) {
        let list_display_style_element = document.getElementById('list_display_style' + type);
        let list_begin_element = document.getElementById('list_begin' + type);
        let list_end_element = document.getElementById('list_end' + type);
        if(list_display_style_element.value != 'custom') {
            list_begin_element.remove();
            list_end_element.remove();
        }
    }

    function displayNormal(name, deadline, id) {
        let content = document.createElement('div');
        content.className = 'view_content';
        let name_parent = document.createElement('div');
        name_parent.className = 'view_schedule_contents_parent';
        let name_child = document.createElement('div');
        name_child.className = 'view_contents_child';
        name_child.innerText = name;
        name_parent.appendChild(name_child);
        let deadline_parent = document.createElement('div');
        deadline_parent.className = 'view_schedule_contents_parent';
        let deadline_child = document.createElement('div');
        deadline_child.className = 'view_contents_child';
        deadline_child.innerText = deadline;
        deadline_parent.appendChild(deadline_child);
        let edit_parent = document.createElement('div');
        edit_parent.className = 'view_schedule_contents_parent';
        let edit_child = document.createElement('div');
        edit_child.className = 'view_contents_child';
        if(id == '0') {
            edit_child.innerText = '編集';
        }
        else {
            let edit_button = document.createElement('button');
            edit_button.type = 'button';
            edit_button.innerText = '編集';
            edit_button.addEventListener('click', (event) => {
                toEditForm(id);
            });
            edit_child.appendChild(edit_button);
        }
        edit_parent.appendChild(edit_child);
        let detail_parent = document.createElement('div');
        detail_parent.className = 'view_detail_parent';
        let detail_child = document.createElement('div');
        detail_child.className = 'view_contents_child';
        if(id == '0') {
            detail_child.innerText = '詳細';
        }
        else {
            let detail_button = document.createElement('button');
            detail_button.type = 'button';
            detail_button.innerText = '詳細';
            detail_button.addEventListener('click', (event) => {
                showDetailSchedule(id);
            });
            detail_child.appendChild(detail_button);
        }
        detail_parent.appendChild(detail_child);
        let delete_parent = document.createElement('div');
        delete_parent.className = 'view_delete_parent';
        let delete_child = document.createElement('div');
        delete_child.className = 'view_contents_child';
        if(id == '0') {
            delete_child.innerText = '削除';
        }
        else {
            let delete_button = document.createElement('button');
            delete_button.type = 'button';
            delete_button.innerText = '削除';
            delete_button.addEventListener('click', (event) => {
                deleteSchedule(id);
            });
            delete_child.appendChild(delete_button);
        }
        delete_parent.appendChild(delete_child);
        content.appendChild(name_parent);
        content.appendChild(deadline_parent);
        content.appendChild(edit_parent);
        content.appendChild(detail_parent);
        content.appendChild(delete_parent);
        document.getElementById('view_form_content').appendChild(content);
    }

    function showDetailSchedule(id) {
        listSetting('_detail');
        let detail_form_id = document.createElement('input');
        detail_form_id.type = 'hidden';
        detail_form_id.name = 'id';
        detail_form_id.value = id;
        document.getElementById('form_detail').appendChild(detail_form_id);
        document.getElementById('form_detail').submit();
    }

    function deleteSchedule(id) {
        if(window.confirm('この要素を削除します。\nよろしいですか。')) {
            listSetting('_delete');
            let delete_form_id = document.createElement('input');
            delete_form_id.type = 'hidden';
            delete_form_id.name = 'id';
            delete_form_id.value = id;
            document.getElementById('form_delete').appendChild(delete_form_id);
            document.getElementById('form_delete').submit();
        }
    }

    function toEditForm(id) {
        let edit_form_id = document.createElement('input');
        edit_form_id.type = 'hidden';
        edit_form_id.name = 'id';
        edit_form_id.value = id;
        document.getElementById('form_edit').appendChild(edit_form_id);
        document.getElementById('form_edit').submit();
    }
</script>

@endsection