@extends('layouts.bootstrap')

@section('content')
    <h1><font>UID:{{$uid}} Welcome back!</font></h1>
    <table border="1" class="table table-bordered">
        <tr>
            <td>用户id</td>
            <td>用户姓名</td>
            <td>邮箱号</td>
            <td>总积分</td>
            <td>添加时间</td>
        </tr>
        @foreach($info as $v)
            <tr>
                <td>{{$v->uid}}</td>
                <td>{{$v->name}}</td>
                <td>{{$v->email}}</td>
                <td>{{$v->integral}}</td>
                <td>{{date('Y-m-d H:i:s',$v->reg_time)}}</td>
            </tr>
        @endforeach
    </table>
    <button class="btn btn-danger" ><a href="/userquit" style="text-decoration: none;color: white;">Quit</a></button>
@endsection