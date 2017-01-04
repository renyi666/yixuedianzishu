<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use Home\Model\UserModel;
use OT\DataDictionary;
use Common\Controller\UController;
use Common\Service\User\MemberService;
use Common\Service\User\UserService;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class CeshiController extends HomeController {
    private $_appId;
    private $_appSecret;
    private $_webRoot;
    private $_openUrlQrc;
    private $_openUrlToken;
    private $_openUrlUserInfo;
    public function __construct()
    {
        parent::__construct();
        //修改成自己的
        $this->_appId = 'wx9852a25a76cf2c85';//开放平台网站应用
        $this->_appSecret = 'd81f928ef45e7e5cf806797f1c5b40f1';
        $this->_webRoot = 'http://www.yixuedianzishu.com/index.php?s=/Home/Ceshi/wxCheck';//返回的域名网址，必须跟网站应用的域名网址相同

        $this->_openUrlQrc = 'https://open.weixin.qq.com/connect/qrconnect';//申请二维码接口
        $this->_openUrlToken = 'https://api.weixin.qq.com/sns/oauth2/access_token';//申请token接口
        $this->_openUrlUserInfo = 'https://api.weixin.qq.com/sns/userinfo';//申请用户信息接口
    }



    /**
     * 验证微信扫码后的用户数据
     */
    public function wxCheck(){

        $code = I('code');//只能使用1次即销毁
        $jumpurl = I('state');
        session('jumpurl1',$jumpurl);
        if($code==''||$code==null){

            $this->error("您还未授权,请重新登陆");
        }

        //获取access_token和openid信息，还有用户唯一标识unionid
        $ken = $this->wxToken($code);//:access_token,expires_in,refresh_token,openid,scope,unionid


        if($ken['errcode'] == 40029){
            $this->error("code参数已经过期,请重新登陆");
        }

//https://open.weixin.qq.com/connect/qrconnect?appid=wx9852a25a76cf2c85&redirect_uri=http%3A%2F%2Fwww.yixuedianzishu.com%2Findex.php%3Fs%3D%2FHome%2FIndex%2FwxCheck&response_type=code&scope=snsapi_login&state=http://www.yixuedianzishu.com/
;
        //判断是否已存在
        $map = array(
            'unionid' => $ken['unionid']
    );


        $a=$ken['unionid'];


dump($ken); $User=new UserModel();

        $res=$User->where($map)->select();
dump($res);
        $userinfo = Array(

            'uid' => $res['0']['id'],
            'username' => $res['0']['username'],
            'nickname' => $res['0']['nickname'],
            'avatar' => $res['0']['avatar'],
            'cellphone' => $res['0']['cellphone'],
            'city' => $res['0']['city'],
            'openid' => $res['0']['openid'],
            'unionid' => $res['0']['unionid'],
        );
        session('userInfo', $userinfo);
        $result =   session('userInfo');
        dump($result);
        $user1 = $this->wxUserInfo($ken['access_token'],$ken['openid']);
        dump($user1);
        dump("aa");
        if($a==Null){
            $this->error("参数错误请重新登录");

        }else {


        }




    }

    /**
     * 提交微信登录请求
     */
    public function wxLogin(){

        $jumpurl=$_SERVER["HTTP_REFERER"];




        $stats = $this->getRandChar(16);//该参数可用于防止csrf攻击（跨站请求伪造攻击）
        session('wx_state',$stats);//把随机字符串写入session，验证时对比
        $url = $this->_openUrlQrc.'?appid='.$this->_appId.'&redirect_uri='.urlencode($this->_webRoot).'&response_type=code&scope=snsapi_login&state='.$jumpurl;
        redirect($url);//跳转到扫码
    }

    //生成随机数,length长度
    function getRandChar($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    //CURL获取url返回值
    function httpGet($url){
        $oCurl = curl_init();//实例化
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );//是否返回值，1时给字符串，0输出到屏幕
        $sContent = curl_exec($oCurl);//获得页面数据
        $aStatus = curl_getinfo($oCurl);//获取CURL连接数据的信息
        curl_close($oCurl);//关闭资源
        //获取成功
        $output_array = json_decode($sContent,true);//转换成数组格式
        if(intval($aStatus["http_code"])==200){
            return $output_array;
        }else{
            return false;
        }
    }

    //获取token信息
    public function wxToken($code){
        $url = $this->_openUrlToken.'?appid='.$this->_appId.'&secret='.$this->_appSecret.'&code='.$code.'&grant_type=authorization_code';
        $sContent = $this->httpGet($url);//获取token数据

        return $sContent;
    }
    //延长token时间,默认token两个小时
    public function wxrefresh($refresh){

        $url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$this->_appId.'&grant_type=refresh_token&refresh_token='.$refresh;
        return $this->httpGet($url);
    }

    //检验token授权是否有效
    public function wxchecktoken($token,$openid){
//        $token = 'OezXcEiiBSKSxW0eoylIeB1YmSEMVVx-QnR-TQB0UGhwGvDVZSksJNuoEXUlc2BYPQ7omt_iutprcPBjR4UjI3oEA5Z0k-N7yOOLn3h8AIF0-UhysqN3WjZAr-rHGVkWmPVh2FS9s-bUcL7Il1q7IQ';
//        $openid = 'o5ZGIxPWrZBa61964QTRGkSWyUgo';
        $url = 'https://api.weixin.qq.com/sns/auth?access_token='.$token.'&openid='.$openid;
        return $this->httpGet($url);
    }
    //获取微信用户个人信息，但公众号和开放平台opendid 会不一样，unionid是用户唯一标识
    public function wxUserInfo($token,$openid){
//        $token = 'OezXcEiiBSKSxW0eoylIeB1YmSEMVVx-QnR-TQB0UGhwGvDVZSksJNuoEXUlc2BYPQ7omt_iutprcPBjR4UjI3oEA5Z0k-N7yOOLn3h8AIF0-UhysqN3WjZAr-rHGVkWmPVh2FS9s-bUcL7Il1q7IQ';
//        $openid = 'o5ZGIxPWrZBa61964QTRGkSWyUgo';
        $url = $this->_openUrlUserInfo.'?access_token='.$token.'&lang=zh-CN&openid='.$openid;

        return $this->httpGet($url);
    }
















    /**
     * @return config
     */
    //获取session中的用户细信息
    public function get_session()
    {
        $data[]=session('userInfo');
        $user_id=$data['0']['uid'];


        $this->assign('data',$data);
       // dump($data);

    }

    //退出
    public  function out(){
        $return_url=$_SERVER["HTTP_REFERER"];
        $str = $return_url;
        //使用正则表达式截取U方法需要的路径
        $isMatched = preg_match('/http:\/\/www\.yixuedianzishu\.com\/index\.php\?s=\/([^\.]+)./', $str, $matches);

        $return_url=$matches['1'];

        session('userInfo',null);
        $this->redirect($return_url);



    }
    //搜索页面






    public function login()
    {



        $this->display();

    }










}