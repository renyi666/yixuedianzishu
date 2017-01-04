<?php
/***
 *
 *                        ,%%%%%%%%,
 *                      ,%%/\%%%%/\%%
 *                     ,%%%\c "" J/%%%
 *            %.       %%%%/ o  o \%%%
 *            `%%.     %%%%    _  |%%%
 *             `%%     `%%%%(__Y__)%%'
 *             //       ;%%%%`\-/%%%'
 *            ((       /  `%%%%%%%'
 *             \\    .'          |
 *              \\  /       \  | |
 *               \\/         ) | |
 *                \         /_ | |__
 *                (___________))))))) 攻城狮
 *
 * @author：gaoyuan
 * @modified_time：2016/11/19 10:47
 * When I wrote this, only God and I understood what I was doing
 * Now, God only knows
 */

namespace Home\Controller;


class BookController extends  HomeController
{

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


    public function  chachong(){
        $parm['isbn']   =   I('isbn');
        $bookM= M('Book');
        $result =   $bookM->where($parm)->find();
        if($result==null||$result==""){
           $this->ajaxReturn('1');
        }
        $this->ajaxReturn('2');

    }

    public  function  addbook(){

        $parm['isbn']   =   I('isbn');
        $bookM= M('Book');
        $result =   $bookM->where($parm)->find();
        if($result!=null||$result!=""){
            $this->ajaxReturn('2');
        }

        $isbn   =   I('isbn');
        $book_result    =   $this->doubanBook($isbn);
        if(!empty($book_result)&&$book_result!=null&&$book_result!=""){
            $book_result['download_url1']   =   I('download_url');
            $book_result['download_url2']   =   I('download_url');
            $book_result['create_time']   =   time();
            $book_result['isbn']   =  $isbn;
            $book_result['user_id']=session('userInfo.uid');
            $book_result['pubdate']=date($book_result['pubdate']);
            $userbookM  =M('UserBook');
           $c= $userbookM->add($book_result);

            $this->ajaxReturn('1');
        }
        $this->ajaxReturn('2');




    }

}