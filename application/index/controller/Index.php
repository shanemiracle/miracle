<?php
namespace app\index\controller;

use think\controller\Rest;
use think\Request;

class Index extends Rest
{

    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }

    public function hello()
    {
        $req = Request::instance();
        switch ($this->_method){
            case 'get': // get请求处理代码
                if ($this->_type == 'html'){
                    echo $req->param('a');
                    echo $req->param('b');
                } elseif ($this->_type == 'xml'){
                    echo 'xml';
                }
                break;
            case 'put': // put请求处理代码
                echo 'put';
                break;
            case 'post': // put请求处理代码
                echo 'post';
                break;
        }

        $array = ['1'=>'xiaoj','2'=>'qium','3'=>'yaoj'];

//        return $array;
       return $this->response($array,"json",200);
    }

    public function up() {
        return '< action="index/User/logoUpload" enctype="multipart/form-data" method="post"> <input type="file" name="image"/> <br> <input type="submit" value="上传"/> </form> ';
    }
}
