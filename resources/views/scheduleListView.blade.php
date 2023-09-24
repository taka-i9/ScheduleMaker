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
            <input type="hidden" name="status" id="status">
            <input type="hidden" name="display_style" id="display_style" value="from_now">
            <input type="hidden" name="begin" id="begin">
            <input type="hidden" name="end" id="end">
        </form>
    </div>
    <div class="view_form_content">
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

    for(let i = 0; i < schedule_data.length; i++) {
        alert(schedule_data[i]['name']);
    }

    window.onload = function() {
        document.getElementById('{{ $status }}' + '_view').checked = true;
        document.getElementById('status').value = '{{ $status }}';
        document.getElementById('{{ $display_style }}').checked = true;
        if('{{ $status }}' == 'normal') {
            document.getElementById('detail_normal').hidden = false;
            document.getElementById('display_style').value = '{{ $display_style }}';
            if('{{ $display_style }}' == 'custom') {
                document.getElementById('detail_custom').hidden = false;    
            }
        }
        document.getElementById('begin').value = '{{ $begin }}';
        document.getElementById('end').value = '{{ $end }}';
        document.getElementById('custom_begin').value = '{{ $begin }}';
        document.getElementById('custom_end').value = '{{ $end }}';
        changeCustomBegin();
        changeCustomEnd();
    };
    

    function changeStatus(type) {
        let status = document.getElementById('status');
        let prev = document.getElementById(status.value + '_view');
        if(status.value == type) return;
        prev.checked = false;
        if(status.value == 'normal') {
            document.getElementById('detail_normal').hidden = true;
            document.getElementById('detail_custom').hidden = true;
        }
        else if(status.value == 'repetition') {
        }
        status.value = type;
        if(type == 'normal') {
            document.getElementById('detail_normal').hidden = false;
            if(document.getElementById('display_style').value == 'custom'){
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
        let display_style = document.getElementById('display_style');
        let prev = document.getElementById(display_style.value);
        let begin = document.getElementById('begin');
        let end = document.getElementById('end');
        if(display_style.value == type) return;
        prev.checked = false;
        if(display_style.value == 'custom') {
            document.getElementById('detail_custom').hidden = true;
        }
        display_style.value = type;
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
        begin.value = display_date(begin_date);
        end.value = display_date(end_date);
    }

    function changeCustomBegin() {
        document.getElementById('custom_end').min = document.getElementById('custom_begin').value;
        if(new Date(document.getElementById('custom_end').min) > new Date(document.getElementById('custom_end').value)) {
            document.getElementById('custom_end').value = document.getElementById('custom_end').min;
        }
        if(document.getElementById('display_style').value == 'custom') {
            document.getElementById('begin').value = document.getElementById('custom_begin').value;
        }
    }

    function changeCustomEnd() {
        document.getElementById('custom_begin').max = document.getElementById('custom_end').value;
        if(new Date(document.getElementById('custom_begin').max) < new Date(document.getElementById('custom_begin').value)) {
            document.getElementById('custom_begin').value = document.getElementById('custom_begin').max;
        }
        if(document.getElementById('display_style').value == 'custom') {
            document.getElementById('end').value = document.getElementById('custom_end').value;
        }
    }
</script>

@endsection