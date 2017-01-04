<?php
/**
 * BookController.class.php.
 * Author: Jace
 * Date: 2016/2/19
 * Time: 15:58
 */

namespace Admin\Controller;


use Admin\Model\BookCategoryModel;
use Admin\Model\BookCategoryRelationModel;
use Admin\Model\BookModel;


class BookController extends AdminController
{
    public function index()
    {
        $title = I('title');

        if (is_numeric($title)) {
            $map['isbn|title'] = array($title, array('like', '%' . $title . '%'), '_multi' => true);
        } else {
            $map['title'] = array('like', '%' . (string)$title . '%');
        }

        $Model = new BookModel();
        $list = $this->lists($Model, $map);
        $pictureM=D('Picture');
        foreach($list as $k => $v){
            if(is_numeric($v['image_l'])){
                $where['id']=$v['image_l'];
          $result=  $pictureM->where($where)->find();
                $list[$k]['image_l']=$result['path'];

            }
            if(empty($v['download_ulr1'])){
                if(empty($v['download_ulr2'])){
                    $list[$k]['count'] =0;
                }else{
                    $list[$k]['count'] =0;
                }
            }else{
                if(empty($v['download_ulr2'])){
                    $list[$k]['count'] =1;
                }else{
                    $list[$k]['count'] =2;
                }
            }
        }

        $this->assign('title',$title);
        $this->assign('_list', $list);
        $this->meta_title = '图书信息';
        $this->display();

    }

