<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GoodsModel extends Model
{
    public $table = 'shop_goods';
    public $timestamps = false;
    public $primaryKey = 'goods_id';
    //获取某字段时 格式化 该字段的值
//    public function getGoodsPriceAttribute($price)
//    {
//        return $price / 100;
//    }
}
