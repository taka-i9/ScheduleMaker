@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            スケジュール 表示
        </div>
    </div>
    <div class="view_form_edit">
        <form method="get" id="setting" action="{{ route('representation.index') }}">
            <!--<div style="display: flex;">
                <button type="button" style="width: 33%;">スケジュール</button>
                <button type="button" style="width: 33%;">ToDo</button>
                <button type="button" style="width: 34%;">スケジュール/ToDo</button>
            </div>-->
            <div style="display: flex;">
                <button type="button" id="style_month" onclick="changeRepresentationStyle('month')" style="width: 33%;">月</button>
                <button type="button" id="style_week" onclick="changeRepresentationStyle('week')" style="width: 33%;">週</button>
                <button type="button" id="style_date" onclick="changeRepresentationStyle('date')" style="width: 34%;">日</button>
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
    <div class="view_form_content_representation" id="view_form_content">
    </div>
</div>

<script>
    let original_schedule_data = [<?php 
        foreach($representation_data['schedule'] as $data) {
            print '{';
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        }
    ?>];

    //並べ替える
    original_schedule_data.sort(function(a, b) {
        if(a['begin_date'] == b['begin_date']) {
            if(a['begin_time'] == b['begin_time']) {
                if(a['end_date'] == b['end_date']) {
                    return a['end_time'] < b['end_time'] ? -1 : 1;
                }
                else return a['end_date'] <= b['end_date'] ? -1 : 1 ;
            }
            else return a['begin_time'] < b['begin_time'] ? -1 : 1;
        }
        else return a['begin_date'] < b['begin_date'] ? -1 : 1;
    });

    //取得したデータを扱いやすいように加工する
    var schedule_data = {};
    if("{{ $representation_style }}" == 'month') {
        original_schedule_data.forEach(function(data, index) {
            let begin_date = data['begin_date'].substr(-2, 2);
            if(!schedule_data[begin_date]) {
                schedule_data[begin_date] = []; 
            }
            schedule_data[begin_date].push(data);
            //開始と終了の日付を取得
            //その範囲の全日にちに対して以下のことを行う
            //schedule_dataに、通し番号(index)と名前と色とIDを連想配列で渡す
            //ただし、日付に直接ではなく、配列を用意してその中に放り込む感じで
            //設置する場所については、連続する場合は同じ位置に設置する
        });
    }
    else if("{{ $representation_style }}" == 'week') {

    }
    else if("{{ $representation_style }}" == 'date') {

    }

    window.onload = function() {
        document.getElementById('representation_style').value = "{{ $representation_style }}";
        document.getElementById('view_from').value = "{{ $view_from }}";
        document.getElementById('style_' + document.getElementById('representation_style').value).disabled = true;
        if(document.getElementById('representation_style').value == 'month') DisplayMonth();
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

    function DisplayMonth() {
        let days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        let days_jp = ["日", "月", "火", "水", "木", "金", "土"];
        let display_table = document.createElement('table');
        display_table.style.width = '100%';
        display_table.style.height = '100%';
        display_table.style.border = 'solid';
        let display_header = document.createElement('tr');
        days.forEach(function(day, index) {
            let header_data = document.createElement('th');
            if(index < 5)header_data.style.width = '14%';
            else header_data.style.width = '15%';
            header_data.style.height = '4%';
            header_data.style.textAlign = 'center';
            header_data.style.border = 'solid';
            header_data.innerText = days_jp[index];
            display_header.appendChild(header_data);
        });
        display_table.appendChild(display_header);
        
        let date = new Date(document.getElementById('view_from').value + '-01');
        let month = date.getMonth();
        date = date.setDate(date.getDate() - ((date.getDay() + 6) % 7 + 1));
        for(let w = 0; w < 6; w++) {
            let display_content = document.createElement('tr');
            for(let d = 0; d < 7; d++) {
                let content_data = document.createElement('td');
                if(d < 5)content_data.style.width = '14%';
                else content_data.style.width = '15%';
                content_data.style.height = '16%';
                content_data.style.border = 'solid';
                date = new Date(date);
                if(date.getMonth() == month) {
                    content_data.onclick = '';
                }
                else {
                    content_data.style.opacity = '0.5';
                }
                let content_data_header = document.createElement('div');
                content_data_header.style.width = '100%';
                content_data_header.style.height = '20%';
                content_data_header.innerText = date.getDate();
                content_data.appendChild(content_data_header);
                for(let i = 0; i < 4; i++) {
                    let content_data_content = document.createElement('div');
                    content_data_content.id = 'schedule_' + String(date.getMonth()) + '_' + String(date.getDate()) + '_' + String(i);
                    content_data_content.style.width = '100%';
                    content_data_content.style.height = '20%';
                    content_data.appendChild(content_data_content);
                }
                let content_data_overflow = document.createElement('div');
                content_data_overflow.id = 'schedule_' + String(date.getMonth()) + '_' + String(date.getDate()) + '_overflow';
                content_data_overflow.style.width = '100%';
                content_data_overflow.style.height = '20%';
                content_data_overflow.style.textAlign = 'center';
                content_data_overflow.hidden = true;
                content_data.appendChild(content_data_overflow);
                display_content.appendChild(content_data);
                date = date.setDate(date.getDate() + 1);
            }
            display_table.appendChild(display_content);
        }
        document.getElementById('view_form_content').appendChild(display_table);

        Object.keys(schedule_data).forEach(function(date) {
            schedule_data[date].forEach(function(data) {
                let begin_date = Number(data['begin_date'].substr(-2, 2));
                let end_date = Number(data['end_date'].substr(-2, 2));
                let pos = 0;
                for(; pos < 4; pos++) {
                    if(document.getElementById('schedule_' + String(month) + '_' + String(begin_date) + '_' + String(pos)).innerHTML === '') break;
                }
                for(let i = begin_date; i <= end_date; i++) {
                    if(pos != 4) {
                        let schedule_view_content = document.createElement('div');
                        schedule_view_content.className = 'schedule_month_common';
                        if(i == begin_date) {
                            schedule_view_content.className += ' schedule_month_cont_left';
                        }
                        if(i == end_date) {
                            schedule_view_content.className += ' schedule_month_cont_right';
                        }
                        schedule_view_content.style.backgroundColor = data['color'];
                        if(i == begin_date) schedule_view_content.innerText = data['name'];
                        else schedule_view_content.innerHTML = '&nbsp;';
                        document.getElementById('schedule_' + String(month) + '_' + String(i) + '_' + String(pos)).appendChild(schedule_view_content);
                    }
                    else {
                        let last_content = document.getElementById('schedule_' + String(month) + '_' + String(i) + '_' + String(pos - 1));
                        let overflow = document.getElementById('schedule_' + String(month) + '_' + String(i) + '_overflow');
                        last_content.hidden = true;
                        overflow.hidden = false;
                        if(overflow.innerHTML === '') {
                            if(last_content.innerHTML === '') overflow.innerHTML = '+1';
                            else overflow.innerHTML = '+2';
                        }
                        else overflow.innerHTML = '+' + String(Number(overflow.innerHTML.substr(1)) + 1);
                    }
                }
            });
        });
            
    }
</script>
@endsection