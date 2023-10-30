@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            今日の予定
        </div>
    </div>
    <div class="view_form_edit">
        <form method="GET" id="setting">
            <input type="hidden" name="display_today" id="display_today" value="1">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="POST" id="done" action="{{ route('representation.todo_done') }}">
            @csrf
            <input type="hidden" name="id" id="done_id">
            <input type="hidden" name="content_id" id="done_content_id">
            <input type="hidden" name="is_today" value="1">
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

    let todo_data = [<?php 
        foreach($representation_data['todo'] as $data) {
            print '{';
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
            print '},';
        }
    ?>];

    let workflow_data = [<?php 
        foreach($representation_data['workflow'] as $workflow_data) {
            print '{';
            foreach($workflow_data as $workflow_key => $workflow_value) {
                if($workflow_key !== 'content_list') print '"'.$workflow_key.'": "'.$workflow_value.'",';
                else {
                    print '"'.$workflow_key.'": [';
                    foreach($workflow_value as $data) {
                        print '{';
                        foreach($data as $key => $value) {
                            print '"'.$key.'": "'.$value.'",';
                        }
                        print '},';
                    } 
                    print '],';
                }
            }
            print '},';
        }
    ?>];

    window.onload = function() {
        display("{{ $date }}");
    }

    function display(display_date) {
        let original_date = display_date;
        if(display_date.substr(-2, 1) == '-') display_date = display_date.substr(-1, 1);
        else display_date = display_date.substr(-2, 2);
        display_date = ('0' + display_date).substr(-2, 2);

        let display_base = document.createElement('div');
        display_base.style.height = '100%';
        display_base.style.width = '100%';
        display_base.style.display = 'flex';
        document.getElementById('view_form_content').appendChild(display_base);
        let display_schedule = document.createElement('div');
        display_schedule.style.height = '100%';
        display_schedule.style.width = '50%';
        display_base.appendChild(display_schedule);
        let display_todo = document.createElement('div');
        display_todo.style.height = '100%';
        display_todo.style.width = '50%';
        display_base.appendChild(display_todo);

        //スケジュールの表示
        let display_list_header = document.createElement('div');
        display_list_header.style.width = '100%';
        display_list_header.style.height = '5%';
        display_list_header.style.borderLeft = 'solid';
        display_list_header.style.borderRight = 'solid';
        display_list_header.style.borderBottom = 'solid';
        display_list_header.style.display = 'flex';
        display_schedule.appendChild(display_list_header);
        let display_list_header_name_base = document.createElement('div');
        display_list_header_name_base.style.width = '100%';
        display_list_header_name_base.style.height = '100%';
        let display_list_header_name = document.createElement('div');
        display_list_header_name.className = 'position_height_center';
        display_list_header_name.style.textAlign = 'center';
        display_list_header_name.innerText = 'スケジュール';
        display_list_header.appendChild(display_list_header_name_base);
        display_list_header_name_base.appendChild(display_list_header_name);

        schedule_data[display_date].forEach(function(data) {
            let display_list_content = document.createElement('div');
            display_list_content.style.width = '100%';
            display_list_content.style.height = '5%';
            display_list_content.style.borderLeft = 'solid';
            display_list_content.style.borderRight = 'solid';
            display_list_content.style.borderBottom = 'solid';
            display_list_content.style.display = 'flex';
            display_list_content.onclick = function() {
                showDetailSchedule(original_date, data['id']);
            };
            display_schedule.appendChild(display_list_content);
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

        //ToDoの表示
        let display_todo_header = document.createElement('div');
        display_todo_header.style.width = '100%';
        display_todo_header.style.height = '5%';
        display_todo_header.style.borderRight = 'solid';
        display_todo_header.style.borderBottom = 'solid';
        display_todo_header.style.display = 'flex';
        display_todo.appendChild(display_todo_header);
        let display_todo_header_name_base = document.createElement('div');
        display_todo_header_name_base.style.width = '100%';
        display_todo_header_name_base.style.height = '100%';
        let display_todo_header_name = document.createElement('div');
        display_todo_header_name.className = 'position_height_center';
        display_todo_header_name.style.textAlign = 'center';
        display_todo_header_name.innerText = 'ToDo';
        display_todo_header.appendChild(display_todo_header_name_base);
        display_todo_header_name_base.appendChild(display_todo_header_name);

        todo_data.forEach(function(data) {
            displayContent(display_todo, data['name'], data['color'], data['is_over'], data['id']);
        });
        workflow_data.forEach(function(workflow_data){
            if(workflow_data['content_list'].length != 0) {
                displayContent(display_todo, workflow_data['name'], workflow_data['color'], workflow_data['is_over'], workflow_data['id'], 0);
                workflow_data['content_list'].forEach(function(data) {
                    displayContent(display_todo, data['name'], '#e0e0e0', false, workflow_data['id'], data['id']);
                });
            }
        });
    }

    function showDetailSchedule(date, id) {
        document.getElementById('id').value = id;
        document.getElementById('setting').action = "{{ route('schedule.detail') }}";
        document.getElementById('setting').submit();
    }

    function displayContent(base, name, color, is_over, id, content_id=-1) {
        let content = document.createElement('div');
        content.style.width = '100%';
        content.style.height = '5%';
        content.style.borderRight = 'solid';
        content.style.borderBottom = 'solid';
        content.style.display = 'flex';
        let name_parent = document.createElement('div');
        name_parent.style.width = '60%';
        name_parent.style.height = '100%';
        name_parent.style.borderRight = 'solid';
        name_parent.style.backgroundColor = color;
        if(id != '0' && content_id == -1) {
            name_parent.onclick = function() {
                showDetailToDo(id);
            };
        }
        else if(content_id != -1) {
            name_parent.onclick = function () {
                showDetailWorkFlow(id);
            };
        }
        let name_child = document.createElement('div');
        name_child.className = 'position_height_center';
        name_child.style.textAlign = 'center';
        name_child.innerText = name;
        if(is_over) name_child.style.color = 'red';
        name_parent.appendChild(name_child);

        let done_parent = document.createElement('div');
        done_parent.style.width = '40%';
        done_parent.style.height = '100%';
        let done_child = document.createElement('div');
        done_child.className = 'position_height_center';
        done_child.style.textAlign = 'center';
        if(content_id != 0) {
            let done_button = document.createElement('button');
            done_button.type = 'button';
            done_button.id = 'done_' + String(id) + '_' + String(content_id);
            done_button.innerText = '完了';
            done_button.onclick =function() {
                doneToDo(id, content_id);
            };
            done_child.appendChild(done_button);
        }
        done_parent.appendChild(done_child);

        content.appendChild(name_parent);
        content.appendChild(done_parent);
        base.appendChild(content);
    }

    function showDetailToDo(id) {
        document.getElementById('id').value = id;
        document.getElementById('setting').action = "{{ route('todo.detail') }}";
        document.getElementById('setting').submit();
    }

    function showDetailWorkFlow(id) {
        document.getElementById('id').value = id;
        document.getElementById('setting').action = "{{ route('workflow.detail') }}";
        document.getElementById('setting').submit();
    }

    function doneToDo(id, content_id) {
        document.getElementById('done_id').value = String(id);
        document.getElementById('done_content_id').value = String(content_id);
        document.getElementById('done').submit();
    }
</script>

@endsection