@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 詳細
        </div>
    </div>
    <div class="registration_form_content">
        <form method="GET" id="registration_form" action="{{ route('schedule.edit') }}" class="registration_form_content">
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        スケジュール名<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="name"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="begin_time"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" id="end_time"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_value" style="display: flex;">
                        <div class="form_element_checkbox">
                            <input type="checkbox" id="repetition_sun" disabled> 日
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 40px;">
                            <input type="checkbox" id="repetition_mon" disabled> 月
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 80px;">
                            <input type="checkbox" id="repetition_tue" disabled> 火
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 120px;">
                            <input type="checkbox" id="repetition_wed" disabled> 水
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 160px;">
                            <input type="checkbox" id="repetition_thu" disabled> 木
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 200px;">
                            <input type="checkbox" id="repetition_fri" disabled> 金
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 240px;">
                            <input type="checkbox" id="repetition_sat" disabled> 土
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 280px;">
                            <input type="checkbox" id="repetition_everyday" disabled> 毎日
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        メモ<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_view_input">
                        <div class="form_element_value" style="scroll: auto" id="memo"></div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ToDoリストとの重複を許可<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value" id="is_duplication"></div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ラベルの色<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <input type="color" name="color" id="color" class="form_element_color" value="{{ old('color') == '' ? '#ffffff' : old('color') }}" disabled>
                    </div>
                </div>
            </div>
            <button type="button" id="edit_button" onclick="toEdit()">編集</button>
            &nbsp;
            <button type="button" onclick="backList()">一覧に戻る</button>

            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_repetition" id="list_repetition">
            <input type="hidden" name="id" id="id">
        </form>
    </div>
</div>

<script>
    var data = {<?php 
        if(isset($data)) {
            foreach($data as $key => $value) {
                print '"'.$key.'": "'.$value.'",';
            }
        }
    ?>};

    var list_status = "<?php if(isset($list_status)) echo $list_status; ?>";
    var list_repetition = "<?php if(isset($list_repetition)) echo $list_repetition; ?>";
    var updated = "<?php if(isset($updated)) echo $updated; ?>";

    window.onload = function() {
        var name = data['name'];
        var begin_time = data['begin_time'].substr(-8, 5);
        var end_time = data['end_time'].substr(-8, 5);
        var repetition = data['repetition'];
        var elapsed_days = data['elapsed_days']
        var memo = data['memo'];
        var is_duplication = data['is_duplication'];
        var color = data['color'];
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + name + '</div>';
        document.getElementById('begin_time').innerHTML = '<div class="form_element_text">' + begin_time + '</div>';
        document.getElementById('end_time').innerHTML = '<div class="form_element_text">' + elapsed_days + ' 日後の ' + end_time + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + memo + '</div>';
        document.getElementById('is_duplication').innerHTML = '<div class="form_element_text">' + (is_duplication!="" ? '許可する' : '許可しない') + '</div>';
        document.getElementById('color').value = color;
        
        document.getElementById('id').value = data['id'];
        document.getElementById('list_status').value = list_status;
        document.getElementById('list_repetition').value = list_repetition;
        
        viewRepetitionState(repetition);

        if(updated) {
            alert("更新しました。");
        }
    };

    function toEdit() {
        document.getElementById('registration_form').submit();
    }

    function backList() {
        document.getElementById('id').remove();
        document.getElementById('registration_form').action = "{{ route('schedule.list') }}";
        document.getElementById('registration_form').submit();
    }

    function viewRepetitionState(repetition) {
        let days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        days.forEach(function(day, index) {
            if(repetition.substr(index, 1) == '1') {
                document.getElementById('repetition_' + day).checked = true;
            }
        });
        if(repetition == '1111111') {
            document.getElementById('repetition_everyday').checked = true;
        }
    }
</script>

@endsection