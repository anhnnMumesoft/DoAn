@extends('layouts.client')
@section('title')
    {{$title}}
@endsection

@section('sidebar')
    @parent
    <h3> Home Sidebar</h3>
@endsection

@section('content')
    <h1> TRANG CHU </h1>
    <button type="button" class="show"> Show</button>
@endsection

@section('css')
    <style>
        header{
            background-color: blue;
            color:#fff;
        }
    </style>

@endsection

@section('js')
    <script>
        document.querySelector('.show').onclick = function(){
            alert('thanh cong');
        }
    </script>

@endsection
