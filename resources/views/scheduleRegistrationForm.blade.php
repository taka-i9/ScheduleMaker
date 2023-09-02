@extends('layouts.hometab')

@section('function_content')

<div class="registration_form">
    <div class="registration_form_header">
        <div class="form_header_content">
            スケジュール 新規登録
        </div>
    </div>
    <form method="POST" action="">
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    スケジュール名<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    開始時刻<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    終了時刻<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    繰り返し設定する<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="checkbox" class="checkbox">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    メモ<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    タグ<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
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
                <input type="checkbox" class="checkbox">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    ラベルの色<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="text">
            </div>
        </div>
        <div class="form_elements">
            <div class="form_element_name">
                <div class="form_element_content">
                    テンプレートにする<br>
                </div>
            </div>
            <div class="form_element_value">
                <input type="checkbox" class="checkbox">
            </div>
        </div>
        <button type="submit">登録</button>
    </form>
</div>

@endsection