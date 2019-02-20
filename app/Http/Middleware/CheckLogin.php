<?php
//
//namespace App\Http\Middleware;
//
//use Closure;
//use Illuminate\Support\Facades\Auth;
//
//class CheckLogin
//{
//    /**
//     * 验证登录
//     * Handle an incoming request.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @param  \Closure  $next
//     * @return mixed
//     */
//    public function handle($request, Closure $next)
//    {
//        $checkLogin=Auth::check();
//        if(!$checkLogin){
//            echo '请先登录';
//            header("Location:http://www.shop.com/login");exit;
//        }
////        if($checkLogin==false){
////            header("refresh:2;url=/userlogin");
////            exit('Please login ... ...');
////        }else{
////            if($_COOKIE['token']!=$request->session()->get('u_token')){
////                header("refresh:2;url=/userlogin");
////                exit('Please login ... ...');
////            }
////        }
//        return $next($request);
//    }
//}
