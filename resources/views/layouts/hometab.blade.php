@extends('layouts.app')

@section('head')

<link rel="stylesheet" href="{{ asset('/css/home.css') }}">

@endsection

@section('content')

<div class="home_overall" style="display: flex;">
    <div class="home_menu" id="home_menu">
        表示1
    </div>
    <div class="menu_expansion" onclick="changeStateMenu('')">
    </div>
    <div class="home_body">
        <div class="home_content">
            表示2
            @yield('function_content')
        </div>
        
    </div>
</div>

<script>
function changeStateMenu(){
    if(document.getElementById("home_menu").hidden){
        document.getElementById("home_menu").hidden=false;
        document.querySelector('.home_body').style.width = '55%';
    }
    else{
        document.getElementById("home_menu").hidden=true;
        document.querySelector('.home_body').style.width = '95%';
    }
}
</script>

@endsection