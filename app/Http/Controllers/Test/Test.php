<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Test extends Controller
{
    //中间件测试
    public function test(){
        echo __METHOD__;
    }
}
