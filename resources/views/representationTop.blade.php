@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            スケジュール 編集・管理
        </div>
    </div>
    <div class="view_form_edit">
        <form method="get" id="setting" action="{{ route('representation.index') }}">
            <div style="display: flex;">
                <button type="button" style="width: 33%;">スケジュール</button>
                <button type="button" style="width: 33%;">ToDo</button>
                <button type="button" style="width: 34%;">スケジュール/ToDo</button>
            </div>
            <div style="display: flex;">
                <button type="button" id="style_date" onclick="changeRepresentationStyle('date')" style="width: 33%;">日</button>
                <button type="button" id="style_week" onclick="changeRepresentationStyle('week')" style="width: 33%;">週</button>
                <button type="button" id="style_month" onclick="changeRepresentationStyle('month')" style="width: 34%;">月</button>
            </div>
            <div>
                <button type="button" onclick="changeRepresentationBefore()" style="width: 33%;">前</button>
                <button type="button" onclick="changeRepresentationNext()" style="width: 33%;">次</button>
                <button type="button" onclick="changeRepresentationNow()" style="width: 34%;">現在</button>
            </div>
            <input type="hidden" name="representation_style" id="representation_style">
            <input type="hidden" name="view_from" id="view_from">
        </form>
    </div>
</div>

<script>
    let schedule_data = [<?php 
        foreach($representation_data['schedule'] as $data) {
            print '{';
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        }
    ?>];

    window.onload = function() {
        document.getElementById('representation_style').value = "{{ $representation_style }}";
        document.getElementById('view_from').value = "{{ $view_from }}";
        document.getElementById('style_' + document.getElementById('representation_style').value).disabled = true;
    }
    
    function changeRepresentationStyle(style) {
        document.getElementById('representation_style').value = style;
        document.getElementById('view_from').remove();
        document.getElementById('setting').submit();
    }

    function changeRepresentationBefore() {
        let style = document.getElementById('representation_style').value;
        let now = new Date(document.getElementById('view_from').value);
        if(style == 'month') {
            now.setMonth(now.getMonth() - 1);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1);
        }
        else if(style == 'week') {
            now.setDate(now.getDate() - 7);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1) + '-' + now.getDate();
        }
        else if(style == 'date') {
            now.setDate(now.getDate() - 1);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1) + '-' + now.getDate();
        }
        document.getElementById('setting').submit();
    }

    function changeRepresentationNext() {
        let style = document.getElementById('representation_style').value;
        let now = new Date(document.getElementById('view_from').value);
        if(style == 'month') {
            now.setMonth(now.getMonth() + 1);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1);
        }
        else if(style == 'week') {
            now.setDate(now.getDate() + 7);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1) + '-' + now.getDate();
        }
        else if(style == 'date') {
            now.setDate(now.getDate() + 1);
            document.getElementById('view_from').value = now.getFullYear() + '-' + String(now.getMonth() + 1) + '-' + now.getDate();
        }
        document.getElementById('setting').submit();
    }

    function changeRepresentationNow() {
        document.getElementById('view_from').remove();
        document.getElementById('setting').submit();
    }
</script>
@endsection