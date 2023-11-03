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
    <form method="post" name="workflow_data" id="workflow_data" action="{{ route('workflow.update') }}">
        @csrf
    </form>
</div>

<script>

    var id_head = "field_content_";
    var mode = "";
    var editingContent = "";
    var content_id = {{ $contents_num + 1 }};
    var connect_begin_id = "";
    var connect_relation = {};
    var connect_relation_rev = {};
    var movingContent = "";
    var block_once = false;
    var prev_left = "";
    var prev_top = "";

    function addForm(id) {
        let titleData = document.createElement("input");
        titleData.type = "hidden";
        titleData.name = id_head + id + "_title_form";
        titleData.id = id_head + id + "_title_form";
        titleData.value = document.getElementById(id_head + id + "_title").innerText;
        let hourData = document.createElement("input");
        hourData.type = "hidden";
        hourData.name = id_head + id + "_time_hour_form";
        hourData.id = id_head + id + "_time_hour_form";
        hourData.value = document.getElementById(id_head + id + "_time_hour").innerText;
        let minuteData = document.createElement("input");
        minuteData.type = "hidden";
        minuteData.name = id_head + id + "_time_minute_form";
        minuteData.id = id_head + id + "_time_minute_form";
        minuteData.value = document.getElementById(id_head + id + "_time_minute").innerText;
        let marginLeftData = document.createElement("input");
        marginLeftData.type = "hidden";
        marginLeftData.name = id_head + id + "_margin_left_form";
        marginLeftData.id = id_head + id + "_margin_left_form";
        marginLeftData.value = document.getElementById(id_head + id).style.marginLeft;
        let marginTopData = document.createElement("input");
        marginTopData.type = "hidden";
        marginTopData.name = id_head + id + "_margin_top_form";
        marginTopData.id = id_head + id + "_margin_top_form";
        marginTopData.value = document.getElementById(id_head + id).style.marginTop;
        document.getElementById("workflow_data").appendChild(titleData);
        document.getElementById("workflow_data").appendChild(hourData);
        document.getElementById("workflow_data").appendChild(minuteData);
        document.getElementById("workflow_data").appendChild(marginLeftData);
        document.getElementById("workflow_data").appendChild(marginTopData);
    }

    function removeContentComplete(id) {
        if(id in connect_relation) {
            Object.keys(connect_relation[id]).forEach(function(v) {
                document.getElementById("connection_" + id + "_" + v).remove();
                document.getElementById("connection_" + id + "_" + v + "_form").remove();
                delete connect_relation_rev[v][id];
            });
            delete connect_relation[id];
        }
        if(id in connect_relation_rev) {
            Object.keys(connect_relation_rev[id]).forEach(function(v) {
                document.getElementById("connection_" + v + "_" + id).remove();
                document.getElementById("connection_" + v + "_" + id + "_form").remove();
                delete connect_relation[v][id];
            });
            delete connect_relation_rev[id];
        }
        document.getElementById(id_head + id).remove();
        document.getElementById(id_head + id + "_title_form").remove();
        document.getElementById(id_head + id + "_time_hour_form").remove();
        document.getElementById(id_head + id + "_time_minute_form").remove();
        document.getElementById(id_head + id + "_margin_left_form").remove();
        document.getElementById(id_head + id + "_margin_top_form").remove();
    }

    function removeConnection(id) {
        if(id in connect_relation) {
            Object.keys(connect_relation[id]).forEach(function(v) {
                document.getElementById("connection_" + id + "_" + v + "_1").remove();
                document.getElementById("connection_" + id + "_" + v + "_2").remove();
                document.getElementById("connection_" + id + "_" + v + "_3").remove();
            });
        }
        if(id in connect_relation_rev) {
            Object.keys(connect_relation_rev[id]).forEach(function(v) {
                document.getElementById("connection_" + v + "_" + id + "_1").remove();
                document.getElementById("connection_" + v + "_" + id + "_2").remove();
                document.getElementById("connection_" + v + "_" + id + "_3").remove();
            });
        }
    }

    function attachConnection(id) {
        if(id in connect_relation) {
            Object.keys(connect_relation[id]).forEach(function(v) {
                createArrow(id, v);
            });
        }
        if(id in connect_relation_rev) {
            Object.keys(connect_relation_rev[id]).forEach(function(v) {
                createArrow(v, id);
            });
        }
    }

    var moveContent = function(e) {
        let target_rect = e.currentTarget.getBoundingClientRect();
        let x = e.clientX - target_rect.left;
        let y = e.clientY - target_rect.top;
        document.getElementById(id_head + movingContent).style.marginLeft  = String(x) + "px";
        document.getElementById(id_head + movingContent).style.marginTop = String(y) + "px";
    };

    var putContent = function(e) {
        if(block_once) {
            block_once = false;
            return;
        }
        //重なっていないかの確認
        let moving = document.getElementById(id_head + movingContent);
        let target_rect = e.currentTarget.getBoundingClientRect();
        let x = Number(e.clientX - target_rect.left);
        let y = Number(e.clientY - target_rect.top);
        let w = Number(moving.style.width.substr(0, moving.style.width.length - 2));
        let h = Number(moving.style.height.substr(0, moving.style.height.length - 2));
        let is_overlaped = false;
        for(let i = 1; i < content_id; i++) {
            if(i == movingContent || document.getElementById(id_head + i) == null) continue;
            let content = document.getElementById(id_head + i);
            let u = Number(content.style.marginLeft.substr(0, content.style.marginLeft.length - 2));
            let v = Number(content.style.marginTop.substr(0, content.style.marginTop.length - 2));
            let content_w = Number(content.style.width.substr(0, content.style.width.length - 2));
            let content_h = Number(content.style.height.substr(0, content.style.height.length - 2));
            if(u - w <= x && x <= u + content_w && v - h <= y && y <= v + content_h) {
                is_overlaped = true;
            }
        }
        if(is_overlaped) {
            alert('重なっています。');
            return;
        }
        document.getElementById("registration_free_field").removeEventListener("mousemove", moveContent);
        document.getElementById("registration_free_field").removeEventListener("click", putContent);
        if(mode == "add") { 
            document.getElementById("registration_menu_" + mode).style.backgroundColor = "";
            addForm(movingContent);
            content_id++;
            mode = "";
        }
        else if(mode == "move") {
            document.getElementById(id_head + movingContent + "_margin_left_form").value = document.getElementById(id_head + movingContent).style.marginLeft;
            document.getElementById(id_head + movingContent + "_margin_top_form").value = document.getElementById(id_head + movingContent).style.marginTop;
            //関連するリンクを再生する
            attachConnection(movingContent);
            prev_left = "";
            prev_top = "";
        }
        movingContent = "";
    };

    function editContent(value) {
        if(mode != "edit" && mode != "move" && mode != "connect" && mode != "delete") return;
        if(editingContent != "") {
            finishEditContent(editingContent);
        }
        if(mode == "edit") {
            if(value.className == "registration_free_field_content_done") {
                alert('完了済みのタスクは編集できません。');
                return;
            }
            editingContent = value.id;
            var title_id = value.id + "_title_input";
            var hour_id = value.id + "_time_input_hour";
            var minute_id = value.id + "_time_input_minute";
            var title = document.getElementById(value.id + "_title").innerText;
            var hour = document.getElementById(value.id + "_time_hour").innerText;
            var minute = document.getElementById(value.id + "_time_minute").innerText;
            var minute_min = String(Number(hour) == 0 ? 15 : 0);
            var func = 'changeRequiredMinuteLimit("' + value.id + '")';
            document.getElementById(value.id + "_title").innerHTML = "<input id='" + title_id + "' value='" + title + "'>";
            document.getElementById(value.id + "_time").innerHTML = "<input type='number' id='" + hour_id + "' value='" + hour + "' min=0 style='width: 20%' onchange='" + func + "'>時間&nbsp;<input type='number' id='"+ minute_id +"' value='" + minute + "' min=" + minute_min + " max='45' step='15' style='width: 20%'>分";
            value.setAttribute('onclick', '');
        }
        else if(mode == "move") {
            if(movingContent == "") {
                movingContent = value.id.substr(id_head.length);
                prev_left = document.getElementById(id_head + movingContent).style.marginLeft;
                prev_top = document.getElementById(id_head + movingContent).style.marginTop;
                document.getElementById("registration_free_field").addEventListener("mousemove", moveContent);
                document.getElementById("registration_free_field").addEventListener("click", putContent);
                //このクリックで配置する際の関数が呼ばれるのを防止する
                block_once = true;
                //関連するリンクの削除
                removeConnection(movingContent);
            }
        }
        else if(mode == "connect") {
            let input_id = value.id.substr(id_head.length);
            if(connect_begin_id == "") {
                connect_begin_id = input_id;
                value.style.borderColor = "red";
            }
            //同じ要素を選択した場合、選択をキャンセル
            else if(connect_begin_id == input_id) {
                connect_begin_id = "";
                value.style.borderColor = "black";
            }
            else {
                if(value.className == "registration_free_field_content_done") {
                    alert('完了済みのタスクを選択することはできません。');
                    return;
                }
                let connection_search_queue = [];
                let searched_id = {};
                let check = false;
                connection_search_queue.push(input_id);
                while(connection_search_queue.length != 0) {
                    let v = connection_search_queue.shift();
                    if(v == connect_begin_id) {
                        check = true;
                        break;
                    }
                    if(v in connect_relation) {
                        Object.keys(connect_relation[v]).forEach(function(next) {
                            if(!(next in searched_id)) {
                                searched_id[next] = true;
                                connection_search_queue.push(next);
                            }
                        });
                    }
                }
                if(check) {
                    alert("ループが生じるため、生成できません。\n接続を見直してください。");
                }
                //既に作成されているリンクの場合、削除を選択できる。
                else if(connect_begin_id in connect_relation && input_id in connect_relation[connect_begin_id]) {
                    if(window.confirm("このリンクは既に作成されています。削除しますか?")) {
                        document.getElementById("connection_" + connect_begin_id + "_" + input_id).remove();
                        document.getElementById("connection_" + connect_begin_id + "_" + input_id + "_form").remove();
                        delete connect_relation[connect_begin_id][input_id];
                        delete connect_relation_rev[input_id][connect_begin_id];
                        document.getElementById(id_head + connect_begin_id).style.borderColor = "black";
                        connect_begin_id = "";
                    }
                }
                else {
                    createArrowForm(connect_begin_id, input_id);
                    //矢印を生成
                    createArrow(connect_begin_id, input_id);
                    document.getElementById(id_head + connect_begin_id).style.borderColor = "black";
                    connect_begin_id = "";
                }           
            }
        }
        else if(mode == "delete") {
            if(value.className == "registration_free_field_content_done") {
                alert('完了済みのタスクを削除することはできません。');
                return;
            }
            if(window.confirm("この要素を削除します。\nよろしいですか?")) {
                removeContentComplete(value.id.substr(id_head.length));
            }
        }
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
        document.getElementById(value + "_title_form").value = title;
        document.getElementById(value + "_time_hour_form").value = hour;
        document.getElementById(value + "_time_minute_form").value = minute;
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
            else if(mode == "move") {
                if(movingContent != "") {
                    //復元する
                    document.getElementById("registration_free_field").removeEventListener("mousemove", moveContent);
                    document.getElementById("registration_free_field").removeEventListener("click", putContent);
                    document.getElementById(id_head + movingContent).style.marginLeft  = prev_left;
                    document.getElementById(id_head + movingContent).style.marginTop = prev_top;
                    attachConnection(movingContent);
                    movingContent = "";
                }
            }
            else if(mode == "connect") {
                if(connect_begin_id != "") {
                    document.getElementById(id_head + connect_begin_id).style.borderColor = "black";
                    connect_begin_id = "";
                }
            }
        }
        mode = modeName;
        document.getElementById("registration_menu_" + mode).style.backgroundColor = "white";
        if(mode == "add") {
            movingContent = String(content_id);
            createContent(content_id, "タイトル" + String(content_id), "1", "0", "0px", "0px", false);

            document.getElementById("registration_free_field").addEventListener("mousemove", moveContent);
            document.getElementById("registration_free_field").addEventListener("click", putContent);
        }
        if(mode == "save") {
            let contents_num_form = document.createElement("input");
            contents_num_form.type = "hidden";
            contents_num_form.name = "contents_num";
            contents_num_form.value = content_id - 1;
            document.getElementById("workflow_data").appendChild(contents_num_form);
            let workflow_id_form = document.createElement("input");
            workflow_id_form.type = "hidden";
            workflow_id_form.name = "workflow_id";
            workflow_id_form.value = {{ $workflow_id }};
            document.getElementById("workflow_data").appendChild(workflow_id_form);
            document.getElementById("workflow_data").submit();
        }
    }

    function changeRequiredMinuteLimit(value_id) {
        if(document.getElementById(value_id + "_time_input_hour").value == 0) {
            document.getElementById(value_id + "_time_input_minute").min = 15;
            if(document.getElementById(value_id + "_time_input_minute").value == 0) {
                document.getElementById(value_id + "_time_input_minute").value = 15;
            }
        }
        else {
            document.getElementById(value_id + "_time_input_minute").min = 0;
        }
        if(document.getElementById(value_id + "_time_input_minute").value == "") {
            document.getElementById(value_id + "_time_input_minute").value = 0;
        }
    }

    function createContent(id, title, time_hour, time_minute, margin_left, margin_top, is_done) {
        let new_content = document.createElement("div");
        if(is_done) new_content.className = "registration_free_field_content_done";
        else new_content.className = "registration_free_field_content";
        new_content.id = id_head + String(id);
        new_content.style.marginLeft = margin_left;
        new_content.style.marginTop = margin_top;
        //現在のところ固定長としているが、幅調整可能にするかもしれない
        new_content.style.width = '200px';
        new_content.style.height = '75px';
        var func = "editContent(document.getElementById('"+ new_content.id +"'))";
        new_content.setAttribute("onclick", func);
        let new_content_title = document.createElement("div");
        new_content_title.className = "divide_relative_field_2";
        let new_content_title_value = document.createElement("div");;
        new_content_title_value.className = "divide_relative_field_2_content";
        new_content_title_value.id = id_head + String(id) + "_title";
        new_content_title_value.innerHTML = title;
        new_content_title.appendChild(new_content_title_value);
        new_content.appendChild(new_content_title);
        let new_content_time = document.createElement("div");
        new_content_time.className = "divide_relative_field_2";
        let new_content_time_value = document.createElement("div");;
        new_content_time_value.className = "divide_relative_field_2_content";
        new_content_time_value.id = id_head + String(id) + "_time";
        new_content_time_value.innerHTML = "<span id='" + id_head + String(id) + "_time_hour'>" + time_hour + "</span>時間&nbsp;<span id='" + id_head + String(id) + "_time_minute'>" + time_minute + "</span>分";
        new_content_time.appendChild(new_content_time_value);
        new_content.appendChild(new_content_time);
        document.getElementById("registration_free_field").appendChild(new_content);
    }

    function createArrowForm(start, end) {
        if(!(start in connect_relation)) {
            connect_relation[start] = {};
        }
        if(!(end in connect_relation_rev)) {
            connect_relation_rev[end] = {};
        }
        connect_relation[start][end] = true;
        connect_relation_rev[end][start] = true;
        let newRelation = document.createElement("input");
        newRelation.type = "hidden";
        newRelation.name = "connection[" + start + "][" + end + "]";
        newRelation.id = "connection_" + start + "_" + end + "_form";
        newRelation.value = true;
        document.getElementById("workflow_data").appendChild(newRelation);
    }

    function createArrow(start, end) {
        let begin_content = document.getElementById(id_head + start);
        let end_content = document.getElementById(id_head + end);
        let begin_x = Number(begin_content.style.marginLeft.substring(0, begin_content.style.marginLeft.length - 2));
        let begin_y = Number(begin_content.style.marginTop.substring(0, begin_content.style.marginTop.length - 2));
        let end_x = Number(end_content.style.marginLeft.substring(0, end_content.style.marginLeft.length - 2));
        let end_y = Number(end_content.style.marginTop.substring(0, end_content.style.marginTop.length - 2));
        let diff_x = end_x - begin_x;
        let diff_y = end_y - begin_y;
        let arrow_begin_x;
        let arrow_begin_y;
        let arrow_end_x;
        let arrow_end_y;
        let begin_width = Number(begin_content.style.width.substr(0, begin_content.style.width.length - 2));
        let begin_height = Number(begin_content.style.height.substr(0, begin_content.style.height.length - 2));
        let end_width = Number(end_content.style.width.substr(0, end_content.style.width.length - 2));
        let end_height = Number(end_content.style.height.substr(0, end_content.style.height.length - 2));
        let arrow1 = document.createElement('div');
        arrow1.id = 'connection_' + start + '_' + end + '_1';
        arrow1.className = 'connection_arrow';
        document.getElementById("registration_free_field").appendChild(arrow1);
        let arrow2 = document.createElement('div');
        arrow2.id = 'connection_' + start + '_' + end + '_2';
        arrow2.className = 'connection_arrow';
        document.getElementById("registration_free_field").appendChild(arrow2);
        let arrow3 = document.createElement('div');
        arrow3.id = 'connection_' + start + '_' + end + '_3';
        arrow3.className = 'connection_arrow triangle_arrow';
        document.getElementById("registration_free_field").appendChild(arrow3);
        let pad1 = 0;
        let pad2 = 0;
        //上下でつなぐ場合
        if(Math.abs(diff_y) > Math.max(begin_height, end_height)) {
            //始点が上側の場合
            if(diff_y > 0) {
                arrow_begin_x = begin_x + begin_width / 2;
                arrow_begin_y = begin_y + begin_height;
                arrow_end_x = end_x + end_width / 2;
                arrow_end_y = end_y;
                arrow3.style.marginLeft = String(arrow_end_x - 10 + 1.5) + 'px';
                arrow3.style.marginTop = String(arrow_end_y - 20) + 'px';
                arrow3.style.borderTop = '20px solid';
                pad1 = 10;
            }
            //始点が下側の場合
            else {
                arrow_begin_x = begin_x + begin_width / 2;
                arrow_begin_y = begin_y;
                arrow_end_x = end_x + end_width / 2;
                arrow_end_y = end_y + end_height;
                arrow3.style.marginLeft = String(arrow_end_x - 10 + 1.5) + 'px';
                arrow3.style.marginTop = String(arrow_end_y - 10) + 'px';
                arrow3.style.borderBottom = '20px solid';
                pad2 = 10;
            }

            arrow1.style.width = String(Math.abs(arrow_begin_x - arrow_end_x)) + 'px';
            arrow1.style.height = String(Math.abs(arrow_begin_y - arrow_end_y) / 2 - pad1) + 'px';
            arrow1.style.marginLeft = String(Math.min(arrow_begin_x, arrow_end_x)) + 'px';
            arrow1.style.marginTop = String((arrow_begin_y + arrow_end_y) / 2) + 'px';
            if(diff_x * diff_y >= 0) arrow1.style.borderRight = 'solid';
            else arrow1.style.borderLeft = 'solid';
            arrow1.style.borderTop = 'solid';

            arrow2.style.width = String(Math.abs(arrow_begin_x - arrow_end_x)) + 'px';
            arrow2.style.height = String(Math.abs(arrow_begin_y - arrow_end_y) / 2 - pad2) + 'px';
            arrow2.style.marginLeft = String(Math.min(arrow_begin_x, arrow_end_x)) + 'px';
            arrow2.style.marginTop = String(Math.min(arrow_begin_y, arrow_end_y) + pad2) + 'px';
            if(diff_x * diff_y >= 0) arrow2.style.borderLeft = 'solid';
            else arrow2.style.borderRight = 'solid';

        }
        //左右でつなぐ場合
        else {
            //始点が左側の場合
            if(diff_x > 0) {
                arrow_begin_x = begin_x + begin_width;
                arrow_begin_y = begin_y + begin_height / 2;
                arrow_end_x = end_x;
                arrow_end_y = end_y + end_height / 2;
                arrow3.style.marginLeft = String(arrow_end_x - 20) + 'px';
                arrow3.style.marginTop = String(arrow_end_y - 10 + 1.5) + 'px';
                arrow3.style.borderLeft = '20px solid';
                pad2 = 10;
            }
            //始点が右側の場合
            else {
                arrow_begin_x = begin_x;
                arrow_begin_y = begin_y + begin_height / 2;
                arrow_end_x = end_x + end_width;
                arrow_end_y = end_y + end_height / 2;
                arrow3.style.marginLeft = String(arrow_end_x - 10) + 'px';
                arrow3.style.marginTop = String(arrow_end_y - 10 + 1.5) + 'px';
                arrow3.style.borderRight = '20px solid';
                pad1 = 10;
            }
            
            arrow1.style.width = String(Math.abs(arrow_begin_x - arrow_end_x) / 2 - pad1) + 'px';
            arrow1.style.height = String(Math.abs(arrow_begin_y - arrow_end_y)) + 'px';
            arrow1.style.marginLeft = String(Math.min(arrow_begin_x, arrow_end_x) + pad1) + 'px';
            arrow1.style.marginTop = String(Math.min(arrow_begin_y, arrow_end_y)) + 'px';
            arrow1.style.borderRight = 'solid';
            if(diff_x * diff_y >= 0) arrow1.style.borderTop = 'solid';
            else arrow1.style.borderBottom = 'solid';
            
            arrow2.style.width = String(Math.abs(arrow_begin_x - arrow_end_x) / 2 - pad2) + 'px';
            arrow2.style.height = String(Math.abs(arrow_begin_y - arrow_end_y)) + 'px';
            arrow2.style.marginLeft = String((arrow_begin_x + arrow_end_x) / 2) + 'px';
            arrow2.style.marginTop = String(Math.min(arrow_begin_y, arrow_end_y)) + 'px';
            if(diff_x * diff_y >= 0) arrow2.style.borderBottom = 'solid';
            else arrow2.style.borderTop = 'solid';
        }
    }

    if("{{ $contents_num }}" != "0") {
        let contents_ids = [<?php 
            for($i = 0; $i < count($contents_data); $i++) {
                print '{';
                foreach($contents_data[$i] as $key => $value) {
                    print '"'.$key.'": "'.$value.'",';
                }
                print '},';
            } 
        ?>];
        for(let i = 0; i < contents_ids.length; i++) {
            createContent(contents_ids[i]['id'], contents_ids[i]['name'], contents_ids[i]['hour'], contents_ids[i]['minute'], contents_ids[i]['margin_left'], contents_ids[i]['margin_top'], contents_ids[i]['is_done']);
            addForm(contents_ids[i]['id']);
        }
        let connections = [<?php 
            foreach($connection as $start => $ends) {
                foreach(array_keys($ends) as $end) {
                    print '"'.$start.'","'.$end.'",';
                }
            } 
        ?>];
        for(let i = 0; i < connections.length / 2; i++) {
            createArrow(connections[2 * i], connections[2 * i + 1]);
            createArrowForm(connections[2 * i], connections[2 * i + 1]);
        }
    }

    window.onload = function() {
        if("{{ $updated }}") {
            alert("保存しました。");
        }
    };

</script>

@endsection