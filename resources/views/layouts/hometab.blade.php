@extends('layouts.app')

@section('head')

<link rel="stylesheet" href="{{ asset('/css/home.css') }}">

@endsection

@section('content')

<div class="home_overall" style="display: flex;">
    <div class="home_menu" id="home_menu">
        <div class="menu_parent" id="menu_schedule" onclick="changeStateMenuChild('schedule')">
            <div class="menu_parent_content">
                スケジュール
            </div>
        </div>
        <div class="menu_child schedule_content" onclick="linkToScheduleRegistraion()">
            <div class="menu_child_content">
                新規作成
            </div>
        </div>
        <div class="menu_child schedule_content" onclick="linkToScheduleList()">
            <div class="menu_child_content">
                編集・管理
            </div>
        </div>
        <div class="menu_parent" id="menu_todo" onclick="changeStateMenuChild('todo')">
            <div class="menu_parent_content">
                To do
            </div>
        </div>
        <div class="menu_child todo_content" onclick="linkToToDoRegistraion()">
            <div class="menu_child_content">
                新規作成
            </div>
        </div>
        <div class="menu_child schedule_content" onclick="linkToToDoList()">
            <div class="menu_child_content">
                編集・管理
            </div>
        </div>
        <div class="menu_parent" id="menu_workfrow" onclick="changeStateMenuChild('workflow')">
            <div class="menu_parent_content">
                ワークフロー
            </div>
        </div>
        <div class="menu_child workflow_content" onclick="linkToWorkFlowRegistration()">
            <div class="menu_child_content">
                新規作成
            </div>
        </div>
        <div class="menu_child workflow_content" onclick="linkToWorkFlowList()">
            <div class="menu_child_content">
                編集・管理
            </div>
        </div>
        <div class="menu_parent" id="menu_representation" onclick="changeStateMenuChild('representation')">
            <div class="menu_parent_content">
                表示
            </div>
        </div>
        <div class="menu_child representation_content" onclick="linkToRepresentationSchedule()">
            <div class="menu_child_content">
                スケジュール
            </div>
        </div>
        <div class="menu_child representation_content" onclick="linkToRepresentationToDo()">
            <div class="menu_child_content">
                To do
            </div>
        </div>
        <div class="menu_child representation_content" onclick="linkToRepresentationToday()">
            <div class="menu_child_content">
                今日の予定
            </div>
        </div>
        <div class="menu_parent" id="menu_adjustment" onclick="changeStateMenuChild('adjustment')">
            <div class="menu_parent_content">
                調整
            </div>
        </div>
        <div class="menu_child adjustment_content">
            <div class="menu_child_content">
                参加ルーム一覧
            </div>
        </div>
        <div class="menu_child adjustment_content">
            <div class="menu_child_content">
                ルームを新規作成
            </div>
        </div>
        <div class="menu_child adjustment_content">
            <div class="menu_child_content">
                受信メッセージ
            </div>
        </div>
        <div class="menu_child adjustment_content">
            <div class="menu_child_content">
                メンバー管理
            </div>
        </div>
        
        <div class="menu_parent" id="menu_setting" onclick="changeStateMenuChild('setting')">
            <div class="menu_parent_content">
                設定
            </div>
        </div>
    </div>
    <div class="menu_expansion" onclick="changeStateMenu()">
       
    </div>
    <div class="home_body">
        <div class="home_content">
            @yield('function_content')
        </div>
        
    </div>
</div>

<script>
function changeStateMenu() {
    if(document.getElementById("home_menu").hidden){
        document.getElementById("home_menu").hidden = false;
        document.querySelector('.home_body').style.width = '55%';
    }
    else{
        document.getElementById("home_menu").hidden = true;
        document.querySelector('.home_body').style.width = '95%';
    }
}

function changeStateMenuChild(parentName) {
    var className = parentName + "_content";
    var childList = document.getElementsByClassName(className);
    for(var i=0;i<childList.length;i++){
        childList[i].hidden = childList[i].hidden ? false : true;
    }
}

function linkToScheduleRegistraion() {
    location.href = "<?php echo(url('/home/schedule/new')); ?>";
}

function linkToScheduleList() {
    location.href = "<?php echo(url('/home/schedule/list')) ?>";
}

function linkToToDoRegistraion() {
    location.href = "<?php echo(url('/home/todo/new')); ?>";
}

function linkToToDoList() {
    location.href = "<?php echo(url('/home/todo/list')) ?>";
}

function linkToWorkFlowRegistration() {
    location.href = "<?php echo(url('/home/workflow/new')); ?>";
}

function linkToWorkFlowList() {
    location.href = "<?php echo(url('/home/workflow/list')); ?>";
}

function linkToRepresentationSchedule() {
    location.href = "<?php echo(url('/home/representation/schedule?representation_style=month')); ?>";
}

function linkToRepresentationToDo() {
    location.href = "<?php echo(url('/home/representation/todo')); ?>";
}

function linkToRepresentationToday() {
    location.href = "<?php echo(url('/home/representation/today')); ?>";
}
</script>

@endsection