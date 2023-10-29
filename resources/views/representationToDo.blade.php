@extends('layouts.hometab')

@section('function_content')

<div class="view_form">
    <div class="view_form_header">
        <div class="form_header_content">
            ToDo / ワークフロー 表示
        </div>
    </div>
    <div class="view_form_edit">
        <form method="GET" id="setting">
            <input type="hidden" name="from_representation" id="from_representation" value="1">
            <input type="hidden" name="id" id="id">
        </form>
        <form method="POST" id="done" action="{{ route('representation.todo_done') }}">
            @csrf
            <input type="hidden" name="id" id="done_id">
            <input type="hidden" name="content_id" id="done_content_id">
        </form>
    </div>
    <div class="view_form_content" id="view_form_content">
    </div>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
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
        displayContent('ToDo名', '期限', '残時間', '#ffffff', false, 0);
        todo_data.forEach(function(data) {
            displayContent(data['name'], data['deadline_date'] + ' ' + data['deadline_time'], data['rest_minutes'], data['color'], data['is_over'], data['id']);
        });
        workflow_data.forEach(function(workflow_data){
            if(workflow_data['content_list'].length != 0) {
                displayContent(workflow_data['name'], workflow_data['deadline_date'] + ' ' + workflow_data['deadline_time'], '', workflow_data['color'], workflow_data['is_over'], workflow_data['id'], 0);
                workflow_data['content_list'].forEach(function(data) {
                    displayContent(data['name'], ' ', data['rest_minutes'], '#e0e0e0', false, workflow_data['id'], data['id']);
                });
            }
        });
    }

    function displayContent(name, deadline, rest_time, color, is_over, id, content_id=-1) {
        let content = document.createElement('div');
        content.className = 'view_content';
        let name_parent = document.createElement('div');
        name_parent.className = 'view_schedule_contents_parent';
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
        name_child.className = 'view_contents_child';
        name_child.innerText = name;
        name_parent.appendChild(name_child);

        let deadline_parent = document.createElement('div');
        deadline_parent.className = 'view_schedule_contents_parent';
        if(id != '0' && content_id == -1) {
            deadline_parent.onclick = function() {
                showDetailToDo(id);
            };
        }
        else if(content_id != -1) {
            deadline_parent.onclick = function () {
                showDetailWorkFlow(id);
            };
        }
        let deadline_child = document.createElement('div');
        deadline_child.className = 'view_contents_child';
        deadline_child.innerText = deadline;
        if(is_over) deadline_child.style.color = 'red';
        deadline_parent.appendChild(deadline_child);

        let rest_time_parent = document.createElement('div');
        rest_time_parent.className = 'view_schedule_contents_parent';
        rest_time_parent.id = 'rest_time_' + String(id) + '_' + String(content_id);
        if(id != '0' && content_id == -1) {
            rest_time_parent.onclick = function() {
                showDetailToDo(id);
            };
        }
        else if(content_id != -1) {
            rest_time_parent.onclick = function () {
                showDetailWorkFlow(id);
            };
        }
        let rest_time_child = document.createElement('div');
        rest_time_child.className = 'view_contents_child';
        if(id == '0' || content_id == '0') rest_time_child.innerText = rest_time;
        else rest_time_child.innerHTML = '<span id="hour_' + String(id) + '_' + String(content_id) + '" style="width: 30%;">' + String(Math.floor(Number(rest_time) / 60)) + '</span>時間&nbsp;<span id="minute_' + String(id) + '_' + String(content_id) + '" style="width: 30%;">' + String(Number(rest_time) % 60) + '</span>分';
        rest_time_parent.appendChild(rest_time_child);

        let edit_time_parent = document.createElement('div');
        edit_time_parent.className = 'view_detail_parent';
        let edit_time_child = document.createElement('div');
        edit_time_child.className = 'view_contents_child';
        if(id == '0') {
            edit_time_child.innerText = '残時間調整';
        }
        else if(content_id != 0) {
            let edit_time_button = document.createElement('button');
            edit_time_button.type = 'button';
            edit_time_button.id = 'edit_' + String(id) + '_' + String(content_id);
            edit_time_button.innerText = '変更';
            edit_time_button.onclick = function() {
                editTime(id, content_id);
            };
            edit_time_child.appendChild(edit_time_button);
        }
        edit_time_parent.appendChild(edit_time_child);

        let done_parent = document.createElement('div');
        done_parent.className = 'view_delete_parent';
        let done_child = document.createElement('div');
        done_child.className = 'view_contents_child';
        if(id == '0') {
            done_child.innerText = '完了';
        }
        else if(content_id != 0) {
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
        content.appendChild(deadline_parent);
        content.appendChild(rest_time_parent);
        content.appendChild(edit_time_parent);
        content.appendChild(done_parent);
        document.getElementById('view_form_content').appendChild(content);
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

    function editTime(id, content_id) {
        let hour = Number(document.getElementById('hour_' + String(id) + '_' + String(content_id)).innerText);
        let hour_input = document.createElement('input');
        hour_input.type = 'number';
        hour_input.id = 'input_hour_' + String(id) + '_' + String(content_id);
        hour_input.min = 0;
        hour_input.style.width = '30%';
        hour_input.value = hour;
        let func_name = 'changeRequiredMinuteLimit(' + id + ', ' + content_id + ')';
        hour_input.onchange = function() {
            changeRequiredMinuteLimit(id, content_id);
        };
        document.getElementById('hour_' + String(id) + '_' + String(content_id)).innerHTML = '';
        document.getElementById('hour_' + String(id) + '_' + String(content_id)).appendChild(hour_input);
        
        let minute = Number(document.getElementById('minute_' + String(id) + '_' + String(content_id)).innerText);
        let minute_input = document.createElement('input');
        minute_input.type = 'number';
        minute_input.id = 'input_minute_' + String(id) + '_' + String(content_id);
        minute_input.min = 0;
        minute_input.max = 60;
        minute_input.step = 15;
        minute_input.style.width = '30%';
        minute_input.value = minute;
        document.getElementById('minute_' + String(id) + '_' + String(content_id)).innerHTML = '';
        document.getElementById('minute_' + String(id) + '_' + String(content_id)).appendChild(minute_input);

        document.getElementById('edit_' + String(id) + '_' + String(content_id)).onclick = function() {
            updateTime(id, content_id);
        };

        document.getElementById('done_' + String(id) + '_' + String(content_id)).innerText = '中止';
        document.getElementById('done_' + String(id) + '_' + String(content_id)).onclick = function() {
            cancelEditTime(id, content_id);
        };

        document.getElementById('rest_time_' + String(id) + '_' + String(content_id)).onclick = function() {};
        
    }

    function changeRequiredMinuteLimit(id, content_id) {
        if(document.getElementById('input_hour_' + String(id) + '_' + String(content_id)).value == 0) {
            document.getElementById('input_minute_' + Stringe(id) + '_' + String(content_id)).min = 15;
            if(document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value == 0) {
                document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value = 15;
            }
        }
        else {
            document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).min = 0;
        }
        if(document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value == "") {
            document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value = 0;
        }
    }

    function cancelEditTime(id, content_id) {
        document.getElementById('hour_' + String(id) + '_' + String(content_id)).innerHTML = String(document.getElementById('input_hour_' + String(id) + '_' + String(content_id)).value);
        document.getElementById('minute_' + String(id) + '_' + String(content_id)).innerHTML = String(document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value);
        document.getElementById('edit_' + String(id) + '_' + String(content_id)).onclick = function() {
            editTime(id, content_id);
        };
        document.getElementById('done_' + String(id) + '_' + String(content_id)).innerText = '完了';
        document.getElementById('done_' + String(id) + '_' + String(content_id)).onclick = function() {
            doneToDo(id, content_id);
        };

        if(id != '0' && content_id == -1) {
            document.getElementById('rest_time_' + String(id) + '_' + String(content_id)).onclick = function() {
                showDetailToDo(id);
            };
        }
        else if(content_id != -1) {
            document.getElementById('rest_time_' + String(id) + '_' + String(content_id)).onclick = function () {
                showDetailWorkFlow(id);
            };
        }
    }

    function updateTime(id, content_id) {
        let new_time = Number(document.getElementById('input_hour_' + String(id) + '_' + String(content_id)).value) * 60 + Number(document.getElementById('input_minute_' + String(id) + '_' + String(content_id)).value);
        axios.post("{{ route('representation.todo_update') }}", {'id': String(id), 'content_id': String(content_id), 'time': String(new_time)})
            .then(function (response) {
                alert('修正しました。');
                cancelEditTime(id, content_id);
            })
            .catch(function (error) {
                alert('保存に失敗しました。');
            });
    }

    function doneToDo(id, content_id) {
        document.getElementById('done_id').value = String(id);
        document.getElementById('done_content_id').value = String(content_id);
        document.getElementById('done').submit();
    }


</script>

@endsection