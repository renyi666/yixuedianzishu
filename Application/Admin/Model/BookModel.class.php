<?php

namespace Admin\Model;


use Think\Model;

class BookModel extends Model
{

    protected $_auto = array(
        /* 验证书名 */
        array('pubdate', 'strtotime',self::MODEL_BOTH, 'function'),
        array('create_time',NOW_TIME,self::MODEL_INSERT),
    );
    public function info($id){
        $where['id'] = $id;
        $book =  $this->where($where)->find();
        if(!empty($book)){
            if(empty($book['download_ulr1'])){
                if(empty($book['download_ulr2'])){
                    $book['count'] =0;
                 }else{
                    $book['count'] =0;
                }
            }else{
                if(empty($book['download_ulr2'])){
                    $book['count'] =1;
                }else{
                    $book['count'] =2;
                }
            }
        }
        return $book;

    }
    public function edit(){
        $data = $this->create();


        if(!$this->_validate($data)){
            return false;
        };

        if(isset($data['id']) && $data['id'] > 0){
            $where['id'] = $data['id'];
            if($this->where($where)->save($data)===false){
                return false;
            }

        }else{
            if($data){

                if($this->add()){
                    $data['id'] = $this->getLastInsID();
                }else{
                    return false;
                }
            }
        }

        $BookCategroyRelationM = D('BookCategoryRelation');
        return $BookCategroyRelationM->update($data['id']);

    }
    public function getBookByISBN($isbn , $field = true){
        if(is_string($isbn) && strpos($isbn,',')){
            $where['isbn'] = array('in',$isbn);
            $res = $this->field($field)->where($where)->select();

            if(!empty($res) && isset($res['image_l'])){
                foreach($res as $k =>$v){
                    $res[$k]['image'] = $v['image_l'] ?  $v['image_l'] :( $v['image_m'] ?  $v['image_m'] : ($v['image_s'] ?  $v['image_s']:''));
                }

            }
        }else{
            $where['isbn'] = $isbn;
            $res = $this->field($field)->where($where)->find();

            if(!empty($res) && isset($res['image_l'])){
                $res['image'] = $res['image_l'] ?  $res['image_l'] :( $res['image_m'] ?  $res['image_m'] : ($res['image_s'] ?  $res['image_s']:''));
            }
        }


        return $res;
    }
    public function doubanBook($isbn){

        $url = 'https://api.douban.com/v2/book/isbn/'.$isbn
            .'?fields=title,isbn13,images,author,pubdate,publisher,price,pages';

        $info = execute_curl($url);

        if(!empty($info)) {
            if (empty($info['code'])) {
                $info['author'] = arr2str($info['author']);
                $info['isbn'] = $info['isbn13'];

                $info['image_s'] = $info['images']['small'] ? $info['images']['small'] : '';
                $info['image_m'] = $info['images']['medium'] ? $info['images']['medium'] : '';
                $info['image_l'] = $info['images']['large'] ? $info['images']['large'] : '';
                $info['image'] = $info['image_l'] ? $info['image_l'] : ($info['image_m'] ? $info['image_m'] : ($info['image_s'] ? $info['image_s'] : ''));
                unset($info['images']);
                $tmp_price = $info['price'];
                $price = explode(' ', $info['price']);

                if (empty($price['1'])) {
                    $regexp = '/(\d+)\.(\d+)/is';
                    preg_match($regexp, $info['price'], $arr);

                    $info['price'] = $arr[0];
                } else {
                    $info['price'] = $price['1'];
                }
                if (empty($info['price'])) {
                    $info['price'] = floatval($tmp_price);
                }
                unset($info['isbn13']);
            }
        }
        return $info;
    }
    public function _validate($data){
        if(isset($data['title'])){
            $length =   strlen($data['title']);
            if($length>200 || $length==0){
                $this->error    =   '名称长度必须在1-200字符之间';
                return false;
            }

        }
        if(isset($data['id']) && $data['id'] > 0){
            $info   =   $this->info($data['id']);
            if($info){
                if($data['isbn'] ==$info['isbn']){
                    unset($data['name']);
                }
            }

        }else{
            $where['isbn']   =  $data['isbn'];
            $count           =  $this->where($where)->count();
            if($count>0){
                $this->error    =   'isbn已存在';
                return false;
            }
        }
        $this->data($data);
        return true;
    }
}