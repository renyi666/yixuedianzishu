<?php
/**
 *　　　　　　　　┏┓　　　┏┓+ +
 *　　　　　　　┏┛┻━━━┛┻┓ + +
 *　　　　　　　┃　　　　　　　┃ 　
 *　　　　　　　┃　　　━　　　┃ ++ + + +
 *　　　　　　 ████━████ ┃+
 *　　　　　　　┃　　　　　　　┃ +
 *　　　　　　　┃　　　┻　　　┃
 *　　　　　　　┃　　　　　　　┃ + +
 *　　　　　　　┗━┓　　　┏━┛
 *　　　　　　　　　┃　　　┃　　　　　　　　　　　
 *　　　　　　　　　┃　　　┃ + + + +
 *　　　　　　　　　┃　　　┃　　　　Code is far away from bug with the animal protecting　　　　　　　
 *　　　　　　　　　┃　　　┃ + 　　　　神兽保佑,代码无bug　　
 *　　　　　　　　　┃　　　┃
 *　　　　　　　　　┃　　　┃　　+　　　　　　　　　
 *　　　　　　　　　┃　 　　┗━━━┓ + +
 *　　　　　　　　　┃ 　　　　　　　┣┓
 *　　　　　　　　　┃ 　　　　　　　┏┛   Author:XiaoFei Zhai
 *　　　　　　　　　┗┓┓┏━┳┓┏┛ + + + +
 *　　　　　　　　　　┃┫┫　┃┫┫
 *　　　　　　　　　　┗┻┛　┗┻┛+ + + +
 */

namespace Home\Service;


use Home\Model\SubscribeModel;
use Shell\Service;
use Shell\Webapi;

class SubscribeService extends Service
{
    private $SubscribeM     ;

    public function _init(){
        $this->SubscribeM               =   new SubscribeModel();
    }

    /**
     * 关注
     * @param $openid
     * @param $fromUser
     * @return bool
     */
    public function subscribe($openid,$fromUser=0){
        $res                           =   $this->SubscribeM->subscribe($openid,$fromUser);
        if($res){
            return true;
        }else{
            $this->setError($this->SubscribeM->getError());
            return false;
        }
    }

    /**
     * 取消关注
     * @param $openid
     * @return bool
     */
    public function unsubscribe($openid){
        $res                           =   $this->SubscribeM->unsubscribe($openid);
        if($res){
            return true;
        }else{
            $this->setError($this->SubscribeM->getError());
            return false;
        }
    }

    /**
     * 存储经纬度
     * @param $openid
     * @param $latitude
     * @param $longitude
     * @return bool
     */
    public function saveLocation($openid,$latitude,$longitude){
        $res                           =   $this->SubscribeM->saveLocation($openid,$latitude,$longitude);
        if($res){
            return true;
        }else{
            $this->setError($this->SubscribeM->getError());
            return false;
        }
    }
    /**
     * 获取经纬度
     * @param $openid
     * @return mixed
     */
    public function getLocation($openid){
        return $this->SubscribeM->getLocation($openid);
    }
    /**
     * 获取城市
     * @param $openid
     * @return mixed
     */
    public function getCity($openid = ''){
        if(empty($openid)){
            $openid     =   session('wx_user.openid');
        }

        $res            =   $this->SubscribeM->getLocation($openid);

        if($res){

            $address    =   Webapi::lacToAdd($res['latitude'],$res['longitude']);

            if(is_array($address)){
                return str_replace('市','',$address['city']);
            }
        }
        return '';
    }
}