<?php

namespace App\Http\Controllers\Cart;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 购物车添加视图
     * @param $goods_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cartAdd($goods_id)
    {
        $where=[
          'goods_id'=>$goods_id
        ];
        $goodsInfo=GoodsModel::where($where)->first();
        $data=[
          'goodsInfo'=>$goodsInfo
        ];
        return view('cart.cartadd',$data);

    }

    /**
     * 购物车添加
     * @param Request $request
     */
    public function cartAddDo(Request $request){
        $goods_id=$request->input('goods_id');
        $buy_number=$request->input('buy_number');
        $uid=Auth::id();
        $session_token=$request->session()->get('u_token');
        $where=[
          'goods_id'=>$goods_id
        ];
        $goodsData=GoodsModel::where($where)->first()->toArray();
        if($goodsData['goods_stock']<$buy_number){
            exit('库存不足');
        }
        $cartWhere=[
          'goods_id'=>$goods_id,
          'user_id'=>$uid
        ];
        $cartData=CartModel::where($cartWhere)->first();
        if(!empty($cartData)){
            //该商品已存在该用户的购物车中--做累加
            $cart_id=$cartData['cart_id'];
            $upWhere=[
              'cart_id'=>$cart_id
            ];
            $data=[
                'goods_id'=>$goods_id,
                'buy_number'=>$cartData['buy_number']+$buy_number,
                'c_time'=>time(),
                'user_id'=>$uid,
                'session_token'=>$session_token
            ];
            $res=CartModel::where($upWhere)->update($data);
        }else{
            //添加购物车
            $data=[
                'goods_id'=>$goods_id,
                'buy_number'=>$buy_number,
                'c_time'=>time(),
                'user_id'=>$uid,
                'session_token'=>$session_token
            ];
            $res=CartModel::insertGetId($data);
        }
        //添加成功
            if($res){
                header("refresh:1;url=/cartlist");
                echo '添加成功!跳转中......';exit;
            }
    }

    /**
     * 购物车列表展示
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cartList(Request $request)
    {
        $uid=Auth::id();
        $where=[
          'user_id'=>$uid
        ];
        $cart_goods=CartModel::where($where)->get();
        $cart_goods=[
          'info'=>$cart_goods,
            'uid'=>$uid
        ];
        return view('cart.cartlist',$cart_goods);
    }

    /**
     * 删除购物车
     * @param $goods_id
     */
    public function delCartInfo($goods_id){
        $uid=Auth::id();
        $where=[
          'user_id'=>$uid,
          'goods_id'=>$goods_id
        ];
        $cartGoodsData=CartModel::where($where)->first()->toArray();
        $res=CartModel::where($where)->delete();
        if($res){
            echo ('删除成功');
            header("refresh:1;url=/cartlist");
        }
    }
}
