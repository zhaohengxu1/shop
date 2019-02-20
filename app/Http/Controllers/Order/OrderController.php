<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use App\Model\OrderDetailModel;
use App\Model\OrderModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //生成订单
    public function createOrder(){
        //根据用户id查询该用户下的购物车信息
        $uid=Auth::id();
        $where=[
          'user_id'=>$uid
        ];
        $cartDate=CartModel::where($where)->get()->toArray();
        //求总价钱
        $order_amount=0;//单位 分
        $order_num=OrderModel::generateOrderSN();
        foreach ($cartDate as $k=>$v){
            $goodsDate=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            //减少库存
            $goodsDate['goods_stock']=$goodsDate['goods_stock']-$v['buy_number'];
            $goodsDate['goods_price']=$goodsDate['goods_price'];
            $upWhere=[
              'goods_id'=>$v['goods_id']
            ];
            $res=GoodsModel::where($upWhere)->update($goodsDate);
            if($res){
                //补全订单详情
                $goodsDate['buy_number']=$v['buy_number'];
                $goodsDate['order_num']=$order_num;
                $goodsDate['user_id']=$uid;
                unset($goodsDate['goods_stock']);
                $list[] = $goodsDate;
            }
            //求总价钱
            $order_amount+=$goodsDate['goods_price']*$v['buy_number'];
        }
        //生成订单
        $data = [
            'order_num'      => $order_num,
            'user_id'           => $uid,
            'c_time'      => time(),
            'order_amount'  => $order_amount
        ];
        $res = OrderModel::insertGetId($data);
        //生成订单详情
        $res2=OrderDetailModel::insert($list);
        if($res&&$res2){
            //减少库存
            if($res){
                //清空购物车
                CartModel::where(['user_id'=>$uid])->delete();
                header("refresh:0;url=/orderdetail/$order_num");
            }else{
                exit('生成订单失败.');
            }
        }
    }
    //订单详情页
    public function orderDetail($order_num){
        $uid=Auth::id();
        $showWhere=[
            'user_id'=>$uid,
            'order_num'=>$order_num
        ];
        $order_detail=OrderDetailModel::where($showWhere)->get();
        $orderDate=OrderModel::where($showWhere)->first()->toArray();
        $order_status=$orderDate['order_status'];
        $data=[
            'order_num'=>$order_num,
            'info'=>$order_detail,
            'order_status'=>$order_status
        ];
        return view('order.orderdetail',$data);
    }
    //所有订单
    public function allOrders(){
        $uid=Auth::id();
        $where=[
          'user_id'=>$uid
        ];
        $orderDate=OrderModel::where($where)->orderBy('c_time','desc')->get();
        $data=[
            'uid'=>$uid,
          'data'=>$orderDate
        ];
        return view('order.allorders',$data);
    }
    //订单支付
    public function orderPay($order_num){
        //支付宝支付
       //获取订单数据
        $orderWhere=[
          'order_num'=>$order_num
        ];
        $orderData=OrderModel::where($orderWhere)->first()->toArray();
        if(empty($orderData)){
            die("订单 ".$order_num. "不存在！");
        }
        $order_status=$orderData['order_status'];
        if($order_status!=1){
            die("此订单已被支付或订单异常。");
        }


        $order_amount=$orderData['order_amount'];
       //更改订单状态
        $where=[
          'order_num'=>$order_num
        ];
        $data=[
          'order_status'=>2
        ];
        $res=OrderModel::where($where)->update($data);
        $uid=Auth::id();
        //赠送积分
        $userWhere=[
            'uid'=>$uid
        ];
        $userData=UserModel::where($userWhere)->first()->toArray();
        $userData['integral']=$userData['integral']+$order_amount;
        UserModel::where($userWhere)->update($userData);
        var_dump($res);
    }
    //取消订单
    public function orderDel($order_num,$order_status){
        if($order_status==2){
            //调用支付宝退款接口
        }
        $uid=Auth::id();
        //获取订单数据
        $orderWhere=[
            'order_num'=>$order_num
        ];
        $orderData=OrderModel::where($orderWhere)->first()->toArray();
        $order_amount=$orderData['order_amount'];
        //更改订单表
        $where=[
          'order_num'=>$order_num
        ];
        $data=[
          'order_status'=>3
        ];
        $res=OrderModel::where($where)->update($data);
        if($res!==false){
            //归还库存
            $order_detail=OrderDetailModel::where($where)->get()->toArray();
            foreach($order_detail as $k=>$v){
                $goodData=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $goodData['goods_stock']=$v['buy_number']+$goodData['goods_stock'];
                $res2=GoodsModel::where(['goods_id'=>$v['goods_id']])->update($goodData);
            }
            //减积分
            //赠送积分
            $userWhere=[
                'id'=>$uid
            ];
            $userData=UserModel::where($userWhere)->first()->toArray();
            $userData['integral']=$userData['integral']-$order_amount;
            UserModel::where($userWhere)->update($userData);
            if($res2){
                if($order_status==1){
                    echo '订单取消成功';
                }else if($order_status==2){
                    echo '退款成功！';
                }
                header("refresh:1;url='/allorders'");
            }
        }
    }
    //订单服务化
    public function orderTest(){
        $url='http://shop.order.com';
        $client=new Client(['base_uri'=>$url,'timeout'=>2.0,]);
        $response=$client->request('GET','/order.php');
        echo $response->getBody();
    }
}
