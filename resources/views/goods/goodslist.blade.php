@extends('layouts.bootstrap')

@section('content')
    <h1><font>UID:{{$uid}} Welcome back!</font></h1>

        <input type="text" name="keys" id="keys" value={{$key}}>
        <button ><a href="" id="seet">搜索</a></button>

    <table border="1" class="table table-bordered">
        <tr>
            <td>商品id</td>
            <td>商品名称</td>
            <td>价格</td>
            <td>库存</td>
            <td>操作</td>
        </tr>
        @foreach($info as $v)
            <tr>
                <td>{{$v->goods_id}}</td>
                <td>{{$v->goods_name}}</td>
                <td>{{$v->goods_price}}</td>
                <td>{{$v->goods_stock}}</td>
                <td><a href="/cartadd/{{$v->goods_id}}">商品信息</a></td>
            </tr>
        @endforeach
    </table>
    {{$info->links()}}
    <button class="btn btn-danger" ><a href="/userquit" style="text-decoration: none;color: white;">Quit</a></button>
    <button class="btn btn-danger"><a href="/allorders" style="text-decoration: none; color: #ffffff;">我的全部订单</a></button>
@endsection
@section('footer')
    @parent
    <script>
        $(function () {
            $('#seet').click(function () {
                var _this=$(this)
                var _key=$('#keys').val()
                var _href=_this.attr('href',"/goodslist?key="+_key)
            })
            $('a').each(function () {
                var _key=$('#keys').val()
                if(_key!=''){
                    var _this=$(this)
                    if(_this.prop('rel')=='next'){
                        var _href=_this.attr('href')
                        var test=_href+"&&key="+_key
                        _this.attr('href',test)
                    }
                    if(_this.prop('rel')=='prev'){
                        var _href=_this.attr('href')
                        var test=_href+"&&key="+_key
                        _this.attr('href',test)
                    }
                }
            })
        })
    </script>
@endsection