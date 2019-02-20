<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use App\Model\GoodsModel;
class GoodsController extends Controller
{
//    use HasResourceActions;
    //商品视图显示
    public function index(Content $content){
        return $content
            ->header('商品管理')
            ->description('商品列表')
            ->body($this->grid());
    }
    //商品列表展示
    protected function grid()
    {
        $grid = new Grid(new GoodsModel());

        $grid->model()->orderBy('goods_id','desc');     //倒序排序

        $grid->goods_id('商品ID');
        $grid->goods_name('商品名称');
        $grid->goods_stock('库存');
        $grid->goods_price('价格');
        $grid->create_time('添加时间')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        return $grid;
    }
    //添加商品视图
    protected function create(Content $content){
        return $content
            ->header('商品管理')
            ->description('商品添加')
            ->body($this->form());
    }
    //添加表单
    protected function form()
    {
        $form = new Form(new GoodsModel());
        $form->text('goods_name', '商品名称');
//        $form->ckeditor('content');
        $form->number('goods_stock', '库存');
        $form->currency('goods_price', '价格')->symbol('¥');
        return $form;
    }
    //得到添加商品数据
    protected function store()
    {
       $data=$_POST;
       unset($data['_token']);
       unset($data['_previous']);
       $data['create_time']=time();
       $data['goods_price']=$data['goods_price']*100;
       $res=GoodsModel::insert($data);
       if($res){
           header("Location:http://www.shop.com/admin/goods");
       }
    }
    //删除
    protected function destroy($id){
       $res=GoodsModel::where(['goods_id'=>$id])->delete();
        if($res){
            header("Location:http://www.shop.com/admin/goods");
        }
    }

    //修改
    protected function edit(Content $content,$id){
        return $content
            ->header('商品管理')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }
    //修改执行
    protected function update($id){
        $data=$_POST;
        $data['goods_price']=$data['goods_price'];
        unset($data['_token']);
        unset($data['_method']);
        unset($data['_previous_']);
        $res=GoodsModel::where(['goods_id'=>$id])->update($data);
        if($res){
            header("Location:http://www.shop.com/admin/goods");
        }
    }

}
