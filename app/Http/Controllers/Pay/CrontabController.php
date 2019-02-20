<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Model\OrderModel;
use App\Model\OrderDetailModel;
use App\Http\Controllers\Controller;

class CrontabController extends Controller
{
    /**
     * 删除订单
     */
    public function deleteOrder(Request $request){

        $orderInfo=OrderModel::all();
        if(empty($orderInfo)){
            echo ('还没有下单.');exit;
        }
        $orderInfo=$orderInfo->toArray();
        $res=false;
        foreach ($orderInfo as $k=>$v){
            if($v['order_status']==1){
                if(time()-$v['c_time'] > 300){
                    $Orderwhere=['order_num'=>$v['order_num']];
                    $data=[
                        'order_status'=>3
                    ];
                    $res=OrderModel::where($Orderwhere)->update($data);
                }
            }
        }
        if($res!==false){
            echo "Success"."\n";
        }else{
            echo 'Error';exit;
        }

    }





}
