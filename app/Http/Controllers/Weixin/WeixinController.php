<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp;

class WeixinController extends Controller
{
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");//获取流的形式获取值(数据类型是一个xml字符串)
        //处理xml字符串

        $xml_str=simplexml_load_string($data);  //得到一个处理后的对象类型
        //获取事件类型
        $event= $xml_str->Event;    //subscribe关注   unsubscribe取消关注 click公众号点击事件

        //处理微信接受用户消息，自动回复
        if(isset($xml_str->MsgType)){
            //获取openid
            $openid=$xml_str->FromUserName;
            //获取用户微信信息
            $toUserName=$xml_str->ToUserName;
            //用户发送文字
            if($xml_str->MsgType=='text'){
                $msg=$xml_str->Content;
                $xmlStrResopnse='<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$toUserName.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA['.$msg.']]></Content>
                </xml>';
                echo $xmlStrResopnse;
            }
            //用户发送图片
            if($xml_str->MsgType=='image'){
                $media_id=$xml_str->MediaId;
                $file_name = $this->dlWxImg($xml_str->MediaId);
                $res=$this->saveImage($media_id);
                if($res){
                    $hint='我们已经收到你的图片啦！';   //hint  提示
                }else{
                    $hint='很遗憾，您的图片我们没收到.....请稍后重试！';
                }
                $xmlStrResopnse='<xml>
                <ToUserName><![CDATA['.$openid.']]></ToUserName>
                <FromUserName><![CDATA['.$toUserName.']]></FromUserName>
                <CreateTime>'.time().'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA['.$hint.']]></Content>
                </xml>';
                echo $xmlStrResopnse;

                //写入数据库
                $data = [
                    'openid'    => $openid,
                    'add_time'  => time(),
                    'msg_type'  => 'image',
                    'media_id'  => $xml_str->MediaId,
                    'format'    => $xml_str->Format,
                    'msg_id'    => $xml_str->MsgId,
                    'file_name'   => $file_name
                ];

                $m_id = WeixinMedia::insertGetId($data);
                var_dump($m_id);
            }
        }

        //判断事件类型----关注和取消关注
        if($event=='subscribe'){
            //获取openid
            $openid=$xml_str->FromUserName;
            //获取扫描时间
            $sub_time=$xml_str->CreateTime;
            //根据openid获取用户信息
            $userInfo=$this->getUserInfo($openid);
//            var_dump($userInfo);die;
            //保存用户信息
            $userData=WeixinUser::where(['openid'=>$openid])->first();
            if($userData){
                $upData=[
                    'status'=>1
                ];
                $res=WeixinUser::where(['openid'=>$openid])->update($upData);
                $str='老用户重新关注'.$res;
                var_dump($str);
            }else{
                $user_data = [
                    'openid'            => $userInfo['openid'],
                    'add_time'          => time(),
                    'nickname'          => $userInfo['nickname'],
                    'sex'               => $userInfo['sex'],
                    'headimgurl'        => $userInfo['headimgurl'],
                    'subscribe_time'    => $sub_time
                ];
                $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                $str='新用户关注'.$id;
                var_dump($str);
            }
        }else if($event=='unsubscribe'){
            //用户取消关注    进行修改
            $openid=$xml_str->FromUserName;
            $where=[
              'openid'=>$openid
            ];
            $upData=[
              'status'=>2
            ];
            $res=WeixinUser::where($where)->update($upData);
            $str='用户取消关注'.$res;
            var_dump($str);
        }else if($event=='CLICK'){
            //判断事件类型----公众号事件(点击自动回复)
            if($xml_str->EventKey=='get_content'){
                $openid=$xml_str->FromUserName;
                $toUserName=$xml_str->ToUserName;
                $this->getContent($openid,$toUserName);
            }
        }

        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 首次接入
     */
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }

    /**
     * 自动回复
     */
