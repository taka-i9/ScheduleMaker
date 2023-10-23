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
            <div style="display: flex">
                <button type="button" onclick="changeRepresentationBefore()" style="width: 33%;">前</button>
                <button type="button" onclick="changeRepresentationNext()" style="width: 33%;">次</button>
                <button type="button" onclick="changeRepresentationNow()" style="width: 34%;">現在</button>
            </div>
            <div>
                <button type="button" id="back_button" onclick="document.getElementById('setting').submit()" style="width: 20%" hidden>戻る</button>
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
    original_schedule_data.forEach(function(data, index) {
        let begin_date = data['begin_date'].substr(-2, 2);
        if(!schedule_data[begin_date]) {
            schedule_data[begin_date] = []; 
        }
        schedule_data[begin_date].push(data);
    });

    window.onload = function() {
        document.getElementById('representation_style').value = "{{ $representation_style }}";
        document.getElementById('view_from').value = "{{ $view_from }}";
        document.getElementById('style_' + document.getElementById('representation_style').value).disabled = true;
        if("{{ $display_detail }}") {
            document.getElementById('back_button').hidden = false;
            DisplayDate("{{ $display_detail }}".substr(-2, 2));
        }
        else if(document.getElementById('representation_style').value == 'month') DisplayMonth();
        else if(document.getElementById('representation_style').value == 'week') DisplayWeek();
        else if(document.getElementById('representation_style').value == 'date') DisplayDate(document.getElementById('view_from').value.substr(-2, 2));
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
                let full_date = String(date.getFullYear()) + '-' + String(date.getMonth() + 1) + '-'  + String(date.getDate());
                content_data.onclick = function() {
                    displayDetailDate(full_date);
                }
                if(date.getMonth() != month) {
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

    function DisplayWeek() {
        let days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        let days_jp = ["日", "月", "火", "水", "木", "金", "土"];
        let week = new Date(document.getElementById('view_from').value);
        week = week.getDay();
        let date = new Date(document.getElementById('view_from').value);

        let display_base = document.createElement('div');
        display_base.style.height = '100%';
        display_base.style.width = '100%';
        display_base.style.display = 'flex';
        let display_time = document.createElement('div');
        display_time.style.height = '100%';
        display_time.style.width = '10%';
        display_time.style.borderLeft = 'solid';
        display_time.style.borderBottom = 'solid';
        display_time.style.position = 'relative';
        let display_main = document.createElement('div');
        display_main.style.height = '100%';
        display_main.style.width = '90%';
        display_base.appendChild(display_time);
        display_base.appendChild(display_main);
        document.getElementById('view_form_content').appendChild(display_base);

        let display_time_display = document.createElement('div');
        display_time_display.style.height = '92%';
        display_time_display.style.width = '100%';
        display_time_display.style.top = '8%';
        display_time_display.style.position = 'absolute';
        display_time.appendChild(display_time_display);
        let display_time_content_base = document.createElement('div');
        display_time_content_base.style.height = '100%';
        display_time_content_base.style.width = '100%';
        display_time_content_base.style.position = 'relative';
        display_time_display.appendChild(display_time_content_base);

        for(let i = 0; i < 24; i++) {
            let display_time_content = document.createElement('div');
            display_time_content.style.height = String(100 / 24) + '%';
            display_time_content.style.width = '100%';
            display_time_content.style.top = String(100 / 48 * (2 * i - 1)) + '%';
            display_time_content.style.position = 'absolute';
            display_time_content_base.appendChild(display_time_content);
            let display_time_content_text_base = document.createElement('div');
            display_time_content_text_base.style.height = '100%';
            display_time_content_text_base.style.width = '100%';
            display_time_content_text_base.style.position = 'relative';
            display_time_content.appendChild(display_time_content_text_base);
            let display_time_content_text = document.createElement('div');
            display_time_content_text.className = 'position_height_center';
            display_time_content_text.style.textAlign = 'center';
            display_time_content_text.innerHTML = ('00' + String(i)).substr(-2, 2);
            display_time_content_text_base.appendChild(display_time_content_text);
        }

        let display_header_date = document.createElement('div');
        display_header_date.style.height = '4%';
        display_header_date.style.width = '100%';
        display_header_date.style.display = 'flex';
        display_header_date.style.borderBottom = 'solid';
        display_header_date.style.borderLeft = 'solid';
        for(let i = 0; i < 7; i++) {
            let header_data = document.createElement('div');
            header_data.style.width = String(100 / 7) + '%';
            header_data.style.height = '100%';
            header_data.style.textAlign = 'center';
            header_data.style.borderRight = 'solid';
            header_data.style.position = 'relative';
            let header_data_content = document.createElement('div');
            header_data_content.className = 'position_height_center';
            header_data_content.innerText = String(date.getMonth() + 1) + '/' + String(date.getDate());
            header_data.appendChild(header_data_content);
            display_header_date.appendChild(header_data);
            date.setDate(date.getDate() + 1);
        }
        display_main.appendChild(display_header_date);
        
        let display_header_day = document.createElement('div');
        display_header_day.style.height = '4%';
        display_header_day.style.width = '100%';
        display_header_day.style.display = 'flex';
        display_header_day.style.borderBottom = 'solid';
        display_header_day.style.borderLeft = 'solid';
        days.forEach(function(day, index) {
            let header_data = document.createElement('div');
            header_data.style.width = String(100 / 7) + '%';
            header_data.style.height = '100%';
            header_data.style.textAlign = 'center';
            header_data.style.borderRight = 'solid';
            header_data.style.position = 'relative';
            let header_data_content = document.createElement('div');
            header_data_content.className = 'position_height_center';
            header_data_content.innerText = days_jp[(week + index) % 7];
            header_data.appendChild(header_data_content);
            display_header_day.appendChild(header_data);
        });
        display_main.appendChild(display_header_day);

        date = new Date(document.getElementById('view_from').value);
        let display_content = document.createElement('div');
        display_content.style.width = '100%';
        display_content.style.height = '92%';
        display_content.style.borderLeft = 'solid';
        display_content.style.position = 'relative';
        for(let i = 0; i < 48; i++) {
            let line = document.createElement('div');
            line.style.width = '100%';
            line.style.height = String(100 / 48) + '%';
            line.style.top = String((100 / 48) * i) + '%';
            line.style.borderBottom = 'solid ' + (i % 2 == 0 ? '1px' : '2px');
            line.style.position = 'absolute';
            display_content.appendChild(line);
        }
        for(let i = 0; i < 7; i++) {
            let line = document.createElement('div');
            line.style.width = String(100 / 7) + '%';
            line.style.height = '100%';
            line.style.marginLeft = String(100 / 7 * i) + '%';
            line.style.borderRight = 'solid';
            line.style.position = 'absolute';
            let full_date = String(date.getFullYear()) + '-' + String(date.getMonth() + 1) + '-'  + String(date.getDate());
            line.onclick = function() {
                displayDetailDate(full_date);
            };
            display_content.appendChild(line);
            date.setDate(date.getDate() + 1);
        }

        let unfinished_schedule_end_list = [];
        let put_pos_margin = -2;
        Object.keys(schedule_data).forEach(function(date) {
            schedule_data[date].forEach(function(data) {
                let begin_pos = parseInt((new Date(data['begin_date']) - new Date(document.getElementById('view_from').value)) / 1000 / 60 / 60 / 24);
                let end_pos = parseInt((new Date(data['end_date']) - new Date(document.getElementById('view_from').value)) / 1000 / 60 / 60 / 24);
                let begin_time_minute = Number(data['begin_time'].substr(0, 2)) * 60 + Number(data['begin_time'].substr(-2, 2));
                let end_time_minute = Number(data['end_time'].substr(0, 2)) * 60 + Number(data['end_time'].substr(-2, 2));
                for(let i = unfinished_schedule_end_list.length - 1; i >= 0; i--) {
                    if(unfinished_schedule_end_list[i][0] > data['begin_date'] || (unfinished_schedule_end_list[i][0] == data['begin_date'] && unfinished_schedule_end_list[i][1] >= data['begin_time'])) {
                        break;
                    }
                    else {
                        unfinished_schedule_end_list.pop();
                        put_pos_margin -= 2;
                    }
                }
                unfinished_schedule_end_list.push([data['end_date'], data['end_time']]);
                put_pos_margin += 2;
                for(let i = begin_pos; i <= end_pos; i++) {
                    let content_begin_time = (i == begin_pos ? begin_time_minute : 0);
                    let content_end_time = (i == end_pos ? end_time_minute : 60 * 24);
                    let schedule_view_content = document.createElement('div');
                    schedule_view_content.style.position = 'absolute';
                    schedule_view_content.style.width = String(12 - put_pos_margin) + '%';
                    schedule_view_content.style.height = String(100 / 24 * ((content_end_time - content_begin_time) / 60)) + '%';
                    schedule_view_content.style.border = 'solid 1px';
                    schedule_view_content.style.marginLeft = String(100 / 7 * i + put_pos_margin + 1) + '%';
                    schedule_view_content.style.top = String(100 / 24 * (content_begin_time / 60)) + '%';
                    schedule_view_content.style.backgroundColor = data['color'];
                    schedule_view_content.style.textAlign = 'center';
                    if(i == begin_pos) schedule_view_content.innerText = data['name'];
                    display_content.appendChild(schedule_view_content);
                }
            });
        });
        display_main.appendChild(display_content);
    }

    function DisplayDate(display_date) {
        let days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        let days_jp = ["日", "月", "火", "水", "木", "金", "土"];
        let week = new Date(document.getElementById('view_from').value);
        week = week.getDay();
        let date = new Date(document.getElementById('view_from').value);

        let display_base = document.createElement('div');
        display_base.style.height = '100%';
        display_base.style.width = '100%';
        display_base.style.display = 'flex';
        let display_time = document.createElement('div');
        display_time.style.height = '100%';
        display_time.style.width = '10%';
        display_time.style.borderLeft = 'solid';
        display_time.style.borderBottom = 'solid';
        display_time.style.position = 'relative';
        let display_main = document.createElement('div');
        display_main.style.height = '100%';
        display_main.style.width = '40%';
        display_main.style.borderLeft = 'solid';
        display_main.style.borderRight = 'solid';
        let display_list = document.createElement('div');
        display_list.style.height = '100%';
        display_list.style.width = '50%';
        display_base.appendChild(display_time);
        display_base.appendChild(display_main);
        display_base.appendChild(display_list);
        document.getElementById('view_form_content').appendChild(display_base);

        let display_time_display = document.createElement('div');
        display_time_display.style.height = '92%';
        display_time_display.style.width = '100%';
        display_time_display.style.top = '8%';
        display_time_display.style.position = 'absolute';
        display_time.appendChild(display_time_display);
        let display_time_content_base = document.createElement('div');
        display_time_content_base.style.height = '100%';
        display_time_content_base.style.width = '100%';
        display_time_content_base.style.position = 'relative';
        display_time_display.appendChild(display_time_content_base);

        for(let i = 0; i < 24; i++) {
            let display_time_content = document.createElement('div');
            display_time_content.style.height = String(100 / 24) + '%';
            display_time_content.style.width = '100%';
            display_time_content.style.top = String(100 / 48 * (2 * i - 1)) + '%';
            display_time_content.style.position = 'absolute';
            display_time_content_base.appendChild(display_time_content);
            let display_time_content_text_base = document.createElement('div');
            display_time_content_text_base.style.height = '100%';
            display_time_content_text_base.style.width = '100%';
            display_time_content_text_base.style.position = 'relative';
            display_time_content.appendChild(display_time_content_text_base);
            let display_time_content_text = document.createElement('div');
            display_time_content_text.className = 'position_height_center';
            display_time_content_text.style.textAlign = 'center';
            display_time_content_text.innerHTML = ('00' + String(i)).substr(-2, 2);
            display_time_content_text_base.appendChild(display_time_content_text);
        }

        let display_header_date = document.createElement('div');
        display_header_date.style.height = '4%';
        display_header_date.style.width = '100%';
        display_header_date.style.borderBottom = 'solid';
        let header_data = document.createElement('div');
        header_data.style.width = '100%';
        header_data.style.height = '100%';
        header_data.style.textAlign = 'center';
        header_data.style.position = 'relative';
        let header_data_content = document.createElement('div');
        header_data_content.className = 'position_height_center';
        header_data_content.innerText = String(date.getMonth() + 1) + '/' + String(date.getDate());
        header_data.appendChild(header_data_content);
        display_header_date.appendChild(header_data);
        display_main.appendChild(display_header_date);
        
        let display_header_day = document.createElement('div');
        display_header_day.style.height = '4%';
        display_header_day.style.width = '100%';
        display_header_day.style.borderBottom = 'solid';
        header_data = document.createElement('div');
        header_data.style.width = '100%';
        header_data.style.height = '100%';
        header_data.style.textAlign = 'center';
        header_data.style.position = 'relative';
        header_data_content = document.createElement('div');
        header_data_content.className = 'position_height_center';
        header_data_content.innerText = days_jp[week];
        header_data.appendChild(header_data_content);
        display_header_day.appendChild(header_data);
        display_main.appendChild(display_header_day);

        let display_content = document.createElement('div');
        display_content.style.width = '100%';
        display_content.style.height = '92%';
        display_content.style.position = 'relative';
        for(let i = 0; i < 48; i++) {
            let line = document.createElement('div');
            line.style.width = '100%';
            line.style.height = String(100 / 48) + '%';
            line.style.top = String((100 / 48) * i) + '%';
            line.style.borderBottom = 'solid ' + (i % 2 == 0 ? '1px' : '2px');
            line.style.position = 'absolute';
            display_content.appendChild(line);
        }

        let unfinished_schedule_end_list = [];
        let put_pos_margin = -10;
        schedule_data[display_date].forEach(function(data) {
            let begin_time_minute = Number(data['begin_time'].substr(0, 2)) * 60 + Number(data['begin_time'].substr(-2, 2));
            let end_time_minute = Number(data['end_time'].substr(0, 2)) * 60 + Number(data['end_time'].substr(-2, 2));
            for(let i = unfinished_schedule_end_list.length - 1; i >= 0; i--) {
                if(unfinished_schedule_end_list[i][0] > data['begin_date'] || (unfinished_schedule_end_list[i][0] == data['begin_date'] && unfinished_schedule_end_list[i][1] >= data['begin_time'])) {
                    break;
                }
                else {
                    unfinished_schedule_end_list.pop();
                    put_pos_margin -= 10;
                }
            }
            unfinished_schedule_end_list.push([data['end_date'], data['end_time']]);
            put_pos_margin += 10;
            let content_begin_time = (display_date == data['begin_date'].substr(-2, 2) ? begin_time_minute : 0);
            let content_end_time = (display_date == data['end_date'].substr(-2, 2) ? end_time_minute : 60 * 24);
            let schedule_view_content = document.createElement('div');
            schedule_view_content.style.position = 'absolute';
            schedule_view_content.style.width = String(92 - put_pos_margin) + '%';
            schedule_view_content.style.height = String(100 / 24 * ((content_end_time - content_begin_time) / 60)) + '%';
            schedule_view_content.style.border = 'solid 1px';
            schedule_view_content.style.marginLeft = String(put_pos_margin + 4) + '%';
            schedule_view_content.style.top = String(100 / 24 * (content_begin_time / 60)) + '%';
            schedule_view_content.style.backgroundColor = data['color'];
            schedule_view_content.style.textAlign = 'center';
            schedule_view_content.innerText = data['name'];
            display_content.appendChild(schedule_view_content);
        });
        display_main.appendChild(display_content);

        schedule_data[display_date].forEach(function(data) {
            let display_list_content = document.createElement('div');
            display_list_content.style.width = '100%';
            display_list_content.style.height = '5%';
            display_list_content.style.borderRight = 'solid';
            display_list_content.style.borderBottom = 'solid';
            display_list_content.style.display = 'flex';
            display_list.appendChild(display_list_content);
            let display_list_content_name_base = document.createElement('div');
            display_list_content_name_base.style.width = '60%';
            display_list_content_name_base.style.height = '100%';
            display_list_content_name_base.style.borderRight = 'solid';
            display_list_content_name_base.style.backgroundColor = data['color'];
            let display_list_content_name = document.createElement('div');
            display_list_content_name.className = 'position_height_center';
            display_list_content_name.style.textAlign = 'center';
            display_list_content_name.innerText = data['name'];
            display_list_content.appendChild(display_list_content_name_base);
            display_list_content_name_base.appendChild(display_list_content_name);
            
            let display_list_content_time = document.createElement('div');
            display_list_content_time.style.width = '40%';
            display_list_content_time.style.height = '100%';
            display_list_content_time.style.display = 'flex';
            display_list_content.appendChild(display_list_content_time);
            let display_list_content_time_begin = document.createElement('div');
            display_list_content_time_begin.style.width = '40%';
            display_list_content_time_begin.style.height = '100%';
            display_list_content_time_begin.style.position = 'relative';
            let display_list_content_time_begin_content = document.createElement('div');
            display_list_content_time_begin_content.className = 'position_height_center';
            display_list_content_time_begin_content.style.textAlign = 'center';
            if(!data['is_begin_out']) display_list_content_time_begin_content.innerText = data['begin_time'];
            display_list_content_time_begin.appendChild(display_list_content_time_begin_content);
            display_list_content_time.appendChild(display_list_content_time_begin);
            let display_list_content_time_middle = document.createElement('div');
            display_list_content_time_middle.style.width = '20%';
            display_list_content_time_middle.style.height = '100%';
            display_list_content_time_middle.style.position = 'relative';
            let display_list_content_time_middle_content = document.createElement('div');
            display_list_content_time_middle_content.className = 'position_height_center';
            display_list_content_time_middle_content.style.textAlign = 'center';
            display_list_content_time_middle_content.innerText = '~';
            display_list_content_time_middle.appendChild(display_list_content_time_middle_content);
            display_list_content_time.appendChild(display_list_content_time_middle);
            let display_list_content_time_end = document.createElement('div');
            display_list_content_time_end.style.width = '40%';
            display_list_content_time_end.style.height = '100%';
            display_list_content_time_end.style.position = 'relative';
            let display_list_content_time_end_content = document.createElement('div');
            display_list_content_time_end_content.className = 'position_height_center';
            display_list_content_time_end_content.style.textAlign = 'center';
            if(!data['is_end_out']) display_list_content_time_end_content.innerText = data['end_time'];
            display_list_content_time_end.appendChild(display_list_content_time_end_content);
            display_list_content_time.appendChild(display_list_content_time_end);
        });
    }

    function displayDetailDate(date) {
        //alert(date);
        let display_detail = document.createElement('input');
        display_detail.type = 'hidden';
        display_detail.name = 'display_detail';
        display_detail.value = date;
        document.getElementById('setting').appendChild(display_detail);
        document.getElementById('setting').submit();
    }
</script>
@endsection