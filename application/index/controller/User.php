<?php
namespace app\index\controller;
/**
 * Created by PhpStorm.
 * User: xiaojie
 * Date: 16/6/16
 * Time: 下午6:48
 */
class User extends \think\controller\Rest
{
    public function get()
    {
        $data = ['name'=>'xiaoj','age'=>'27','sex'=>'male'];
        return $this->response($data,'json',200);
    }
}