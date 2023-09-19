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
        <div class="registration_free_field" id="registration_free_field"></div>
        <div class="registration_menu">
            <div class="registration_menu_parent" id="registration_menu_add" onclick="changeMode('add')">
                <div class="registration_menu_parent_content">
                    追加
                </div>
            </div>
            <div class="registration_menu_parent" id="registration_menu_edit" onclick="changeMode('edit')">
                <div class="registration_menu_parent_content">
                    編集
                </div>
            </div>
            <div class="registration_menu_parent" id="registration_menu_move" onclick="changeMode('move')">
                <div class="registration_menu_parent_content">
                    移動
                </div>
            </div>
            <div class="registration_menu_parent" id="registration_menu_connect" onclick="changeMode('connect')">
                <div class="registration_menu_parent_content">
                    連結
                </div>
            </div>
            <div class="registration_menu_parent" id="registration_menu_delete" onclick="changeMode('delete')">
                <div class="registration_menu_parent_content">
                    削除
                </div>
            </div>
            <div class="registration_menu_parent_no_border" id="registration_menu_save" onclick="changeMode('save')">
                <div class="registration_menu_parent_content">
                    保存
                </div>
            </div>
        </div>
    </div>
    <form method="post" name="workflow_data" id="workflow_data" action=""></form>
</div>

<script>
    alert("{{ $workflow_id }}");

    var mode="";
    var editingContent="";
    var content_id = 1;

    var moveContent = function(e) {
        let target_rect = e.currentTarget.getBoundingClientRect();
        let x = e.clientX - target_rect.left;
        let y = e.clientY - target_rect.top;
        document.getElementById("registration_free_field").lastElementChild.style.marginLeft  = String(x) + "px";
        document.getElementById("registration_free_field").lastElementChild.style.marginTop = String(y) + "px";
    };

    var putContent = function(e) {
        document.getElementById("registration_free_field").removeEventListener("mousemove", moveContent);
        document.getElementById("registration_free_field").removeEventListener("click", putContent);
        document.getElementById("registration_menu_" + mode).style.backgroundColor = "";
        let titleData = document.createElement("input");
        titleData.type = "hidden";
        titleData.name = "field_" + String(content_id) + "_name";
        titleData.id = "field_" + String(content_id) + "_name";
        titleData.value = document.getElementById("field_content_" + String(content_id) + "_title").innerText;
        let hourData = document.createElement("input");
        hourData.type = "hidden";
        hourData.name = "field_" + String(content_id) + "_time_hour";
        hourData.id = "field_" + String(content_id) + "_time_hour";
        hourData.value = document.getElementById("field_content_" + String(content_id) + "_time_hour").innerText;
        let minuteData = document.createElement("input");
        minuteData.type = "hidden";
        minuteData.name = "field_" + String(content_id) + "_time_minute";
        minuteData.id = "field_" + String(content_id) + "_time_minute";
        minuteData.value = document.getElementById("field_content_" + String(content_id) + "_time_minute").innerText;
        document.getElementById("workflow_data").appendChild(titleData);
        document.getElementById("workflow_data").appendChild(hourData);
        document.getElementById("workflow_data").appendChild(minuteData);
        mode = "";
        content_id++;
    };

    function editContent(value) {
        if(mode != "edit") return;
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
        if(mode != "edit") return;
        if(value == "") return;
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

    function changeMode(modeName){
        if (mode != "") {
            document.getElementById("registration_menu_" + mode).style.backgroundColor = "";
            if(mode == "add") {
                document.getElementById("registration_free_field").removeEventListener("mousemove", moveContent);
                document.getElementById("registration_free_field").removeEventListener("click", putContent);
                document.getElementById("registration_free_field").lastElementChild.remove();
            }
            else if(mode == "edit") {
                finishEditContent(editingContent);
            }
        }
        mode = modeName;
        document.getElementById("registration_menu_" + mode).style.backgroundColor = "white";
        if(mode == "add") {
            let new_content = document.createElement("div");
            new_content.className = "registration_free_field_content";
            new_content.id = "field_content_" + String(content_id);
            new_content.style.marginLeft = "0px";
            new_content.style.marginTop = "0px";
            var func = "editContent(document.getElementById('"+ new_content.id +"'))";
            new_content.setAttribute("onclick", func);
            let new_content_title = document.createElement("div");
            new_content_title.className = "divide_relative_field_2";
            let new_content_title_value = document.createElement("div");;
            new_content_title_value.className = "divide_relative_field_2_content";
            new_content_title_value.id = "field_content_" + String(content_id) + "_title";
            new_content_title_value.innerHTML = "タイトル" + String(content_id);
            new_content_title.appendChild(new_content_title_value);
            new_content.appendChild(new_content_title);
            let new_content_time = document.createElement("div");
            new_content_time.className = "divide_relative_field_2";
            let new_content_time_value = document.createElement("div");;
            new_content_time_value.className = "divide_relative_field_2_content";
            new_content_time_value.id = "field_content_" + String(content_id) + "_time";
            new_content_time_value.innerHTML = "<span id='field_content_" + String(content_id) + "_time_hour'>1</span>時間&nbsp;<span id='field_content_" + String(content_id) + "_time_minute'>0</span>分";
            new_content_time.appendChild(new_content_time_value);
            new_content.appendChild(new_content_time);
            document.getElementById("registration_free_field").appendChild(new_content);

            document.getElementById("registration_free_field").addEventListener("mousemove", moveContent);
            document.getElementById("registration_free_field").addEventListener("click", putContent);
        }
    }

</script>

@endsection