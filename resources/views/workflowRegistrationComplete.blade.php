@extends('layouts.hometab')

@section('function_content')

<!--再度作成したい場合のリンクや、情報の確認、表示画面への遷移とかを追加する予定-->
登録完了
<br>

@if (!empty($id))
<form method="post" name="to_edit" action="{{ route('workflow.edit_form') }}">
    @csrf
    
    <input type="hidden" name="id" value="{{ $id }}">
    <a href="javascript:to_edit.submit()">編集画面へ</a>
</form>
@endif

@endsection