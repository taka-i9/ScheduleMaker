@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            ToDo 新規登録
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" action="{{ route('todo.add') }}" class="registration_form_content">
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
                            <input type="text" name="name" class="form_element_text {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('deadline'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('deadline') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="date" name="deadline_date" id="deadline_date" value="{{ old('deadline_date') }}">
                                &nbsp;
                                <input type="time" name="deadline_time" id="deadline_time" value="{{ old('deadline_time') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        期限日当日に行う<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <input type="checkbox" name="is_today" class="form_element_checkbox" {{ old('is_today') == 'on' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        所要時間<br>
                    </div>
                </div>
                <div class="form_element_input_base">
                    <div class="form_element_error">
                        @if ($errors->has('required_time'))
                            <!--<span class="invalid-feedback" role="alert">-->
                            <span style="color: red;" role="alert">
                                {{ $errors->first('required_time') }}
                            </span>
                        @endif
                    </div>
                    <div class="form_element_input">
                        <div class="form_element_value">
                            <div class="form_element_time">
                                <input type="number" name="required_hour" id="required_hour" value="{{ old('required_hour') }}" min="0" style="width: 20%" onchange="changeRequiredMinuteLimit()">
                                時間
                                &nbsp;
                                <input type="number" name="required_minute" id="required_minute" value="{{ old('required_minute') }}" min="0" max="45" step="15" style="width: 20%">
                                分
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定する<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <input type="checkbox" name="is_repetition" class="form_element_checkbox" onchange="changStateRepetition(this)" {{ old('is_repetition') == 'on' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="repetition_form" {{ old('is_repetition') == 'on' ? '' : 'hidden' }}>
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value" style="display: flex;">
                        <div class="form_element_checkbox">
                            <input type="checkbox" name="repetition_sun" id="repetition_sun" {{ old('repetition_sun') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 日
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 40px;">
                            <input type="checkbox" name="repetition_mon" id="repetition_mon" {{ old('repetition_mon') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 月
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 80px;">
                            <input type="checkbox" name="repetition_tue" id="repetition_tue" {{ old('repetition_tue') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 火
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 120px;">
                            <input type="checkbox" name="repetition_wed" id="repetition_wed" {{ old('repetition_wed') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 水
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 160px;">
                            <input type="checkbox" name="repetition_thu" id="repetition_thu" {{ old('repetition_thu') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 木
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 200px;">
                            <input type="checkbox" name="repetition_fri" id="repetition_fri" {{ old('repetition_fri') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 金
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 240px;">
                            <input type="checkbox" name="repetition_sat" id="repetition_sat" {{ old('repetition_sat') == 'on' || old('repetition_everyday') == 'on' ? 'checked' : '' }} {{ old('repetition_everyday') == 'on' ? 'disabled' : '' }}> 土
                        </div>
                        <div class="form_element_checkbox" style="margin-left: 280px;">
                            <input type="checkbox" name="repetition_everyday" onchange="changeStateReptationEveryday(this)" {{ old('repetition_everyday') == 'on' ? 'checked' : '' }}> 毎日
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
                        <label for="priority_level">優先度</label><br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <select name="priority_level" id="priority_level" class="form_element_text">
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
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
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        テンプレートとして作成<br>
                    </div>
                </div>
                <div class="form_element_input_no_error">
                    <div class="form_element_value">
                        <input type="checkbox" name="is_template" class="form_element_checkbox" onchange="changStateTemplate(this)" {{ old('is_template') == 'on' ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
            <div class="form_elements" id="template_form" {{ old('is_template') == 'on' ? '' : 'hidden' }}>
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

    function changeRequiredMinuteLimit() {
        if(document.getElementById("required_hour").value == 0) {
            document.getElementById("required_minute").min = 15;
            if(document.getElementById("required_minute").value == 0) {
                document.getElementById("required_minute").value = 15;
            }
        }
        else {
            document.getElementById("required_minute").min = 0;
        }
    }
</script>

@endsection