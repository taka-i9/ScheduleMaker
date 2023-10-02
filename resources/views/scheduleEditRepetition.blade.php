@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 編集
        </div>
    </div>
    
    <div class="registration_form_content">
        <form method="POST" action="{{ route('schedule.add') }}" class="registration_form_content">
            @csrf

            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        スケジュール名<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('name'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <input type="text" name="name" id="name" class="form_element_text {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_begin_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('repetition_begin'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('repetition_begin') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="time" name="repetition_begin_time" id="repetition_begin_time" value="{{ old('repetition_begin_time') }}" onchange="changeRepetitionEndLimit()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_end_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('repetition_end'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('repetition_end') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="number" name="repetition_end_date" id="repetition_end_date" style="width: 20%" value="{{ old('repetition_end_date') ? old('repetition_end_date') : '0' }}" min="0" onchange="changeRepetitionEndLimit()">日後の
                                &nbsp;
                                <input type="time" name="repetition_end_time" id="repetition_end_time" value="{{ old('repetition_end_time') }}" onchange="changeRepetitionEndLimit()">
                            </div>
                        </div>
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
                    <div class="form_element_error">
                        @if ($errors->has('repetition_setting'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('repetition_setting') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_value" style="display: flex;">
                        <div class="form_element_checkbox">
                            <input type="checkbox" name="repetition_sun" id="repetition_sun" {{ old('repetition_sun') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 日
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 40px;">
                            <input type="checkbox" name="repetition_mon" id="repetition_mon" {{ old('repetition_mon') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 月
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 80px;">
                            <input type="checkbox" name="repetition_tue" id="repetition_tue" {{ old('repetition_tue') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 火
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 120px;">
                            <input type="checkbox" name="repetition_wed" id="repetition_wed" {{ old('repetition_wed') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 水
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 160px;">
                            <input type="checkbox" name="repetition_thu" id="repetition_thu" {{ old('repetition_thu') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 木
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 200px;">
                            <input type="checkbox" name="repetition_fri" id="repetition_fri" {{ old('repetition_fri') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 金
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 240px;">
                            <input type="checkbox" name="repetition_sat" id="repetition_sat" {{ old('repetition_sat') || old('repetition_everyday') ? 'checked' : '' }} {{ old('repetition_everyday') ? 'disabled' : '' }}> 土
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 280px;">
                            <input type="checkbox" name="repetition_everyday" id="repetition_everyday" onchange="changeStateReptationEveryday(this)" {{ old('repetition_everyday') ? 'checked' : '' }}> 毎日
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
                    <div class="form_element_error">
                        @if ($errors->has('memo'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('memo') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <input type="text" name="memo" id="memo" class="form_element_text" value="{{ old('memo') }}">
                        </div>
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
                    <div class="form_element_value">
                        <input type="checkbox" name="is_duplication" id="is_duplication" class="form_element_checkbox" {{ old('is_duplication') == 'on' ? 'checked' : '' }}>
                    </div>
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
                        <input type="color" name="color" id="color" class="form_element_color" value="{{ old('color') == '' ? '#ffffff' : old('color') }}">
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="list_status" id="list_status">
            <input type="hidden" name="list_repetition" id="list_repetition">
            <input type="hidden" name="status" id="status" value="{{ old('status') }}">
            <button type="submit">保存</button>
            &nbsp;
            <button type="button" onclick="backList()">一覧に戻る</button>
        </form>
        <form method="GET" id="back_list">
            <input type="hidden" name="list_status" id="list_status_back">
            <input type="hidden" name="list_repetition" id="list_repetition_back">
        <form>
    </div>
</div>

<script>
    window.onload = function () {
        <?php
        if(count($errors) == 0) {
            echo "var data = {";
            foreach($data as $key => $value) {
                echo '"'.$key.'": "'.$value.'",';
            }
            echo "};\n";
            echo "document.getElementById('name').value = data['name'];\n";
            echo "document.getElementById('repetition_begin_time').value = data['begin_time'].substr(-8, 5);\n";
            echo "document.getElementById('repetition_end_date').value = data['elapsed_days'];\n";
            echo "document.getElementById('repetition_end_time').value = data['end_time'].substr(-8, 5);\n";
            echo "document.getElementById('memo').value = data['memo'];\n";
            echo "document.getElementById('is_duplication').checked = data['is_duplication'];\n";
            echo "document.getElementById('color').value = data['color'];\n";
            echo "document.getElementById('status').value = '".$list_status."';\n";

            echo "document.getElementById('id').value = '".$data['id']."';\n";
            echo "document.getElementById('list_status').value = '".$list_status."';\n";
            echo "document.getElementById('list_repetition').value = '".$list_repetition."';\n";

            echo "document.getElementById('list_status_back').value = '".$list_status."';\n";
            echo "document.getElementById('list_repetition_back').value = '".$list_repetition."';\n";

            echo "viewRepetitionState(data['repetition']);\n";
        }
        else {
            echo "document.getElementById('id').value = '".old('id')."'\n";
            echo "document.getElementById('list_status').value = '".old('list_status')."'\n";
            echo "document.getElementById('list_repetition').value = '".old('list_repetition')."'\n";

            echo "document.getElementById('list_status_back').value = '".old('list_status_back')."'\n";
            echo "document.getElementById('list_repetition_back').value = '".old('list_repetition_back')."'\n";
        }
        ?>  
        changeStateReptationEveryday(document.getElementById('repetition_everyday'));
    };

    function backList() {
        document.getElementById('back_list').action = "{{ route('schedule.list') }}";
        document.getElementById('back_list').submit();
    }

    function changeStateReptationEveryday(value){
        var days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        days.forEach(function(day) {
            document.getElementById("repetition_"+day).disabled = value.checked;
            document.getElementById("repetition_"+day).checked = value.checked;
        });
    }

    function changeRepetitionEndLimit() {
        if(document.getElementById('repetition_end_date').value == 0) {
            document.getElementById("repetition_end_time").min = document.getElementById("repetition_begin_time").value;
        }
        else{
            document.getElementById("repetition_end_time").min = "";
        }
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
            changeStateReptationEveryday(document.getElementById('repetition_everyday'));
        }
    }
</script>

@endsection