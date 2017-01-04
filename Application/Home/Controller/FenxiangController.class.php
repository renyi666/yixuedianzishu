<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Com\WechatAuth;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class FenxiangController extends HomeController {


    private $appid;
    private $appsecret;
    public function __construct()
    {
        parent::__construct();
        //修改成自己的
        $this->appid = 'wx998e66817ed2b1ac';//开放平台网站应用
        $this->appsecret = '3ed8553cc9b8d8b4a474cbadeef45ad6';
        $this->_webRoot = 'http://www.yixuedianzishu.com/index.php?s=/Home/Index/wxCheck';//返回的域名网址，必须跟网站应用的域名网址相同


    }


    public  function index(){
        $wechatAuth                 =   new WechatAuth($this->appid,$this->appsecret,$this->token);
        $wechatAuth->getAccessToken('client');
        $wechatAuth->getTicket();
        $sign                       =   $wechatAuth->getSignArray();
        $this->assign('sign_array',$sign);
        if(IS_AJAX){
            $this->ajaxReturn(array('sign_array'=>$sign));
        }
        $this->display();

    }
//        public function  score($a=''){
//
//            $data="ok";
//            $this->ajaxReturn($data);
//
//
//
//        }

}
