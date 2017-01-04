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

namespace Home\Model;


use Think\Model;

class SubscribeModel extends Model
{
    /* 模型自动完成 */
    protected $_auto = array(
        array('subscribe_time',NOW_TIME, self::MODEL_INSERT),
        array('user_id', UID, self::MODEL_INSERT),
        array('subscribe', 1, self::MODEL_INSERT),
    );

    protected $_validate = array(
        array('openid', 'require', 'openid必须', self::MUST_VALIDATE),
    );

    /**
     * 关注
     * @param $openid
     * @param $fromUser
     * @return bool
     */
    public function subscribe($openid,$fromUser = 0){
        $data                   =   $this->create(array('openid'=>$openid,'from_user'=>$fromUser));
        if($data){
            if($this->hasRecord($data['openid'])){
                $where['openid']=   $data['openid'];
                return $this->where($where)->save(array('subscribe' => 1));
            }else{
                return $this->add($data);
            }
        }
        return false;
    }

    /**
     * 取消关注
     * @param $openid
     * @return bool
     */
    public function unsubscribe($openid){
        $where['openid']            =   $openid;


        if($this->hasRecord($openid)){
            $where['openid']            =   $openid;
            $data['unsubscribe_time']   =   NOW_TIME;
            $data['subscribe']          =   0;
            return $this->where($where)->save($data);
        }else{
            $data                       =   $this->create(array('openid'=>$openid));
            if($data){
                $data['subscribe']      =   0;
                $data['unsubscribe_time']=  NOW_TIME;
                return $this->add($data);
            }
        }
        return false;
    }

    /**
     * 已有记录
     * @param $openid
     * @return bool
     */
    public function hasRecord($openid){
        $where['openid']        =   $openid;
        return $this->where($where)->count() > 0 ? true : false;
    }

    /**
     * 已关注
     * @param $openid
     * @return bool
     */
    public function hasSubscribe($openid){
        $where['openid']        =   $openid;
        $res                    =   $this->field('subscribe')->where($where)->find();
        if($res['subscribe']==1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 绑定用户
     * @param $openid
     * @param $userId
     * @return bool
     */
    public function bindUser($openid,$userId){
        $where['openid']            =   $openid;
        $data['user_id']            =   $userId;
        return $this->where($where)->save($data);
    }

    /**
     * 存储经纬度
     * @param $openid
     * @param $latitude
     * @param $longitude
     * @return bool
     */
    public function saveLocation($openid,$latitude,$longitude){
        $where['openid']            =   $openid;
        $data['latitude']           =   $latitude;
        $data['longitude']          =   $longitude;
        $data['openid']             =   $openid;
        if($this->create($data)){
            return $this->where($where)->save();
        }else{
            return false;
        }
    }

    /**
     * 获取经纬度
     * @param $openid
     * @return mixed
     */
    public function getLocation($openid){
        $where['openid']            =   $openid;
        return $this->field('latitude,longitude')->where($where)->find();
    }

    public function getUserId($openid){
        $where['openid']            =   $openid;
        return $this->where($where)->getField('user_id');
    }
}