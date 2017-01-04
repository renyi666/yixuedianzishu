<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015-12-27
 * Time: 10:42
 */

namespace Home\Controller;


use Common\Model\MemberModel;
use Common\Model\SyncLoginModel;

use Com\Wechat;
use Com\WechatAuth;
use Common\Model\ActivityDonateModel;
use Common\Model\DonateBookModel;
use Home\Service\SubscribeService;
use Org\Net\Http;
use Think\Controller;
use Think\Exception;
use Think\Log;

class ShareController extends HomeController
{
    private     $appid      = 'wx4654aaf3c18173d4'; //AppID(应用ID)
    private     $token      = 'weixin'; //微信后台填写的TOKEN
    private     $crypt      = 'ycpUIRAFJyULbDNIvLCV6CaAyPza9lvPkkHVy32V0Yc'; //消息加密KEY（EncodingAESKey）
    private     $appsecret  = '357d4935ef4973e2a2be3b480a08b255';
/*
    private     $appid      = 'wx2d40cc229dd4ade5'; //AppID(应用ID)
    private     $token      = 'beike'; //微信后台填写的TOKEN
    private     $crypt      = 'Dy0XAZMJQ4qHYvnf5J9Tc1tcwTup1SItxrgVrl25x4S'; //消息加密KEY（EncodingAESKey）
    private     $appsecret  = 'b6c29bab7b715dbf055961f8e880441d';
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index(){
        //调试
        try{

            /* 加载微信SDK */
            $wechat = new Wechat($this->token, $this->appid, $this->crypt);


            /* 获取请求信息 */
            $data = $wechat->request();

