@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            ToDo 編集・管理
        </div>
    </div>
    <div class="view_form_edit">
        <form method="get" id="list_setting" action="{{ route('todo.list') }}">
            <div style="display: flex;">
                <input type="checkbox" id="normal_view" onclick="changeStatus('normal')"><label for="normal_view">スケジュール</label>
                &nbsp;
                <input type="checkbox" id="repetition_view" onclick="changeStatus('repetition')"><label for="repetition_view">繰り返し</label>
                &nbsp;
                <input type="checkbox" id="template_view" onclick="changeStatus('template')"><label for="template_view">テンプレート</label>
                &nbsp;
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
            <div id="detail_repetition" style="position: relative; display: flex;" hidden>
                <div style="position: absolute;">
                    <input type="checkbox" id="repetition_sun" onchange="changeRepetition(this, 0)"><label for="repetition_sun">日</label>
                </div>
                <div style="position: absolute; margin-left: 40px;">
                    <input type="checkbox" id="repetition_mon" onchange="changeRepetition(this, 1)"><label for="repetition_mon">月</label>
                </div>
                <div style="position: absolute; margin-left: 80px;">
                    <input type="checkbox" id="repetition_tue" onchange="changeRepetition(this, 2)"><label for="repetition_tue">火</label>
                </div>
                <div style="position: absolute; margin-left: 120px;">
                    <input type="checkbox" id="repetition_wed" onchange="changeRepetition(this, 3)"><label for="repetition_wed">水</label>
                </div>
                <div style="position: absolute; margin-left: 160px;">
                    <input type="checkbox" id="repetition_thu" onchange="changeRepetition(this, 4)"><label for="repetition_thu">木</label>
                </div>
                <div style="position: absolute; margin-left: 200px;">
                    <input type="checkbox" id="repetition_fri" onchange="changeRepetition(this, 5)"><label for="repetition_fri">金</label>
                </div>
                <div style="position: absolute; margin-left: 240px;">
                    <input type="checkbox" id="repetition_sat" onchange="changeRepetition(this, 6)"><label for="repetition_sat">土</label>
                </div>
                <div style="position: absolute; margin-left: 280px;">
                    <input type="checkbox" id="everyday" onchange="changeStateReptationEveryday(this)"><label for="everyday">全て</label>
                </div>
            </div>
            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_display_style" id="list_display_style" value="from_now">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
            <input type="hidden" name="list_repetition" id="list_repetition" value="0000000">
        </form>
    </div>
    <div class="view_form_content" id="view_form_content">
        <form method="get" id="form_detail" action="{{ route('todo.detail') }}">
            <input type="hidden" name="list_status" id="list_status_detail">
            <input type="hidden" name="list_display_style" id="list_display_style_detail">
            <input type="hidden" name="list_begin" id="list_begin_detail">
            <input type="hidden" name="list_end" id="list_end_detail">
            <input type="hidden" name="list_repetition" id="list_repetition_detail">
        </form>
        <form method="post" id="form_delete" action="{{ route('todo.delete') }}">
            @csrf
            <input type="hidden" name="list_status" id="list_status_delete">
            <input type="hidden" name="list_display_style" id="list_display_style_delete">
            <input type="hidden" name="list_begin" id="list_begin_delete">
            <input type="hidden" name="list_end" id="list_end_delete">
            <input type="hidden" name="list_repetition" id="list_repetition_delete">
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
        document.getElementById('{{ $list_status ? $list_status : "normal" }}' + '_view').checked = true;
        document.getElementById('list_status').value = '{{ $list_status }}';
        if('{{ $list_status }}' == 'normal') {
            document.getElementById('{{ $list_display_style }}').checked = true;
            document.getElementById('detail_normal').hidden = false;
            document.getElementById('list_display_style').value = '{{ $list_display_style }}';
            if('{{ $list_display_style }}' == 'custom') {
                document.getElementById('detail_custom').hidden = false;    
            }
        }
        if('{{ $list_status }}' == 'repetition') {
            document.getElementById('detail_repetition').hidden = false;
            let days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
            let display_repetition = '{{ $list_repetition }}';
            if(display_repetition == '1111111') {
                document.getElementById('everyday').checked = true;
                changeStateReptationEveryday(document.getElementById('everyday'));
            }
            else {
                for(let i = 0; i < days.length; i++) {
                    if(display_repetition.substr(i, 1) == '1') {
                        document.getElementById("repetition_"+days[i]).checked = true;
                    }
                }
            }
        }
        document.getElementById('list_begin').value = '{{ $list_begin }}';
        document.getElementById('list_end').value = '{{ $list_end }}';
        document.getElementById('custom_begin').value = '{{ $list_begin }}';
        document.getElementById('custom_end').value = '{{ $list_end }}';
        changeCustomBegin();
        changeCustomEnd();
        document.getElementById('list_repetition').value = '{{ $list_repetition }}';
        if('{{ $list_status }}' == 'normal') {
            displayNormal('スケジュール名', '期限', 'タイプ', '0');
            for(let i = 0; i < schedule_data.length; i++) {
                displayNormal(schedule_data[i]['name'], schedule_data[i]['deadline'], schedule_data[i]['type'], schedule_data[i]['id']);
            }
        }
        if('{{ $list_status }}' == 'repetition') {
            displayRepetition('スケジュール名', '繰り返し', '0');
            for(let i = 0; i < schedule_data.length; i++) {
                displayRepetition(schedule_data[i]['name'], schedule_data[i]['repetition'], schedule_data[i]['id']);
            }
        }
        if('{{ $list_status }}' == 'template') {
            displayTemplate('テンプレート名', '0');
            for(let i = 0; i < schedule_data.length; i++) {
                displayTemplate(schedule_data[i]['name'], schedule_data[i]['id']);
            }
        }
        document.getElementById('list_status_detail').value = '{{ $list_status }}';
        document.getElementById('list_display_style_detail').value = '{{ $list_display_style }}';
        document.getElementById('list_begin_detail').value = '{{ $list_begin }}';
        document.getElementById('list_end_detail').value = '{{ $list_end }}';
        document.getElementById('list_repetition_detail').value = '{{ $list_repetition }}';

        document.getElementById('list_status_delete').value = '{{ $list_status }}';
        document.getElementById('list_display_style_delete').value = '{{ $list_display_style }}';
        document.getElementById('list_begin_delete').value = '{{ $list_begin }}';
        document.getElementById('list_end_delete').value = '{{ $list_end }}';
        document.getElementById('list_repetition_delete').value = '{{ $list_repetition }}';

        if("{{ $deleted }}") {
            alert("削除が完了しました。");
        }
    };
    

    function changeStatus(type) {
        let list_status = document.getElementById('list_status');
        let status_name = list_status.value;
        if(status_name == '') status_name = 'normal';
        let prev = document.getElementById(status_name + '_view');
        if(status_name == type) return;
        prev.checked = false;
        if(status_name == 'normal') {
            document.getElementById('detail_normal').hidden = true;
            document.getElementById('detail_custom').hidden = true;
        }
        else if(status_name == 'repetition') {
            document.getElementById('detail_repetition').hidden = true;
        }
        list_status.value = type;
        if(type == 'normal') {
            document.getElementById('detail_normal').hidden = false;
            if(document.getElementById('list_display_style').value == 'custom'){
                document.getElementById('detail_custom').hidden = false;
            }
        }
        else if(type == 'repetition') {
            document.getElementById('detail_repetition').hidden = false;
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

    function listSetting(type) {
        let list_status_element = document.getElementById('list_status' + type);
        let list_display_style_element = document.getElementById('list_display_style' + type);
        let list_begin_element = document.getElementById('list_begin' + type);
        let list_end_element = document.getElementById('list_end' + type);
        let list_repetition_element = document.getElementById('list_repetition' + type);
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

    function displayNormal(name, deadline, type, id) {
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
        let type_parent = document.createElement('div');
        type_parent.className = 'view_schedule_contents_parent';
        let type_child = document.createElement('div');
        type_child.className = 'view_contents_child';
        if(id == '0') {
            type_child.innerText = type;
        }
        else {
            if(type == 'today') {
                type_child.innerText = '当日実施';
            }
            else if(type == 'deadline') {
                type_child.innerText = '期限';
            }
        }
        type_parent.appendChild(type_child);
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
        content.appendChild(type_parent);
        content.appendChild(detail_parent);
        content.appendChild(delete_parent);
        document.getElementById('view_form_content').appendChild(content);
    }

    function displayRepetition(name, repetition, id) {
        let content = document.createElement('div');
        content.className = 'view_content';
        let name_parent = document.createElement('div');
        name_parent.className = 'view_schedule_contents_parent';
        let name_child = document.createElement('div');
        name_child.className = 'view_contents_child';
        name_child.innerText = name;
        name_parent.appendChild(name_child);
        let repetition_parent = document.createElement('div');
        repetition_parent.className = 'view_schedule_contents_repetition_parent';
        let repetition_child = document.createElement('div');
        repetition_child.className = 'view_contents_child';
        if(id == '0') {
            repetition_child.innerText = repetition;
        }
        else {
            repetition_child.innerText = viewRepetitionState(repetition);
        }
        repetition_parent.appendChild(repetition_child);
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
        content.appendChild(repetition_parent);
        content.appendChild(detail_parent);
        content.appendChild(delete_parent);
        document.getElementById('view_form_content').appendChild(content);
    }

    function displayTemplate(name, id) {
        let content = document.createElement('div');
        content.className = 'view_content';
        let name_parent = document.createElement('div');
        name_parent.className = 'view_schedule_contents_template_parent';
        let name_child = document.createElement('div');
        name_child.className = 'view_contents_child';
        name_child.innerText = name;
        name_parent.appendChild(name_child);
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

    function changeStateReptationEveryday(value){
        let days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        days.forEach(function(day) {
            document.getElementById("repetition_"+day).disabled = value.checked;
            document.getElementById("repetition_"+day).checked = value.checked;
        });
        document.getElementById('list_repetition').value = value.checked ? '1111111' : '0000000';
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
</script>

@endsection