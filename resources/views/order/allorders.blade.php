@extends('layouts.bootstrap')

@section('content')
    <h1>UID: <font color="red">{{$uid}}</font>我的订单</h1>
    <form>
        @foreach($data as $v)
            <table border="1" class="table table-bordered" style="width: 600px;">
                <tr>
                    <td>订单号</td>
                    <td><a href="/orderdetail/{{$v->order_num}}">{{$v->order_num}}</a></td>
                </tr>
                <tr>
                    <td>订单总价</td>
                    <td>{{$v->order_amount}}￥</td>
                </tr>
                <tr>
                    <td>添加时间</td>
                    <td>{{date("Y-m-d H:i:s",$v->c_time)}}</td>
                </tr>
                <tr>
                    <td>订单状态</td>
                    @if ($v->order_status === 1)
                        <td>待支付！</td>
                    @elseif ($v->order_status ===2)
                        <td><font color="blue">已支付！</font></td>
                    @else
                        <td>订单已取消！</td>
                    @endif
                </tr>
            </table>
        @endforeach
    </form>
    <button class="btn btn-danger"><a href="/goodslist" style="text-decoration: none; color: #ffffff;">继续购买</a></button>
@endsection
@section('footer')
    @parent

@endsection
