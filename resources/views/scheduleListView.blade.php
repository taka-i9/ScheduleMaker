@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            スケジュール 編集・管理
        </div>
    </div>
    <div class="view_form_edit">
        <form method="post" action="{{ route('schedule.list') }}">
            @csrf
            <div style="display: flex;">
                <input type="checkbox" id="normal_view" onclick="changeStatus('normal')"><label for="normal_view">スケジュール</label>
                &nbsp;
                <input type="checkbox" id="repetition_view" onclick="changeStatus('repetition')"><label for="repetition_view">繰り返し</label>
                &nbsp;
                <input type="checkbox" id="template_view" onclick="changeStatus('template')"><label for="template_view">テンプレート</label>
                &nbsp;
                <input type="submit" value="更新">
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
            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_display_style" id="list_display_style" value="from_now">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
        </form>
    </div>
    <div class="view_form_content" id="view_form_content">
        <form method="post" id="form_detail" action="{{ route('schedule.detail') }}">
            @csrf
        </form>
        <form method="post" id="form_delete" action="{{ route('schedule.delete') }}">
            @csrf
        </form>
    </div>

</div>

<script>
    let schedule_data = [<?php 
        for($i = 0; $i < count($schedule_data); $i++) {
            print '{';
            foreach($schedule_data[$i] as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        } 
    ?>];

    window.onload = function() {
        document.getElementById('{{ $list_status }}' + '_view').checked = true;
        document.getElementById('list_status').value = '{{ $list_status }}';
        document.getElementById('{{ $list_display_style }}').checked = true;
        if('{{ $list_status }}' == 'normal') {
            document.getElementById('detail_normal').hidden = false;
            document.getElementById('list_display_style').value = '{{ $list_display_style }}';
            if('{{ $list_display_style }}' == 'custom') {
                document.getElementById('detail_custom').hidden = false;    
            }
        }
        document.getElementById('list_begin').value = '{{ $list_begin }}';
        document.getElementById('list_end').value = '{{ $list_end }}';
        document.getElementById('custom_begin').value = '{{ $list_begin }}';
        document.getElementById('custom_end').value = '{{ $list_end }}';
        changeCustomBegin();
        changeCustomEnd();
        if('{{ $list_status }}' == 'normal') {
            displayNormal('スケジュール名', '開始時刻', '終了時刻', '0');
            for(let i = 0; i < schedule_data.length; i++) {
                displayNormal(schedule_data[i]['name'], schedule_data[i]['begin_time'], schedule_data[i]['end_time'], schedule_data[i]['id']);
            }
        }
        let detail_form_status = document.createElement('input');
        detail_form_status.type = 'hidden';
        detail_form_status.name = 'list_status';
        detail_form_status.value = '{{ $list_status }}';
        let detail_form_display_style = document.createElement('input');
        detail_form_display_style.type = 'hidden';
        detail_form_display_style.name = 'list_display_style';
        detail_form_display_style.value = '{{ $list_display_style }}';
        let detail_form_begin = document.createElement('input');
        detail_form_begin.type = 'hidden';
        detail_form_begin.name = 'list_begin';
        detail_form_begin.value = '{{ $list_begin }}';
        let detail_form_end = document.createElement('input');
        detail_form_end.type = 'hidden';
        detail_form_end.name = 'list_end';
        detail_form_end.value = '{{ $list_end }}';
        document.getElementById('form_detail').appendChild(detail_form_status);
        document.getElementById('form_detail').appendChild(detail_form_display_style);
        document.getElementById('form_detail').appendChild(detail_form_begin);
        document.getElementById('form_detail').appendChild(detail_form_end);
        let delete_form_status = document.createElement('input');
        delete_form_status.type = 'hidden';
        delete_form_status.name = 'list_status';
        delete_form_status.value = '{{ $list_status }}';
        let delete_form_display_style = document.createElement('input');
        delete_form_display_style.type = 'hidden';
        delete_form_display_style.name = 'list_display_style';
        delete_form_display_style.value = '{{ $list_display_style }}';
        let delete_form_begin = document.createElement('input');
        delete_form_begin.type = 'hidden';
        delete_form_begin.name = 'list_begin';
        delete_form_begin.value = '{{ $list_begin }}';
        let delete_form_end = document.createElement('input');
        delete_form_end.type = 'hidden';
        delete_form_end.name = 'list_end';
        delete_form_end.value = '{{ $list_end }}';
        document.getElementById('form_delete').appendChild(delete_form_status);
        document.getElementById('form_delete').appendChild(delete_form_display_style);
        document.getElementById('form_delete').appendChild(delete_form_begin);
        document.getElementById('form_delete').appendChild(delete_form_end);
    };
    

    function changeStatus(type) {
        let list_status = document.getElementById('list_status');
        let prev = document.getElementById(list_status.value + '_view');
        if(list_status.value == type) return;
        prev.checked = false;
        if(list_status.value == 'normal') {
            document.getElementById('detail_normal').hidden = true;
            document.getElementById('detail_custom').hidden = true;
        }
        else if(list_status.value == 'repetition') {
        }
        list_status.value = type;
        if(type == 'normal') {
            document.getElementById('detail_normal').hidden = false;
            if(document.getElementById('list_display_style').value == 'custom'){
                document.getElementById('detail_custom').hidden = false;
            }
        }
        else if(type == 'repetition') {
        }
    }

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

    function displayNormal(name, begin_time, end_time, id) {
        let content = document.createElement('div');
        content.className = 'view_content';
        let name_parent = document.createElement('div');
        name_parent.className = 'view_schedule_contents_parent';
        let name_child = document.createElement('div');
        name_child.className = 'view_contents_child';
        name_child.innerText = name;
        name_parent.appendChild(name_child);
        let begin_time_parent = document.createElement('div');
        begin_time_parent.className = 'view_schedule_contents_parent';
        let begin_time_child = document.createElement('div');
        begin_time_child.className = 'view_contents_child';
        begin_time_child.innerText = begin_time;
        begin_time_parent.appendChild(begin_time_child);
        let end_time_parent = document.createElement('div');
        end_time_parent.className = 'view_schedule_contents_parent';
        let end_time_child = document.createElement('div');
        end_time_child.className = 'view_contents_child';
        end_time_child.innerText = end_time;
        end_time_parent.appendChild(end_time_child);
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
        content.appendChild(begin_time_parent);
        content.appendChild(end_time_parent);
        content.appendChild(detail_parent);
        content.appendChild(delete_parent);
        document.getElementById('view_form_content').appendChild(content);
    }

    function showDetailSchedule(id) {
        let detail_form_id = document.createElement('input');
        detail_form_id.type = 'hidden';
        detail_form_id.name = 'id';
        detail_form_id.value = id;
        document.getElementById('form_detail').appendChild(detail_form_id);
        document.getElementById('form_detail').submit();
    }

    function deleteSchedule(id) {
        if(window.confirm('この要素を削除します。\nよろしいですか。')) {
            let detail_form_id = document.createElement('input');
            detail_form_id.type = 'hidden';
            detail_form_id.name = 'id';
            detail_form_id.value = id;
            document.getElementById('form_detail').appendChild(detail_form_id);
            alert(id);
        }
    }
</script>

@endsection