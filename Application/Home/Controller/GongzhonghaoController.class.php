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
use Com\Wechat;
use Com\WechatAuth;
use Common\Controller\UController;
use Common\Service\User\MemberService;
use Common\Service\User\UserService;
/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class GongzhonghaoController extends HomeController {
    private     $appid      = 'wx998e66817ed2b1ac'; //AppID(应用ID)
    private     $token      = 'weixin'; //微信后台填写的TOKEN
    private     $crypt      = 'M6nck8Bj7jyb3B11GXZjS3btzkALfaxBho9A5cpm19R'; //消息加密KEY（EncodingAESKey）
    private     $appsecret  = '3ed8553cc9b8d8b4a474cbadeef45ad6';
    public function index(){

        $code = $_GET['code'];

        $this->get_wechatInfo($code);


        //$this->redirect('Fenxiang/index');

    }
    public  function  sign(){

        $code = $_GET['code'];

        $where['status']=$status=$_GET['state'];



        $wechatAuth = new WechatAuth($this->appid,$this->appsecret,$this->token);
        $result=  $wechatAuth->getAccessToken('code',$code);
        $openid=$result['openid'];
        $result1=$wechatAuth->getUserInfo($openid);

        $where1['unionid']=$result1['unionid'];
        $userM  =   M('User');
        $user_result    =   $userM->where($where1)->find();

        $score_recordM=M('ScoreRecord');
        $result1=$score_recordM   ->where($where1)->order('time desc')->select();
       
        if($result1==null||$result1==''){

            $scoreinfo=Array(


                'status' =>$status,
                'cause'  =>'微信签到',
                'score'  =>'+1',
                'unionid'=>$result['unionid'],
            );

            $user_result['score']   =   $user_result['score']+1;
            $userM->save($user_result);
            $score_recordM->add($scoreinfo);
            $data=1;

        }else{

            $compare['time']    =  strtotime($result1['0']['time']);
            $compare1['time']   =   time();
            $compare2['time']    =   $compare['time']+60*24*60;

            $today  =strtotime(date("Y-m-d"),time());
            $today_final    =   $today+60*60*24;
            if($compare['time']<$today){

                $scoreinfo=Array(


                    'status' =>$status,
                    'cause'  =>'微信签到',
                    'score'  =>'+1',
                    'unionid'=>$result['unionid'],
                );
                $user_result['score']   =   $user_result['score']+1;
                $userM->save($user_result);
                $score_recordM->add($scoreinfo);
                $data=1;

            }else{

                $data=2;
            }

        }
        $this->assign('data',$data);
$this->display();

    }

    public function get_wechatInfo($code){


            $code_result    =   session('code');
        if($code_result==$code){
            $a_result   =   session('a');
            if($a_result==1){

                $this->redirect('Fenxiang/index');

            }
            else{
                $res2=session('out_date');
                $this->redirect('Gongzhonghao/fail',array('time'=>$res2));

            }

        }

        
        session('code',$code);


         $where1['number']=$status=$_GET['state'];
            if(empty($_COOKIE['openid'])) {


                $wechatAuth = new WechatAuth($this->appid,$this->appsecret,$this->token);
                $result=  $wechatAuth->getAccessToken('code',$code);
                $openid=$result['openid'];
            if($openid==Null){



            }else{


                $result1=$wechatAuth->getUserInfo($openid);

                $User=new UserModel();
                $where['unionid']=$result1['unionid'];
                if($where['unionid']!=null){
                    $lianjie=M('Lianjie');
                    $score_record=M('ScoreRecord');
                    $yuedu=M('Yuedu');
                    //从积分记录表里查询是否有此次的status参数
                    $score=$score_record->field('status')->where($where)->select();
                    $ids = array_column($score, 'status');

                    $result2=$lianjie->field('create_time')->where($where1)->find();

                    $ids=array_unique($ids);

                    $res=$User->where($where)->select();


                    //转换成一维数组

                    if($result2==Null){


                    }else {
                        $where2=$result2['create_time'];
                        $res2=$where2+60*60*24;
                        session('out_date',$res2);
                        $res3=$res2-time();
                        if($res3>0){

                            session('a',1);
                            if ($res) {
                                if (in_array($status, $ids)) {


                                    $this->redirect('Fenxiang/index');
                                } else {


                                    $res1['score']=$res['0']['score'] + 1;
                                    $User->where($where)->save($res1);

                                    $scoreinfo=Array(


                                        'status' =>$status,
                                        'cause'  =>'微信阅读原文',
                                        'score'  =>'+1',
                                        'unionid'=>$result['unionid'],
                                    );

                                    $scoreinfo1=Array(


                                        'status'=>$status,

                                        'unionid'=>$result['unionid'],
                                        'create_time'=>time(),
                                    );
                                    $yuedu->add($scoreinfo1);

                                    $score_record->add($scoreinfo);
                                    $this->redirect('Gongzhonghao/success');

                                }


                            } else {

                                $result1=$wechatAuth->getAccessToken();
                                $user_1=$wechatAuth->userInfo($result['openid']);

                                $userinfo=Array(


                                    'nickname'=>$user_1['nickname'],
                                    'avatar'  =>$user_1['headimgurl'],
                                    'openid'  =>$user_1['openid'],
                                    'unionid' =>$user_1['unionid'],
                                    'updated' =>time(),
                                    'created' =>time(),
                                );
                                $scoreinfo=Array(


                                    'status' =>$status,
                                    'cause'  =>'通过微信新用户注册+阅读原文',
                                    'score'  =>'+3',
                                    'unionid'=>$result['unionid'],
                                );
                                $User->add($userinfo);
                                $score_record->add($scoreinfo);
                                $this->redirect('Gongzhonghao/success');
                            }
                        }else{
                            session('a',2);
                            $this->redirect('Gongzhonghao/fail',array('time'=>$res2));
                        }

                    }
                }


            }


        }

    }
//            public function  score($username=''){
//
//                $score_record=M('ScoreRecord');
//                $result=session('gongzhong_res');
//
//                if($username=='a'){
//
//                    $scoreinfo=Array(
//
//
//                        'status'=>'-3',
//                        'cause'  =>'微信朋友分享',
//                        'score'  =>'+5',
//                        'unionid' =>$result['unionid'],
//                    );
//
//                }else{
//
//                    $scoreinfo=Array(
//
//
//                        'status'=>'-4',
//                        'cause'  =>'微信朋友圈分享',
//                        'score'  =>'+5',
//                        'unionid' =>$result['unionid'],
//                    );
//                }
//
//
//
//
//                $score_record->add($scoreinfo);
//
//            $data="ok";
//
//            $this->ajaxReturn($data);
//
//
//
//        }


    public  function  fail(){
        $time=I('time');

        $time=date("Y-m-d H:i:s",$time);
        $this->assign('time',$time);

    $this->display();
}
public  function success(){
    $time=time();
    $time=date("Y-m-d",$time);
    $this->assign('time',$time);
    $this->display();


}
    public function fenxiang(){



            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
            $res = $this->api_request($url);

            if(isset($res->access_token)){
                return array(
                    'errcode'       =>0,
                    'errmsg'        =>'success',
                    'access_token'  =>$res->access_token,
                    'expires_in'    =>$res->expires_in
                );
            }else{
                return array(
                    'errcode'       =>$res->errcode,
                    'errmsg'        =>$res->errmsg,
                    'access_token'  =>null,
                    'expires_in'    =>null
                );

        }




     //   $this->display();
    }

    public function api_request($url,$data=null){
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数（基本参数）
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        );
        curl_setopt_array($ch, $opts);
        //post请求参数
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //执行cURL操作
        $output = curl_exec($ch);
        if (curl_errno($ch)) {    //cURL操作发生错误处理。
            var_dump(curl_error($ch));
            die;
        }
        //关闭cURL
        curl_close($ch);
        $res = json_decode($output);

        return ($res);   //返回json数据
    }

}