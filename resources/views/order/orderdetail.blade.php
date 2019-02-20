@extends('layouts.bootstrap')

@section('content')
    <form>
        <h2>订单生成成功</h2>
        <h5>订单号：<font color="red">{{$order_num}}</font></h5>
        @foreach($info as $v)
            <table border="1" class="table table-bordered" style="width: 600px;">
                <tr>
                    <td style="width: 150px;">商品名称</td>
                    <td>{{$v->goods_name}}</td>
                </tr>
                <tr>
                    <td>单价</td>
                    <td>{{$v->goods_price}}￥</td>
                </tr>
                <tr>
                    <td>购买数量</td>
                    <td>{{$v->buy_number}}件</td>
                </tr>
                <tr>
                    <td>小计</td>
                    <td class="amount">{{$v->buy_number*$v->goods_price}}￥</td>
                </tr>
            </table>
        @endforeach
        <h3 id="amount">总计：</h3>
    </form>
    @if ($order_status == 1)
        <button class="btn btn-danger"><a href="/goodslist" style="text-decoration: none; color: #ffffff;">继续购买</a></button>
        <button class="btn btn-danger"><a href="/alipay/{{$order_num}}" style="text-decoration: none; color: #ffffff;">去付款</a></button>
        <button class="btn btn-danger"><a href="/orderdel/{{$order_num}}/{{$order_status}}" style="text-decoration: none; color: #ffffff;">取消订单   </a></button>
        <button class="btn btn-danger"><a href="/allorders" style="text-decoration: none; color: #ffffff;">我的全部订单</a></button>
    @elseif ($order_status == 2)
        <button class="btn btn-danger"><a href="/goodslist" style="text-decoration: none; color: #ffffff;">继续购买</a></button>
        <button class="btn btn-danger"><a href="/orderdel/{{$order_num}}/{{$order_status}}" style="text-decoration: none; color: #ffffff;">申请退款</a></button>
        <button class="btn btn-danger"><a href="/allorders" style="text-decoration: none; color: #ffffff;">我的全部订单</a></button>
    @else
        <button class="btn btn-danger"><a href="/goodslist" style="text-decoration: none; color: #ffffff;">继续购买</a></button>
        <button class="btn btn-danger"><a href="/allorders" style="text-decoration: none; color: #ffffff;">我的全部订单</a></button>
    @endif

@endsection
@section('footer')
    @parent
    <script>
        $(function () {
            var amount=0
           $('.amount').each(function (i,v) {
               var _this=$(this)
              amount+=parseInt(_this.text().substr(0,_this.text().length-1))
           })
            $('#amount').text('总计：'+amount+'￥')
        })
    </script>
@endsection
