<?php
/**
 * ┌--------------------------------┐
 * │ ▉▉▉▉▉▉▉▉▉▉▉▉▉ 99.9%  │
 * │ ------------------------------ │
 * │        Author：   翟小斐        │
 * │ ------------------------------ │
 * │        Email:  jcove@163.com   │
 * │ ------------------------------ │
 * │       正在努力中，请稍候...      │
 * └--------------------------------┘
 */

namespace Common\Model;


use Think\Model;

class SyncLoginModel extends Model
{
    public function edit($data){
        if(empty($data)){
            $data = $this->create();
        }
        if($data){
            if(!isset($data['openid']) || empty($data['openid'])){
                $this->error    =   'openid无效';
                return false;
            }
            if($this->checkField($data)){
                return $this->add($data);
            }else{
                $where['openid']    =   $data['openid'];
                $where['from']      =   $data['from'];
                unset($data['openid']);
                if(empty($data['user_id'])){
                    unset($data['user_id']);
                }
                if(empty($data['from'])){
                    unset($data['from']);
                }
                if(empty($data['nick'])){
                    unset($data['nick']);
                }
                if(empty($data['avatar'])){
                    unset($data['avatar']);
                }
                return $this->where($where)->save($data);
            }
        }
        $this->error    =   '数据无效';
        return false;
    }

    public function info($openid,$field = true){
        $where['openid']    =   $openid;
        return $this->field($field)->where($where)->find();
    }
    public function checkUser($openid){
        $where['openid']    =   $openid;
        $count              =   $this->where($where)->count();
        return $count > 0 ? false : true;
    }
    public function getAvatar($userId){
        $openid =   I('openid');
        if(!empty($openid)){
            $where['openid'] = $openid;
        }
        $where['user_id']    =   $userId;
        $field =  $this->field('avatar')->where($where)->find();

        if(empty($field)){
            return '';
        }

        return $field['avatar'];
    }

    public function checkField($data){

        $where['openid']    =   $data['openid'];
        $where['from']      =   $data['from'];
        $count              =   $this->where($where)->count();
        return $count > 0 ? false : true;
    }
}