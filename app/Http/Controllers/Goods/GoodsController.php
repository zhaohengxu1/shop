<?php

namespace App\Http\Controllers\Goods;

use App\Model\GoodsModel;
use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class GoodsController extends Controller
{
    //商品列表展示
    public function goodsList(){

        if(!empty($_GET['key'])){
            $key=$_GET['key'];
        }else{
            $key='';
        }

//        $redis_Key='h_goods_info'.rand(0,99);
//        echo $redis_Key;echo '</br>';
//        //从缓存中取
//        $info=Redis::hGetAll($redis_Key);
//        if(!empty($info)){
//            echo 'Redis';
//        }else{
//            echo 'Mysql';
            $info=DB::table('shop_goods')->where('goods_name','like',"%$key%")->paginate(2);
//            $res=Redis::hmset($redis_Key,$info);
//        }

        $uid=session()->get('uid');
        $data=[
            'info'=>$info,
            'uid'=>$uid,
            'key'=>$key
        ];

        return view('goods.goodslist',$data);
    }
    //商品列表展示搜索分页
}
