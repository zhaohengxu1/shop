<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\Model\UserModel;
use GuzzleHttp\Client;

class AlipayController extends Controller
{
    public $app_id ;
    public $gate_way;
    public $notify_url;
    public $return_url ;
    public $rsaPrivateKeyFilePath = './key/priv.key';
    public $aliPubKey = './key/ali_pub.key';

    public function __construct()
    {
        $this->app_id=env('ALIPAY_APPID');
        $this->gate_way=env('ALIPAY_GETWAY');
        $this->notify_url=env('ALIPAY_NOTIFY_URL');
        $this->return_url=env('AILIPAY_RETURN_URL');
    }

    public function test($order_num)
    {
        $orderWhere=[
        'order_num'=>$order_num
        ];
        $orderData=OrderModel::where($orderWhere)->first()->toArray();
//业务请求参数
        $bizcont = [
            'subject'           => 'ancsd'. mt_rand(1111,9999).str_random(6), //订单信息
            'out_trade_no'      =>$orderData['order_num'] , //订单号'oid'.date('YmdHis').mt_rand(1111,2222)
            'total_amount'      => $orderData['order_amount'],                 //金额
            'product_code'      => 'QUICK_WAP_WAY',  //销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
        ];
//$data 公共参数
        $data = [
            'app_id'   => $this->app_id,                     //支付宝分配给开发者的应用ID
            'method'   => 'alipay.trade.wap.pay',           //接口名称
            'format'   => 'JSON',
            'charset'   => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'), //发送请求时间
            'version'   => '1.0',                           //	调用的接口版本，固定为：1.0
            'return_url' => $this->return_url,
            'notify_url'   => $this->notify_url,            //支付宝服务器主动通知商户服务器里指定的页面http/https路径。建议商户使用https
            'biz_content'   => json_encode($bizcont),       //业务请求参数的集合
        ];

        $sign = $this->rsaSign($data);
        $data['sign'] = $sign;
        $param_str = '?';
        foreach($data as $k=>$v){
            $param_str .= $k.'='.urlencode($v) . '&';
        }
        $url = rtrim($param_str,'&');
        $url = $this->gate_way . $url;
        header("Location:".$url);
    }


    public function rsaSign($params) {
        return $this->sign($this->getSignContent($params));
    }

    protected function sign($data) {
        $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
        $res = openssl_get_privatekey($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);

        if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }


    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, 'UTF-8');
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = 'UTF-8';
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }

    /**支付宝同步通知回调*/
    public function sync(){
        $order_num=$_GET['out_trade_no'];
        $orderWhere=[
            'order_num'=>$order_num
        ];
        $orderData=OrderModel::where($orderWhere)->first();
        //验证订单号
        if(empty($orderData)){
            echo '订单号有误!';exit;
        }
        $order_amount=$_GET['total_amount'];
        //验证支付金额
        if($order_amount!=$orderData->order_amount){
            echo '定点金额有误！';exit;
        }
        //验签
        if(!$this->verify($_GET)){
            echo 'Error';exit;
        }
        echo 'ok';
}
    /**支付宝异步通知回调*/
   public function notify(){

       $data = json_encode($_POST);
       $log_str = '>>>> '.date('Y-m-d H:i:s') . $data . "<<<<\n\n";
       //记录日志
       file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);

       //验签
       $res = $this->verify($_POST);
       $log_str = '>>>> ' . date('Y-m-d H:i:s');
       if($res === false){
           //记录日志 验签失败
           $log_str .= " Sign Failed!<<<<< \n\n";
           file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);
       }else{
           $log_str .= " Sign OK!<<<<< \n\n";
           file_put_contents('logs/alipay.log',$log_str,FILE_APPEND);
       }
       //处理订单信息
       if(!$this->dealOrder($_POST)){
           echo "Error";exit;
       };
       echo 'success';
    }

    //验签
    function verify($params){
        $sign = $params['sign'];
        $params['sign_type'] = null;
        $params['sign'] = null;

        //读取公钥文件
        $pubKey = file_get_contents($this->aliPubKey);
        $pubKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        $result = (openssl_verify($this->getSignContent($params), base64_decode($sign), $res, OPENSSL_ALGO_SHA256)===1);
        return $result;
    }

    //处理订单逻辑
    function dealOrder($data){
       $order_num=$data['out_trade_no'];
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
        //根据订单号获查询订单表获取到用户id
        $uid=$orderData['user_id'];
        //赠送积分
        $userWhere=[
            'id'=>$uid
        ];
        $userData=UserModel::where($userWhere)->first()->toArray();
        $userData['integral']=$userData['integral']+$order_amount;
        $res2=UserModel::where($userWhere)->update($userData);
        if($res&&$res2){
            return true;
        }
    }
}
