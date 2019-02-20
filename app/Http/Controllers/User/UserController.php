<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{
//    /**
//     * 用户列表展示
//     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
//     */
//    public function usershow(Request $request)
//    {
//        $this->middleware('auth');
//
//        $info=UserModel::all();
//        $uid=session()->get('uid');
//	        $data=[
//	          'info'=>$info,
//                'uid'=>$uid
//            ];
//	        return view('user.userlist',$data);
//    }
//
//    /**
//     * 用户注册
//     * 2019年1月3日14:26:56
//     * liwei
//     */
//    public function reg()
//    {
//        return view('user.reg');
//    }
//    public function doReg(Request $request)
//    {
//        $u_name=$request->input('u_name');
//        if(empty($u_name)){
//            exit('User name Can\'t be empty!');
//        }else{
//            //唯一性验证
//            $userInfo=UserModel::where(['name'=>$u_name])->first();
//            if(!empty($userInfo)){
//                header("refresh:3;url=/userreg");
//                exit('This user name has already been registered.');
//            }
//        }
//        $u_age=$request->input('u_age');
//        if(empty($u_age)){
//            exit('Please fill in your age!');
//        }
//        $pwd=$request->input('u_pwd');
//        $qpwd=$request->input('u_qpwd');
//        if($pwd!==$qpwd){
//            exit('Password and confirm password must be consistent!');
//        }else{
//            $pwd=password_hash($pwd,PASSWORD_BCRYPT);
//        }
//        $data = [
//            'name'  => $request->input('u_name'),
//            'age'  => $request->input('u_age'),
//            'pwd'  => $pwd,
//            'email'  => $request->input('u_email'),
//            'reg_time'  => time(),
//        ];
//        $uid = UserModel::insertGetId($data);
//        if($uid){
//            setcookie('uid',$uid,time()+86400,'','',false,true);
//            echo 'Registered successfully';
//            header("refresh:2;url=/userlogin");
//        }else{
//            echo 'Registered fail';
//        }
//    }
//
//    /**
//     * 用户登录
//     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
//     */
//    public function loginview(){
//        return view('user.login');
//    }
//    public function userlogin(Request $request){
//        $u_email=$request->input('u_email');
//        if(empty($u_email)){
//            exit('Email can\'t be empty');
//        }
//        $pwd=$request->input('u_pwd');
//        if(empty($pwd)){
//            exit('Password can\' be empty');
//        }
//       $where=[
//         'email'=>$u_email,
//       ];
//       $data=UserModel::where($where)->first();
//        $token = substr(md5(time().mt_rand(1,99999)),10,10);
//       if(password_verify($pwd,$data->pwd)){
//           $request->session()->put('uid',$data->uid);
//           setcookie('token',$token,time()+86400,'','',false,true);
//           $request->session()->put('u_token',$token);
//           echo 'Login successfully';
//           header("refresh:2;url=/goodslist");
//       }else{
//           header("refresh:2;url=/userlogin");
//           echo 'Email or Password is error';exit;
//       }
//    }

    /**
     * 退出
     *
     */
    public function quit(){
        header("refresh:0;url=/home");
    }
}
