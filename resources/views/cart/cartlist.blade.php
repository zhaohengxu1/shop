@extends('layouts.bootstrap')

@section('content')
    <h1><font>UID:{{$uid}} Welcome back!  Cart list</font></h1>
    <table border="1" class="table table-bordered">
        <tr>
            <td>商品id</td>
            <td>购买数量</td>
            <td>添加时间</td>
            <td>操作</td>
        </tr>
        @foreach($info as $v)
            <tr>
                <td>{{$v->goods_id}}</td>
                <td>{{$v->buy_number}}</td>
                <td>{{date('Y-m-d H:i:s',$v->c_time)}}</td>
                <td>
                    <a href="/delcart/{{$v->goods_id}}">删除</a>
                </td>
            </tr>
        @endforeach
    </table>
    <button class="btn btn-danger" ><a href="/userquit" style="text-decoration: none;color: white;">Quit</a></button>
    <button class="btn btn-danger" ><a href="/orderadd" style="text-decoration: none;color: white;">立即下单</a></button>
@endsection