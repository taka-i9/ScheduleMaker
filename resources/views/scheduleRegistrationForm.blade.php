@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 新規登録
        </div>
    </div>
    
    <div class="registration_form_content">
        <form method="POST" action="{{ route('schedule.add') }}" class="registration_form_content">
            @csrf
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        形式<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value" style="display: flex;">
                        <div class="form_element_checkbox">
                            <input type="checkbox" id="normal_form_style" onclick="changeStatus('normal')" checked disabled><label for="normal_form">スケジュール</label>
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 110px;">
                            <input type="checkbox" id="repetition_form_style" onclick="changeStatus('repetition')"><label for="repetition_form">繰り返し</label>
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 190px;">
                            <input type="checkbox" id="template_form_style" onclick="changeStatus('template')"><label for="template_form">テンプレート</label>
                        </div>
                        <input type="hidden" name="status" id="status" value="normal">
                    </div>
                </div>
            </div>
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
                            <input type="text" name="name" class="form_element_text {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="normal_begin_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('begin'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('begin') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="date" name="begin_date" id="begin_date" value="{{ old('begin_date') }}" onchange="changeEndLimit()">
                                &nbsp;
                                <input type="time" name="begin_time" id="begin_time" value="{{ old('begin_time') }}" onchange="changeEndLimit()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="normal_end_form">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('end'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('end') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" onchange="changeEndLimit()">
                                &nbsp;
                                <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" onchange="changeEndLimit()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_begin_form" hidden>
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
            <div class="form_elements" id="repetition_end_form" hidden>
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
            <div class="form_elements" id="repetition_form" {{ old('is_repetition') ? '' : 'hidden' }}>
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
                            <input type="checkbox" name="repetition_everyday" onchange="changeStateReptationEveryday(this)" {{ old('repetition_everyday') ? 'checked' : '' }}> 毎日
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
                            <input type="text" name="memo" class="form_element_text" value="{{ old('memo') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        <!--後で実装する-->
                        タグ<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="text">
                </div>
            </div>
            <div class="form_elements" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        <!--後で実装する-->
                        アラーム<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="text">
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
                        <input type="checkbox" name="is_duplication" class="form_element_checkbox" {{ old('is_duplication') == 'on' ? 'checked' : '' }}>
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
                        <input type="color" name="color" class="form_element_color" value="{{ old('color') == '' ? '#ffffff' : old('color') }}">
                    </div>
                </div>
            </div>
            <div class="form_elements" id="template_form" {{ old('is_template') ? '' : 'hidden' }}>
                <div class="form_element_name">
                    <div class="form_element_content">
                        テンプレート名<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('template_name'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('template_name') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <input type="text" name="template_name" class="form_element_text" value="{{ old('template_name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit">登録</button>
        </form>
    </div>
</div>

<script>
    window.onload = function() {
        changeStatus("{{ old('status') }}");
    };

    function changStateRepetition(value){
        document.getElementById("repetition_form").hidden = !value.checked;
    }

    function changeStateReptationEveryday(value){
        var days=["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
        days.forEach(function(day) {
            document.getElementById("repetition_"+day).disabled = value.checked;
            document.getElementById("repetition_"+day).checked = value.checked;
        });
    }

    function changStateTemplate(value){
        document.getElementById("template_form").hidden = !value.checked;
    }

    function changeEndDateLimit(){
        document.getElementById("begin_date").max = document.getElementById("end_date").value;
        document.getElementById("end_date").min = document.getElementById("begin_date").value;
    }

    function changeEndTimeLimit(){
        if(document.getElementById("begin_date").value === document.getElementById("end_date").value){
            document.getElementById("end_time").min = document.getElementById("begin_time").value;
        }
        else{
            document.getElementById("end_time").min = "";
        }
    }

    function changeEndLimit() {
        changeEndDateLimit();
        changeEndTimeLimit();
    }

    function changeRepetitionEndLimit() {
        if(document.getElementById('repetition_end_date').value == 0) {
            document.getElementById("repetition_end_time").min = document.getElementById("repetition_begin_time").value;
        }
        else{
            document.getElementById("repetition_end_time").min = "";
        }
    }

    function changeStatus(status) {
        if(status == '') return;
        if(document.getElementById('status').value == 'normal') {
            document.getElementById('normal_begin_form').hidden = true;
            document.getElementById('normal_end_form').hidden = true;
        }
        else if(document.getElementById('status').value == 'repetition') {
            document.getElementById('repetition_form').hidden = true;
            document.getElementById('repetition_begin_form').hidden = true;
            document.getElementById('repetition_end_form').hidden = true;
        }
        else if(document.getElementById('status').value == 'template') {
            document.getElementById('template_form').hidden = true;
            document.getElementById('repetition_begin_form').hidden = true;
            document.getElementById('repetition_end_form').hidden = true;
        }
        document.getElementById(document.getElementById('status').value + '_form_style').checked = false;
        document.getElementById(document.getElementById('status').value + '_form_style').disabled = false;
        document.getElementById('status').value = status;
        document.getElementById(status + '_form_style').checked = true;
        document.getElementById(status + '_form_style').disabled = true;
        if(document.getElementById('status').value == 'normal') {
            document.getElementById('normal_begin_form').hidden = false;
            document.getElementById('normal_end_form').hidden = false;
        }
        else if(document.getElementById('status').value == 'repetition') {
            document.getElementById('repetition_form').hidden = false;
            document.getElementById('repetition_begin_form').hidden = false;
            document.getElementById('repetition_end_form').hidden = false;
        }
        else if(document.getElementById('status').value == 'template') {
            document.getElementById('template_form').hidden = false;
            document.getElementById('repetition_begin_form').hidden = false;
            document.getElementById('repetition_end_form').hidden = false;
        }
    }
</script>

@endsection