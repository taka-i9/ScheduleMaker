@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 新規登録
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" action="" class="registration_form_content">
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        スケジュール名<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="text" name="name" class="form_element_text">
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        開始時刻<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <div class="form_element_time">
                        <input type="date" name="begin_date">
                        &nbsp;
                        <input type="time" name="begin_time">
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        終了時刻<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <div class="form_element_time">
                        <input type="date" name="end_date">
                        &nbsp;
                        <input type="time" name="end_time">
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定する<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="checkbox" name="is_repetition" class="form_element_checkbox" onchange="changStateRepetition(this)">
                </div>
            </div>
            <div class="form_elements" id="repetition_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        繰り返し設定<br>
                    </div>
                </div>
                <div class="form_element_value" style="display: flex;">
                    <div class="form_element_checkbox">
                        <input type="checkbox" name="repetition_sun" id="repetition_sun"> 日
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 40px;">
                        <input type="checkbox" name="repetition_mon" id="repetition_mon"> 月
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 80px;">
                        <input type="checkbox" name="repetition_tue" id="repetition_tue"> 火
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 120px;">
                        <input type="checkbox" name="repetition_wed" id="repetition_wed"> 水
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 160px;">
                        <input type="checkbox" name="repetition_thu" id="repetition_thu"> 木
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 200px;">
                        <input type="checkbox" name="repetition_fri" id="repetition_fri"> 金
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 240px;">
                        <input type="checkbox" name="repetition_sat" id="repetition_sat"> 土
                    </div>
                    <div class="form_element_checkbox" style="margin-left: 280px;">
                        <input type="checkbox" name="repetition_everyday" onchange="changeStateReptationEveryday(this)"> 毎日
                    </div>
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        メモ<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="text" class="form_element_text">
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
                <div class="form_element_value">
                <input type="checkbox" name="is_duplecation" class="form_element_checkbox">
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        ラベルの色<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="color" name="color" class="form_element_color" value="#ffffff">
                </div>
            </div>
            <div class="form_elements">
                <div class="form_element_name">
                    <div class="form_element_content">
                        テンプレートにする<br>
                    </div>
                </div>
                <div class="form_element_value">
                    <input type="checkbox" name="is_template" class="form_element_checkbox" onchange="changStateTemplate(this)">
                </div>
            </div>
            <div class="form_elements" id="template_form" hidden>
                <div class="form_element_name">
                    <div class="form_element_content">
                        テンプレート名<br>
                    </div>
                </div>
                <div class="form_element_value">
                <input type="text" name="template_name" class="form_element_text">
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
</script>

@endsection