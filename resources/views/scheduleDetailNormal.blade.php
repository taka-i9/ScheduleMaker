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
                    <div class="form_element_value" id="is_duplecation"></div>
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
            <button type="button" onclick="toEdit()">編集</button>
            &nbsp;
            <button type="button" onclick="history.back()">一覧に戻る</button>
        </form>
    </div>
</div>

<script>
    var data = {<?php 
        foreach($data as $key => $value) {
            print '"'.$key.'": "'.$value.'",';
        }
    ?>};

    window.onload = function() {
        document.getElementById('name').innerHTML = '<div class="form_element_text">' + data['name'] + '</div>';
        document.getElementById('begin_time').innerHTML = '<div class="form_element_text">' + data['begin_time'] + '</div>';
        document.getElementById('end_time').innerHTML = '<div class="form_element_text">' + data['end_time'] + '</div>';
        document.getElementById('memo').innerHTML = '<div class="form_element_text">' + data['memo'] + '</div>';
        document.getElementById('is_duplecation').innerHTML = '<div class="form_element_text">' + (data['is_duplecation']!="" ? '許可する' : '許可しない') + '</div>';
        document.getElementById('color').value = data['color'];
    };
</script>

@endsection