    public function info(){
        $isbn = I('isbn');
        $BookM  = new BookModel();
        $book   = $BookM->getBookByISBN($isbn);

        $this->assign('book',$book);
        $action = I('action','on_sale');
        switch($action){
            case 'on_sale':
                $MarketM = new MarketModel();
                $Model = $MarketM;
                $field = 'id,uid,book_title,price,isbn,author,number,brief,create_time';
                $where['isbn'] = $isbn;
                $where['status'] = 1;
                break;
            case 'sell':
                $Model = new OrderModel();;
                $where['isbn'] = $isbn;
                $order = 'create_time desc';
                break;
            case 'follow':
                $Model = new FollowModel();;
                $where['isbn'] = $isbn;
                break;
            default:
                break;
        }

        $list = $this->lists($Model,$where,$order,$field);

        switch($action){
            case 'on_sale':
                if(!empty($list)) {
                    $ReadGroupMarketM = new ReadGroupMarketModel();
                    foreach ($list as $k => $v) {
                        $list[$k]['nickname'] = get_nickname($v['uid']);
                        $list[$k]['read_group_name'] = $ReadGroupMarketM->getReadGroupNameByMarket($v['id']);
                    }
                }
                break;
            case 'sell':
                if(!empty($list)){
                    foreach($list as $k=>$v){
                        $list[$k]['buyer_name'] = get_nickname($list[$k]['buyer']);
                        $list[$k]['seller_name'] = get_nickname($list[$k]['seller']);
                    }
                }
                break;
            case 'follow':
                if(!empty($list)){
                    foreach($list as $k=>$v){
                        $list[$k]['nickname'] = get_nickname($list[$k]['uid']);
                    }
                }
                break;
            default:
                break;
        }


        $this->assign('action',$action);
        $this->assign('_list',$list);
        $this->display();

    }
    public function edit(){
        $id = I('id');
        $BookM = new BookModel();
        if(IS_POST){

            if($BookM->edit()!==false){
                $this->success('保存成功',U('index'));
            }else{
                $this->error($BookM->getError());
            }
        }

        if(!empty($id)){

            $book   = $BookM->info($id);
            $this->assign('book',$book);

            $BookCategoryRelationM = new BookCategoryRelationModel();
            $categoryIds = $BookCategoryRelationM->getCategorys($id);
            $book['category'] = $categoryIds;
        }

        $BookCategoryM  =   new BookCategoryModel();
        $category       =   $BookCategoryM->getTree();

        foreach($category as $k=>$v){
            if(in_array($category[$k]['id'],$book['category'])){
                $category[$k]['selected'] = 'selected';
            }
            if(isset($v['_'])){
                foreach($v['_'] as $key => $value){
                    if(in_array($value['id'],$book['category'])){
                        $category[$k]['_'][$key]['selected'] = 'selected';
                    }

                }
            }
        }
        $this->assign('category',$category);
        $this->meta_title = '编辑图书';
        $this->display();
    }
    public function getBookByIsbn(){
        $isbn = I('isbn');
        $BookM= new BookModel();
        $book = $BookM->doubanBook($isbn);
        $this->ajaxReturn($book);
    }
    public function del(){
        $id =   I('id');
        $where['id']    =   $id;
        $BookM = new BookModel();
        if($BookM->where($where)->delete()!==false){
            $this->success('删除成功');
        }else{
            $this->error($BookM->getError());
        }
    }
    public  function  report(){
        $report=M('Report');
        $User=M('User');
        $book=M('Book');
        $list=$this->lists($report);
        $nu=count($list);
        for ($i='0';$i<$nu;$i++){

            $where['id']=$list[$i]['user_id'];
            $where1['id']=$list[$i]['book_id'];

            $result=$User->field('nickname')->where($where)->find();

            $list[$i]['nickname']=$result['nickname'];
            $result1=$book->field('title')->where($where1)->find();
            $list[$i]['book_name']=$result1['title'];
            $list[$i]['time']=date('Y-m-d H:i:s',$list[$i]['time']);


        }

        $this->assign('_list',$list);

        $this->display();


    }
//生成链接
    public function lianjie($id=''){
        $lianjie=M('Lianjie');
        $result=$lianjie->order('create_time desc')->limit(1)->select();

        $where['id']=$result['0']['id'];
        $res['number']=$result['0']['number']+1;
        $res['create_time']=time();
        $res['address']=$result['0']['address'];
        $res['address1']=$result['0']['address1'];
        $fin=$result['0']['address'].$res['number'].$result['0']['address1'];
        $lianjie->add($res);
        $data=$fin;
        $this->ajaxReturn($data);



    }

public  function  user(){
    $id=I('title');

    if (is_numeric($id)) {
        $map['id|nickname'] = array('like', '%' . $id . '%');
    } else {
        $map['nickname'] = array('like', '%' . (string)$id . '%');
    }

    $user=M('User');
    $list=$this->lists($user,$map,'created desc');

    $this->assign('list',$list);

    $this->display();
}
    public  function  jifen(){
        $yuedu=M('Yuedu');
        $lianjie=M('Lianjie');
        $result=$yuedu->field('status,count(status) as a')->group('status')->select();

        $nu=count($result);
        for($i=0;$i<$nu;$i++){
            $where['number']=$result[$i]['status'];

            $result1=$lianjie->where($where)->select();

            $result[$i]['create_time']=$result1['0']['create_time'];
            $result[$i]['create_time']=date("Y-m-d H:i:s", $result1['0']['create_time']);
            $result[$i]['address']="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx998e66817ed2b1ac&redirect_uri=http://www.yixuedianzishu.com/index.php?s=/Home/Gongzhonghao/index&response_type=code&scope=snsapi_userinfo&state=".$result[$i]['status']."#wechat_redirect";


        }

        $this->assign('_list',$result);
        $this->display();
    }
        public  function  addscore($user_id,$add_score){
            $where['id']    =  $user_id;
            $data['score']    =  $add_score;
            $userM  =   D('User');
            $scoreRecord=D('ScoreRecord');
            $result =   $userM->where($where)->find();

           if(is_array($result)){

               $data1['score']   =   $result['score']+$data['score'];
                $a=$userM->where($where)->save($data1);
      if($a==1){
                   $data2['unionid']=$result['unionid'];
                 $data2['cause']="贡献新电子书";
                   $data2['score']="+".$data['score'];
                   $data2['score']="+".$data['score'];
          $data2['status']=5;
                   $scoreRecord->add($data2);
               }
           }
          

                $data="ok";
            $this->ajaxReturn($data);
        }
}