//    public function getContent($openid,$toUserName){
//        $time=time();
//        $date=date("Y/m/d H:i:s");
//        $content='你好，我是Tactshan！温馨提示您当前时间为'.$date;
//        $xmlStrResopnse='<xml>
//                <ToUserName><![CDATA['.$openid.']]></ToUserName>
//                <FromUserName><![CDATA['.$toUserName.']]></FromUserName>
//                <CreateTime>'.time().'</CreateTime>
//                <MsgType><![CDATA[text]]></MsgType>
//                <Content><![CDATA['.$content.']]></Content>
//                </xml>';
//        echo $xmlStrResopnse;
//    }

    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {
        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;
    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
//        echo $openid;exit;
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        return $data;
//        echo '<pre>';print_r($data);echo '</pre>';die;
    }

    /**
     * 自定义菜单创建
     */
    public function createMenu(){
        //获取access_token
        $access_token=$this->getWXAccessToken();
        //拼接url
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);

        //拼接菜单数据
        $data=[
          "button" => [
              [
                  "name"=>"个人中心",
                  "sub_button"=>[
                      [
                          "type"=>"location_select",
                          "name"=>"发送位置",
                          "key"=> "rselfmenu_2_0"
                      ]
                  ]
              ],
              [
                 'name'=>"扫码",
                  'sub_button'=>[
                      [
                          "type"=>"scancode_waitmsg",
                          "name"=>"扫码",
                          "key"=>"rselfmenu_0_0",
                          "sub_button"=> [ ]
                      ],
                      [
                          'type'=>'pic_sysphoto',
                          'name'=>'拍照发图',
                          'key'=>"rselfmenu_1_0",
                          "sub_button"=> [ ]
                      ],
                      [
                          'type'=>'pic_photo_or_album',
                          'name'=>'拍照或者相册发图',
                          'key'=>"rselfmenu_1_1",
                          "sub_button"=> [ ]
                      ],
                      [
                          'type'=>'pic_weixin',
                          'name'=>'微信相册发图',
                          'key'=>"rselfmenu_1_2",
                          "sub_button"=> [ ]
                      ]
                  ]
              ],

              [
                  "type"=>"click",
                  "name"=>"获取自动回复",
                  "key"=>"get_content"
              ]
          ]
        ];
        $res=$client->request('POST', $url, ['body' => json_encode($data,JSON_UNESCAPED_UNICODE)]);
        $res_arr=json_decode($res->getBody(),true);
        if($res_arr['errcode']==0){
            echo '菜单创建成功';
        }else{
            echo '菜单创建失败！错误码'.$res_arr['errmsg'];
        }
    }

    /**
     * 保存用户发送的图片
     * @param $mediaId
     * @return bool
     */
    public function saveImage($mediaId){
        $client=new GuzzleHttp\Client();
        //获取access_token
        $access_token=$this->getWXAccessToken();
        //拼接下载图片的url  https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$access_token.'&media_id='.$mediaId;

        //使用GuzzleHttp下载文件
        $response=$client->get($url);

        //获取文件名称
        $file_info = $response->getHeader('Content-disposition');
        // string(91) "attachment; filename="naj5JLd6yeW1dLiIxlaNCv5AceOAyuCYt1EVcBWr8ky5FO48dIAarm_pDvbNDy25.jpg""
        $file_name=substr(rtrim($file_info[0],'"'),-20);
        //dIAarm_pDvbNDy25.jpg
        $WxImageSavePath='wx/images/'.$file_name;
        //保存路径/home/wwwroot/shop/storage/app/wx/images
        //保存图片
        $res = Storage::disk('local')->put($WxImageSavePath,$response->getBody());
        if($res){     //保存成功
            return true;
        }else{      //保存失败
            return false;
        }
    }


    /**
     * 刷新access_token
     */
    public function refreshToken()
    {
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
    }

    /**
     * 自动回复
     */
    public function getContent($openid,$toUserName){
        $time=time();
        $date=date("Y/m/d H:i:s");
        $content='你好，我是狸狸狸！温馨提示您当前时间为'.$date;
        $xmlStrResopnse='<xml>
            <ToUserName><![CDATA['.$openid.']]></ToUserName>
            <FromUserName><![CDATA['.$toUserName.']]></FromUserName>
            <CreateTime>'.time().'</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA['.$content.']]></Content>
            </xml>';
        echo $xmlStrResopnse;
    }





}
