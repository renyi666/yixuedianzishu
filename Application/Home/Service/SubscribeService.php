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
use Shell\Service\Service;

class SubscribeService extends Service
{
    private $SubscribeM     ;

    public function _init(){
        $this->SubscribeM               =   new SubscribeModel();
    }

    /**
     * 关注
     * @param $openid
     * @return bool
     */
    public function subscribe($openid){
        $res                           =   $this->SubscribeM->subscribe($openid);
        if($res===true){
            return true;
        }else{
            $this->setError($this->SubscribeM->getError());
            return false;
        }
    }

    public function unsubscribe($openid){
        $res                           =   $this->SubscribeM->unsubscribe($openid);
        if($res){
            return true;
        }else{
            $this->setError($this->SubscribeM->getError());
            return false;
        }
    }
}