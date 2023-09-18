@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            編集画面
        </div>
    </div>
    <!--エラーが発生した場合は、このエリアに表示する-->
    <div class="registration_form_content registration_free_field_base">
        <div class="registration_free_field">
            <div onclick="editContent(this)" id="field_content_1" style="border: solid; border-color: black; position: absolute; width: 200px; height: 75px; background-color: white; text-align:center; margin-left: 200px; margin-top: 100px;">
                <div class="divide_relative_field_2">
                    <div class="divide_relative_field_2_content" id="field_content_1_title">
                        タイトル1
                    </div>
                </div>
                <div class="divide_relative_field_2">
                    <div class="divide_relative_field_2_content" id="field_content_1_time">
                        <span id="field_content_1_time_hour">1</span>時間&nbsp;<span id="field_content_1_time_minute">0</span>分
                    </div>
                </div>
            </div>

            <div onclick="editContent(this)" id="field_content_2" style="border: solid; border-color: black; position: absolute; width: 200px; height: 75px; background-color: white; text-align:center; margin-left: 200px; margin-top: 300px;">
                <div class="divide_relative_field_2">
                    <div class="divide_relative_field_2_content" id="field_content_2_title">
                        タイトル2
                    </div>
                </div>
                <div class="divide_relative_field_2">
                    <div class="divide_relative_field_2_content" id="field_content_2_time">
                        <span id="field_content_2_time_hour">1</span>時間&nbsp;<span id="field_content_2_time_minute">0</span>分
                    </div>
                </div>
            </div>
        </div>
        <div class="registration_menu">
            <div class="registration_menu_parent">
                <div class="registration_menu_parent_content">
                    追加
                </div>
            </div>
            <div class="registration_menu_parent">
                <div class="registration_menu_parent_content">
                    移動
                </div>
            </div>
            <div class="registration_menu_parent">
                <div class="registration_menu_parent_content">
                    連結
                </div>
            </div>
            <div class="registration_menu_parent">
                <div class="registration_menu_parent_content">
                    削除
                </div>
            </div>
            <div class="registration_menu_parent_no_border">
                <div class="registration_menu_parent_content">
                    保存
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    alert("{{ $workflow_id }}");

    var editingContent="";

    function editContent(value) {
        if(editingContent != "") {
            finishEditContent(editingContent);
        }
        editingContent = value.id;
        var title_id = value.id + "_title_input";
        var hour_id = value.id + "_time_input_hour";
        var minute_id = value.id + "_time_input_minute";
        var title = document.getElementById(value.id + "_title").innerText;
        var hour = document.getElementById(value.id + "_time_hour").innerText;
        var minute = document.getElementById(value.id + "_time_minute").innerText;
        var minute_min = String(Number(hour) == 0 ? 15 : 0);
        document.getElementById(value.id + "_title").innerHTML = "<input id='" + title_id + "' value='" + title + "'>";
        document.getElementById(value.id + "_time").innerHTML = "<input type='number' id='" + hour_id + "' value='" + hour + "' min=0 style='width: 20%' onchange='changeRequiredMinuteLimit()'>時間&nbsp;<input type='number' id='"+ minute_id +"' value='" + minute + "' min=" + minute_min + " max='45' step='15' style='width: 20%'>分";
        value.setAttribute('onclick', '');
    }

    function finishEditContent(value) {
        var title_id = value + "_title";
        var hour_id = value + "_time_hour";
        var minute_id = value + "_time_minute";
        var title = document.getElementById(value + "_title_input").value;
        var hour = document.getElementById(value + "_time_input_hour").value;
        var minute = document.getElementById(value + "_time_input_minute").value;
        var func = "editContent(document.getElementById('"+ value +"'))";
        document.getElementById(value + "_title").innerHTML = title;
        document.getElementById(value + "_time").innerHTML = "<span id='" + hour_id + "'>" + hour +"</span>時間&nbsp;<span id='" + minute_id + "'>" + minute + "</span>分";
        document.getElementById(value).setAttribute('onclick', func);
        editingContent = "";
    }
</script>

@endsection