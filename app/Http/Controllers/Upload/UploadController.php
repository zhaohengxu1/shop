<?php

namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    //文件上传
    public function upload(){
        return view('upload.show');
    }
    public function pdfadd(Request $request){
        $pdf=$request->file('pdf');
        $exit=$pdf->extension();
        if($exit!='pdf'){
            echo '请上传pdf类型的文件';exit;
        }
        $res = $pdf->storeAs(date('Ymd'),str_random(5) . '.pdf');
        if($res){
            echo 'ok';
        }
    }
}
