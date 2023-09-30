@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 編集
        </div>
    </div>
    <div class="registration_form_content">
        <form method="POST" id="registration_form" action="{{ route('schedule.add') }}" class="registration_form_content">
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
            <div class="form_elements">
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
            <div class="form_elements">
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
            <input type="hidden" name="list_display_style" id="list_display_style">
            <input type="hidden" name="list_begin" id="list_begin">
            <input type="hidden" name="list_end" id="list_end">
            <button type="submit">保存</button>
            &nbsp;
            <button type="button" onclick="backList()">一覧に戻る</button>
        </form>
        <form method="GET" id="back_list">
            <input type="hidden" name="list_status" id="list_status_back">
            <input type="hidden" name="list_display_style" id="list_display_style_back">
            <input type="hidden" name="list_begin" id="list_begin_back">
            <input type="hidden" name="list_end" id="list_end_back">
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
            echo "document.getElementById('begin_date').value = data['begin_time'].substr(0, 10);\n";
            echo "document.getElementById('begin_time').value = data['begin_time'].substr(-8, 5);\n";
            echo "document.getElementById('end_date').value = data['end_time'].substr(0, 10);\n";
            echo "document.getElementById('end_time').value = data['end_time'].substr(-8, 5);\n";
            echo "document.getElementById('memo').value = data['memo'];\n";
            echo "document.getElementById('is_duplication').checked = data['is_duplication'];\n";
            echo "document.getElementById('color').value = data['color'];\n";

            echo "document.getElementById('id').value = '".$data['id']."';\n";
            echo "document.getElementById('list_status').value = '".$list_status."';\n";
            echo "document.getElementById('list_display_style').value = '".$list_display_style."';\n";
            echo "document.getElementById('list_begin').value = '".$list_begin."';\n";
            echo "document.getElementById('list_end').value = '".$list_end."';\n";

            echo "document.getElementById('list_status_back').value = '".$list_status."';\n";
            echo "document.getElementById('list_display_style_back').value = '".$list_display_style."';\n";
            echo "document.getElementById('list_begin_back').value = '".$list_begin."';\n";
            echo "document.getElementById('list_end_back').value = '".$list_end."';\n";
        }
        else {
            echo "document.getElementById('id').value = '".old('id')."'\n";
            echo "document.getElementById('list_status').value = '".old('list_status')."'\n";
            echo "document.getElementById('list_display_style').value = '".old('list_display_style')."'\n";
            echo "document.getElementById('list_begin').value = '".old('list_begin')."'\n";
            echo "document.getElementById('list_end').value = '".old('list_end')."'\n";

            echo "document.getElementById('list_status_back').value = '".old('list_status_back')."'\n";
            echo "document.getElementById('list_display_style_back').value = '".old('list_display_style_back')."'\n";
            echo "document.getElementById('list_begin_back').value = '".old('list_begin_back')."'\n";
            echo "document.getElementById('list_end_back').value = '".old('list_end_back')."'\n";
        }
        ?>  
    };

    function listSetting(type) {
        let list_status_element = document.getElementById('list_status' + type);
        let list_display_style_element = document.getElementById('list_display_style' + type);
        let list_begin_element = document.getElementById('list_begin' + type);
        let list_end_element = document.getElementById('list_end' + type);
        if(list_status_element.value == 'normal') {
            if(list_display_style_element.value != 'custom') {
                list_begin_element.remove();
                list_end_element.remove();
            }
        }
        else {
            delete list_display_style_element.remove();
            delete list_begin_element.remove();
            delete list_end_element.remove();
        }
    }

    function backList() {
        listSetting('_back');
        document.getElementById('back_list').action = "{{ route('schedule.list') }}";
        document.getElementById('back_list').submit();
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
</script>

@endsection