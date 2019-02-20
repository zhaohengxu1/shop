@extends('layouts.bootstrap')

@section('content')
    <div class="container">
        <h1>{{$goodsInfo->goods_name}}</h1>
        <span> 价格： {{$goodsInfo->goods_price}}</span>
        <form class="form-inline" method="post" action="/cartadd">
            <input type="hidden" name="goods_id" value="{{$goodsInfo->goods_id}}">
            {{csrf_field()}}
            <div class="form-group">
                <label class="sr-only" for="goods_num">Amount (in dollars)</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="buy_number"  value="1">
                </div>
            </div>
            <input type="hidden" id="goods_id" value="{{$goodsInfo->goods_id}}">
            <button type="submit" class="btn btn-primary" id="add_cart_btn">加入购物车</button>
        </form>
    </div>


@endsection

@section('footer')
    @parent
@endsection