            if($data && is_array($data)){
                /**
                 * 你可以在这里分析数据，决定要返回给用户什么样的信息
                 * 接受到的信息类型有10种，分别使用下面10个常量标识
                 * Wechat::MSG_TYPE_TEXT       //文本消息
                 * Wechat::MSG_TYPE_IMAGE      //图片消息
                 * Wechat::MSG_TYPE_VOICE      //音频消息
                 * Wechat::MSG_TYPE_VIDEO      //视频消息
                 * Wechat::MSG_TYPE_SHORTVIDEO //视频消息
                 * Wechat::MSG_TYPE_MUSIC      //音乐消息
                 * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
                 * Wechat::MSG_TYPE_LOCATION   //位置消息
                 * Wechat::MSG_TYPE_LINK       //连接消息
                 * Wechat::MSG_TYPE_EVENT      //事件消息
                 *
                 * 事件消息又分为下面五种
                 * Wechat::MSG_EVENT_SUBSCRIBE    //订阅
                 * Wechat::MSG_EVENT_UNSUBSCRIBE  //取消订阅
                 * Wechat::MSG_EVENT_SCAN         //二维码扫描
                 * Wechat::MSG_EVENT_LOCATION     //报告位置
                 * Wechat::MSG_EVENT_CLICK        //菜单点击
                 */

                //记录微信推送过来的数据

Log::write(json_encode($data),Log::INFO);
                /* 响应当前请求(自动回复) */
                //$wechat->response($content, $type);

                /**
                 * 响应当前请求还有以下方法可以使用
                 * 具体参数格式说明请参考文档
                 *
                 * $wechat->replyText($text); //回复文本消息
                 * $wechat->replyImage($media_id); //回复图片消息
                 * $wechat->replyVoice($media_id); //回复音频消息
                 * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
                 * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
                 * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
                 * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
                 *
                 */

                //执行Demo
                $this->response($wechat, $data);
            }
        } catch(\Exception $e){
            Log::write($e->getMessage());
        }

    }

    /**
     * DEMO
     * @param  Object $wechat Wechat对象
     * @param  array  $data   接受到微信推送的消息
     */
    private function response($wechat, $data){
        switch ($data['MsgType']) {
            case Wechat::MSG_TYPE_EVENT:
                switch ($data['Event']) {
                    case Wechat::MSG_EVENT_SUBSCRIBE:
                        //关注

                        if (isset($data['EventKey'])) {

                            //扫描推广二维码关注
                            if (substr($data['EventKey'], 0, 8) === 'qrscene_') {
                                $se = str_replace('qrscene_', '', $data['EventKey']);//推广用户user_id
                            }
                        }
                        $this->subscribe($data['FromUserName'],$se);
                        $wechat->replyText('
终于等到你  欢迎关注医伯乐~');
                        break;

                    case Wechat::MSG_EVENT_UNSUBSCRIBE:
                        //取消关注，记录日志
                        $this->unsubscribe($data['FromUserName']);
                        break;
                    case Wechat::MSG_EVENT_CLICK:

                        break;
                    case Wechat::MSG_EVENT_LOCATION:
                        $this->location($data);
                        break;
                    default:
                        //$wechat->replyText("欢迎访问！");
                        break;
                }
                break;

            case Wechat::MSG_TYPE_TEXT:

                $res            =   $this->keyword($data);

                switch ($res['type']) {
                    case '文本':
                        $wechat->replyText($res['content']);
                        break;

                    case '图片':

                        $wechat->replyImage($res['media_id']);
                        break;

                    case '语音':
                        //$media_id = $this->upload('voice');
                        $media_id = '1J03FqvqN_jWX6xe8F-VJgisW3vE28MpNljNnUeD3Pc';
                        $wechat->replyVoice($media_id);
                        break;

                    case '视频':
                        //$media_id = $this->upload('video');
                        $media_id = '1J03FqvqN_jWX6xe8F-VJn9Qv0O96rcQgITYPxEIXiQ';
                        $wechat->replyVideo($media_id, '视频标题', '视频描述信息。。。');
                        break;

                    case '音乐':
                        //$thumb_media_id = $this->upload('thumb');
                        $thumb_media_id = '1J03FqvqN_jWX6xe8F-VJrjYzcBAhhglm48EhwNoBLA';
                        $wechat->replyMusic(
                            'Wakawaka!',
                            'Shakira - Waka Waka, MaxRNB - Your first R/Hiphop source',
                            'http://wechat.zjzit.cn/Public/music.mp3',
                            'http://wechat.zjzit.cn/Public/music.mp3',
                            $thumb_media_id
                        ); //回复音乐消息
                        break;

                    case '图文':
                        $wechat->replyNewsOnce(
                            "全民创业蒙的就是你，来一盆冷水吧！",
                            "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。",
                            "http://www.topthink.com/topic/11991.html",
                            "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
                        ); //回复单条图文消息
                        break;

                    case '多图文':
                      /*  $news = array(
                            "全民创业蒙的就是你，来一盆冷水吧！",
                            "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。",
                            "http://www.topthink.com/topic/11991.html",
                            "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
                        ); //回复单条图文消息
*/
                        $wechat->replyNews($res['content'][0],$res['content'][1] );
                        break;

                    default:
                       // $wechat->replyText("您输入的内容是：{$data['Content']}");
                        break;
                }
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 资源文件上传方法
     * @param  string $type 上传的资源类型
     * @return string       媒体资源ID
     */
    private function upload($type){
        $appid = C('WX_APP_ID'); //AppID(应用ID)

        $appsecret= C('WX_APPSECRET');


        $token = session("token");

        if($token){
            $auth = new WechatAuth($appid, $appsecret, $token);
        } else {
            $auth  = new WechatAuth($appid, $appsecret);
            $token = $auth->getAccessToken();

            session(array('expire' => $token['expires_in']));
            session("token", $token['access_token']);
        }

        switch ($type) {
            case 'image':
                $filename = './Public/image.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'voice':
                $filename = './Public/voice.mp3';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'video':
                $filename    = './Public/video.mp4';
                $discription = array('title' => '视频标题', 'introduction' => '视频描述');
                $media       = $auth->materialAddMaterial($filename, $type, $discription);
                break;

            case 'thumb':
                $filename = './Public/music.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            default:
                return '';
        }

        if($media["errcode"] == 42001){ //access_token expired
            session("token", null);
            $this->upload($type);
        }

        return $media['media_id'];
    }

    public function keyword($data){
        if($data['Content']=='捐赠'){
            $wechatAuth     =   new WechatAuth($this->appid,$this->appsecret,$this->token);
            $wechatAuth->getAccessToken('client');
            $user           =   $wechatAuth->userInfo($data['FromUserName']);
            Log::write(json_encode($user));
            $SysncLoginM    =   new \Common\Model\SyncLoginModel();
            $sysncUser      =   $SysncLoginM->info($user['unionid']);
            $ActivityDonateM                =   new ActivityDonateModel();
            $where['donate_user']           =   $sysncUser['user_id'];
            $number                         =   $ActivityDonateM->where($where)->count();

            if($number > 0 ){
                $return['type']             =   '文本';
                $return['content']          =   U('Activity/share',array('user_id'=>$sysncUser['user_id']),true,true);
            }else{
                $DonateBookM                =   new DonateBookModel();
                $tempNumber                 =   $DonateBookM->where($where)->sum('number');
                if($tempNumber > 0){
                    $return['type']             =   '文本';
                    $return['content']          =   U('Activity/share',array('user_id'=>$sysncUser['user_id']),true,true);
                }else{
                    return;
                }
            }
        }elseif($data['Content']=='推广'){
            try{
                $wechatAuth = new WechatAuth($this->appid,$this->appsecret,$this->token);
                $wechatAuth->getAccessToken('client');
                $user           =   $wechatAuth->userInfo($data['FromUserName']);
                $SysncLoginM    =   new \Common\Model\SyncLoginModel();
                $sysncUser      =   $SysncLoginM->info($user['unionid']);

                if($sysncUser['user_id']){
                    $res    =   $wechatAuth->qrcodeCreate($sysncUser['user_id']);
                    $qr     =   $wechatAuth->showqrcode($res['ticket']);
                    $file   =   './Uploads/Download/qcode.jpg';
                    Http::curlDownload($qr,$file);
                    // $wechat->replyText('正在为您下载');
                    $media  =   $wechatAuth->mediaUpload($file,'image');
                    // $wechat->replyText('马上就好');
                    if(isset($media['media_id'])){
                        $return = array(
                            'type' =>'图片',
                            'media_id'=>$media['media_id']
                        );
                    }
                }
            }catch(Exception $e){
                Log::write($e->getMessage());
            }

        }
        else{
            $KeywordM               =   D('WxKeyword');
            $where['keyword']       =   $data['Content'];
            $info                   =   $KeywordM->where($where)->find();
            if($info){
                $return = array(
                    'type' =>'文本',
                    'content'=>$info['content']
                );
            }
        }

        return $return;
    }

    public function subscribe($openid,$se){
        $SubscribeS                 =   new SubscribeService();
        $res                        =   $SubscribeS->subscribe($openid,$se);
        if(!$res){
            Log::write($SubscribeS->getError());
        }

    }
    public function unsubscribe($openid){
        $SubscribeS                 =   new SubscribeService();
        $SubscribeS->unsubscribe($openid);
    }

    public function location($data){
        $openid             =   $data['FromUserName'];
        $latitude           =   $data['Latitude'];
        $longitude          =   $data['Longitude'];
        $SubscribeS         =   new SubscribeService();
        $res                =   $SubscribeS->saveLocation($openid,$latitude,$longitude);
        Log::write($SubscribeS->getError());
    }

}