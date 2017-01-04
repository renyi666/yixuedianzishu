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
class IndexController extends HomeController {
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
        $this->_webRoot = 'http://www.yixuedianzishu.com/index.php?s=/Home/Index/wxCheck';//返回的域名网址，必须跟网站应用的域名网址相同

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

        if($a==Null){
            $this->error("参数错误请重新登录");

        }else{




            $User=new UserModel();

            $res=$User->field('id,nickname,avatar,openid,unionid')->where($map)->find();



//        $res = $Member->getMemberInfo($map);//查询openid是否存在，而PC和微信端 openid不一致，只有unionid才是唯一标识
            if(is_numeric($res['id'])){


                $userinfo = Array(

                    'uid' => $res['id'],
                    'nickname' => $res['nickname'],
                    'avatar' => $res['avatar'],
                    'openid' => $res['openid'],
                    'unionid' => $res['unionid'],
                );
                $login['updated']   =   time();
                $where['id']    =   $res['id'];
                $User->where($where)->save($login);

            }

            else{


                //很有可能是从这一步开始往后出现错误
                //经过验证这一步的返回值是正确的
                $user = $this->wxUserInfo($ken['access_token'],$ken['openid']);//获取用户信息

                if($user==null||$user==''){

                    $this->error("参数错误请重新登录");

                }
                $data = array(
                    'openid' => $user['openid'],
                    'unionid' => $user['unionid'],
                    'nickname' => $user['nickname'],
                    'avatar' => $user['headimgurl'],
                    'updated' => time(),
                    'created' => time(),
                );


                //写入数据库中

                $crId = $User->add($data);//写入到数据库中,返回的是插入id

                //c插入到score_reoord中
                $scoreinfo = array(
                    'status'=>'-1',
                    'cause'  =>'新用户注册',
                    'score'  =>'+2',
                    'unionid' =>$data['unionid'],
                );
                $score_record=M('ScoreRecord');
                $score_record->add($scoreinfo);

                $userinfo = Array(
                    'uid' => $crId,
                    'nickname' => $user['nickname'],
                    'avatar' => $user['headimgurl'],
                    'openid' => $user['openid'],
                    'unionid' => $user['unionid'],

                );

            }
            session('userInfo', $userinfo);//写入session

            session('jumpurl',$jumpurl);

            $str = $jumpurl;
            //使用正则表达式截取U方法需要的路径
            $isMatched = preg_match('/http:\/\/www.yixuedianzishu.com\/index.php\?s=([^\.]+)./', $str, $matches);


            $jumpurl=$matches['1'];

                  if($jumpurl==Null){

                $this->redirect('index');

            }else {
                $this->redirect($jumpurl);
            }

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
//        return $sContent;
        $aStatus = curl_getinfo($oCurl);//获取CURL连接数据的信息

//return $aStatus;




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

//        $sContent =json_encode(file_get_contents($url));


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


        public  function neike3(){

            $book=M('Book');
            $book_category_relation=M('BookCategoryRelation');

           $res=$book_category_relation->field('book_id')->where('category_id=1')->select();
        $res=array_column($res,'book_id');
            $res=implode(',',$res);
            $data['id']=array('in',$res);
            $start=strtotime('2016-3-1 00:00:00');

            $end=strtotime('2016-3-31 24:00:00');
            $data['pubdate']=array('between',$start,$end);

            $list=$book->where($data)->select();


            $lists = $this->lists($book, $data, 'create_time desc');
            $this->assign('list', $lists);




            $this->display();
        }




    public  function neike4(){

        $book=M('Book');
        $book_category_relation=M('BookCategoryRelation');

        $res=$book_category_relation->field('book_id')->where('category_id=1')->select();
        $res=array_column($res,'book_id');
        $res=implode(',',$res);
        $data['id']=array('in',$res);
        $start=strtotime('2016-4-1 00:00:00');

        $end=strtotime('2016-4-30 24:00:00');
        $data['pubdate']=array('between',$start,$end);

        $list=$book->where($data)->select();


        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);




        $this->display();
    }
    public  function  new_5(){
        $book=M('Book');
        $start=strtotime('2016-5-1 00:00:01');

        $end=strtotime('2016-5-31 24:00:59');
        $data['pubdate']=array('between',$start,$end);

        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);
        $this->display();

    }
    public  function  new_4(){
        $book=M('Book');
        $start=strtotime('2016-4-1 00:00:01');

        $end=strtotime('2016-4-30 24:00:59');
        $data['pubdate']=array('between',$start,$end);

        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);
        $this->display();

    }


    public  function neike5(){

        $book=M('Book');
        $book_category_relation=M('BookCategoryRelation');

        $res=$book_category_relation->field('book_id')->where('category_id=1')->select();
        $res=array_column($res,'book_id');
        $res=implode(',',$res);
        $data['id']=array('in',$res);
        $start=strtotime('2016-5-1 00:00:01');

        $end=strtotime('2016-5-31 24:00:59');
        $data['pubdata']=array('between',$start,$end);

        $list=$book->where($data)->select();


        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);




        $this->display();
    }
    public  function kaoshi_5(){

        $book=M('Book');
        $book_category_relation=M('BookCategoryRelation');

        $res=$book_category_relation->field('book_id')->where('category_id=93')->select();
        $res=array_column($res,'book_id');
        $res=implode(',',$res);
        $data['id']=array('in',$res);
        $start=strtotime('2016-5-1 00:00:01');

        $end=strtotime('2016-5-31 23:59:59');
        $ceshi=array($start,$end);
        $data['pubdate']=array('between',$ceshi);

        $list=$book->where($data)->select();


        $lists = $this->lists($book, $data, 'create_time desc');

        $this->assign('list', $lists);




        $this->display();
    }
    public  function kaoshi_4(){

        $book=M('Book');
        $book_category_relation=M('BookCategoryRelation');

        $res=$book_category_relation->field('book_id')->where('category_id=93')->select();
        $res=array_column($res,'book_id');
        $res=implode(',',$res);
        $data['id']=array('in',$res);
        $start=strtotime('2016-4-1 00:00:01');

        $end=strtotime('2016-4-30 23:59:59');
        $data['pubdate']=array('between','$start,$end');

        $list=$book->where($data)->select();


        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);




        $this->display();
    }
    public  function kaoshi_3(){

        $book=M('Book');
        $book_category_relation=M('BookCategoryRelation');

        $res=$book_category_relation->field('book_id')->where('category_id=93')->select();
        $res=array_column($res,'book_id');
        $res=implode(',',$res);
        $data['id']=array('in',$res);
        $start=strtotime('2016-3-1 00:00:01');

        $end=strtotime('2016-3-31 23:59:59');
        $data['pubdate']=array('between','$start,$end');

        $list=$book->where($data)->select();


        $lists = $this->lists($book, $data, 'create_time desc');
        $this->assign('list', $lists);




        $this->display();
    }

    //系统首页
    public function index(){

        $book_category=M('BookCategory');
        $book_category_relation=M('BookCategoryRelation');
        $pictureM   =   D('Picture');
        $User=M('User');
        $id=I('id');
        $where['pid']='0';
        $result=$book_category->where($where)->order('sort')->select();

        foreach ($result as $k => $v){


            $where1['pid']=$v['id'];
            $result1=$book_category->where($where1)->select();
            $result[$k]['item']=$result1;
        }

       $this->assign('result',$result);

        $this->get_session();
        $book=M('Book');
        if(is_numeric($id)){
            $where2['category_id']=$id;
            $where5['id']=$id;
            $result4=$book_category->field('pid')->where($where5)->find();
            if($result4['pid']=='0'){
                $result4['pid']=$id;


            }
            $res['id']=$id;
            $this->assign('id',$res);
            $this->assign('result4',$result4);
           $result3=$book_category_relation->where($where2)->select();

            $result3=array_column($result3,'book_id');
            $result3=implode(',',$result3);
            $where4['id']=array('in',$result3);
            $lists = $this->lists($book,$where4);
            foreach ($lists as $k => $v){

                if(is_numeric($v['image_l'])){
                    $where6['id']=$v['image_l'];
                    $result6=  $pictureM->where($where6)->find();

                    $lists[$k]['image_l']=$result6['path'];

                }
            }
//            $lists=$book->where($where4)->select();
        }else{
            $lists = $this->lists($book, '', 'create_time desc');
            foreach ($lists as $k => $v){

                if(is_numeric($v['image_l'])){
                    $where6['id']=$v['image_l'];
                    $result6=  $pictureM->where($where6)->find();

                    $lists[$k]['image_l']=$result6['path'];

                }
            }

        }


        //数据分页
//        $count      = $book->count();
//       $Page       = new \Think\Page($count,25);
//
//       $show       = $Page->show();

        //根据创建时间排序


        $this->assign('list', $lists);




        //$list=$book->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

       // $this->assign('list',$list);
       // $this->assign('page',$show);
       
        $this->display();
    }

    /**
     * @return config
     */
    //获取session中的用户细信息
    public function get_session()
    {
        $data=session('userInfo');



        $this->assign('data',$data);


    }

    //退出
    public  function out(){
        $return_url=$_SERVER["HTTP_REFERER"];
        $str = $return_url;
        //使用正则表达式截取U方法需要的路径
        $isMatched = preg_match('/http:\/\/www\.yixuedianzishu\.com\/index\.php\?s=\/([^\.]+)./', $str, $matches);

        $return_url=$matches['1'];

        session(null);
        $this->redirect($return_url);



    }
    //搜索页面
    public function search(){
        $this->get_session();
        $pictureM   =   D('Picture');
        $book_category=M('BookCategory');
        $book_category_relation=M('BookCategoryRelation');
        $User=M('User');
        $id=I('id');

        $where['pid']='0';
        $result=$book_category->where($where)->order('sort')->select();

        foreach ($result as $k => $v){
            $where1['pid']=$v['id'];
            $result1=$book_category->where($where1)->select();
            $result[$k]['item']=$result1;
        }

        $this->assign('result',$result);
        $book=M('Book');

        $result=I('title');

        if (is_numeric($result)) {
            $map['isbn|title'] = array('like', '%' . $result . '%');
        } else {
            $map['title'] = array('like', '%' . (string)$result . '%');
        }



$list=$this->lists($book,$map);
        foreach ($list as $k => $v){

            if(is_numeric($v['image_l'])){
                $where6['id']=$v['image_l'];
                $result6=  $pictureM->where($where6)->find();

                $list[$k]['image_l']=$result6['path'];

            }
        }

        $this->assign('list',$list);
        $this->display();
    }

    //时间排序页面
    public function time_sort(){
        $this->get_session();
        $pictureM   =   D('Picture');
        $book_category=M('BookCategory');
        $book_category_relation=M('BookCategoryRelation');
        $User=M('User');
        $id=I('id');
        $where['pid']='0';
        $result=$book_category->where($where)->order('sort')->select();

        foreach ($result as $k => $v){
            $where1['pid']=$v['id'];
            $result1=$book_category->where($where1)->select();
            $result[$k]['item']=$result1;
        }

        $this->assign('result',$result);
        $this->get_session();
        $book=M('Book');
        if(is_numeric($id)){
            $where2['category_id']=$id;
            $where5['id']=$id;
            $result4=$book_category->field('pid')->where($where5)->find();
            if($result4['pid']=='0'){
                $result4['pid']=$id;


            }
            $res['id']=$id;
            $this->assign('id',$res);
            $this->assign('result4',$result4);

            $result3=$book_category_relation->where($where2)->select();

            $result3=array_column($result3,'book_id');
            $result3=implode(',',$result3);
            $where4['id']=array('in',$result3);
            $lists = $this->lists($book,$where4,'pubdate desc');
            foreach ($lists as $k => $v){

                if(is_numeric($v['image_l'])){
                    $where6['id']=$v['image_l'];
                    $result6=  $pictureM->where($where6)->find();

                    $lists[$k]['image_l']=$result6['path'];

                }
            }
//            $lists=$book->where($where4)->select();
        }else{
            $lists = $this->lists($book, '', 'pubdate desc');
            foreach ($lists as $k => $v){

                if(is_numeric($v['image_l'])){
                    $where6['id']=$v['image_l'];
                    $result6=  $pictureM->where($where6)->find();

                    $lists[$k]['image_l']=$result6['path'];

                }
            }
        }


        //数据分页
//        $count      = $book->count();
//       $Page       = new \Think\Page($count,25);
//
//       $show       = $Page->show();

        //根据创建时间排序


        $this->assign('list', $lists);




        //$list=$book->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        // $this->assign('list',$list);
        // $this->assign('page',$show);

        $this->display();

    }

    //图书详情页面
    public function book(){
        $rec['id'] = I('id');

        $data[]=session('userInfo');
        $user_id['user_id']=$data['0']['uid'];
        $this->assign('data',$data);
       // dump($user_id);
        //dump($data);
        //实例化举报表
        $report=M('Report');
        $rep_result=$report->where($user_id)->select();
        $rep_names = array_column($rep_result, 'book_id');
        if (in_array( $rec['id'], $rep_names)){
            $rep_result['res']="1";
        }else{
            $rep_result['res']="2";
        }
        $this->assign('rep_result',$rep_result);

        //实例化购买记录表
        $buyRecord=M('Buyrecord');
        //从表里查询出id用户的购买记录
        $br_result=$buyRecord->where($user_id)->select();

        //把二维数组转换成一位数组

        $names = array_column($br_result, 'book_id');




        //判断该图书的id是否在购买记录的数组中
        if (in_array( $rec['id'], $names)){
            $buy_result['res']="1";
        }else{
            $buy_result['res']="2";
        }

        $this->assign('buy_result',$buy_result);

        $this->query_score();
        //实例化数据库
        $book = D('Book');
        $bcr = D('BookCategoryRelation');
        $bc = D('BookCategory');
        $pictureM=D('Picture');

        $list = $book->where($rec)->select();

        $str = $list['0']['download_url1'];
        $isMatched = preg_match('/链接: ([^\ ]+)/', $str, $matches);
        //var_dump( $matches);

        $isMatched = preg_match('/密码: ([^、]+)/', $str, $matches1);;
        //var_dump($matches1);
        //把出版时间时间戳转换为正常日期
        $list['0']['pubdate']=  date('Y-m-d',$list['0']['pubdate']);
        $list['0']['download_url1']=$matches['1'];
        $list['0']['download_url1_mima']=$matches1['1'];
        if(is_numeric( $list['0']['image_l'])){
            $where6['id']= $list['0']['image_l'];
            $result6=  $pictureM->where($where6)->find();

            $list['0']['image_l']=$result6['path'];

        }
        //dump($list['0']['download_url1_mima']);
        //根据id关联book_category_releation表查询
        $where['book_id']=$list['0']['id'];
        $result=$bcr->field('category_id')->where($where)->select();
      //获取id结果   吧结果放在一维数组里
        $result_number=count($result);
        for($i=0;$i<$result_number;$i++){
            $cate[]=$result[$i]['category_id'];
        }

      //区间查询
        $map['id']  = array('in',$cate);
       //从表里查出分类的名称
        $result_cate = $bc->field('title')->where($map)->select();
        $num_cate = count($result_cate);
        for($i=0;$i<$num_cate;$i++){
            $result_cate1[] = $result_cate[$i]['title'];

       }
        $this->meta_title = $list['0']['title']."_医学电子书";
        $this->assign('list',$list);
        $this->assign('result_cate',$result_cate);

        $this->display();


    }

    public function login()
    {



        $this->display();

    }


    //查询用户积分
    public  function  query_score(){
        $User=new UserModel();
       $query['id']=session('userInfo.uid');

        $score_result=$User->field('score')->where($query)->select();

        $this->assign('score_result',$score_result);


    }
        //赚取积分
        public function  earn_score(){
            $list=session('userInfo');

            $this->assign('list',$list);

            $this->display();
        }
    public function  earn_score2(){
        $list=session('userInfo');

        $this->assign('list',$list);

        $this->display();
    }
    public function  earn_score3(){
        $list=session('userInfo');

        $this->assign('list',$list);

        $this->display();
    }
        //  花费积分购买电子书
        public  function  buy_book($user_id = '',$book_id='',$book_name=''){

            $uid['id']=$user_id;
            $User=new UserModel();
            $result=$User->info($uid);

            $res['score']=$result['score']-1;


            if($res['score']<0){

                $data = 'no';
            }else{

                $User->where($uid)->save($res);
            //写入购买记录表
            $buyRecord=M('Buyrecord');
            $receive['user_id']=$user_id;
           $a= $User->field('unionid')->where($receive)->select();
            $cc=$receive['book_id']=$book_id;
            $receive['book_name']=$book_name;
            $buyRecord->add($receive);
            $score_record=M('ScoreRecord');
            $list=session('userInfo');

            $scoreinfo=Array(
                'status'=>'-2',
                'cause'  =>'购买图书',
                'book_name'=>$receive['book_name'],
                'address'=>'http://www.yixuedianzishu.com/index.php?s=/Home/Index/book/id/'.$receive['book_id'].'.html',
                'score'  =>'-1',
                'unionid' =>$list['unionid'],
            );

            $score_record->add($scoreinfo);

            //ajax返回值
         $data = 'ok';
            }
//            $data = $list;
            $this->ajaxReturn($data);





        }

        //举报失效链接
        public function  report($user_id = '',$book_id=''){
                $report=M('Report');
                $uid['user_id']=$user_id;
                $uid['book_id']=$book_id;
            $uid['time']=time();
                $report->add($uid);
                $data = 'ok';
                $this->ajaxReturn($data);
        }

    public function oauth2(){
        $code = $_GET['code'];


        dump($code);



    }
        //积分记录
    public  function  score(){
        $list=session('userInfo');
        $buyRecord = M('Buyrecord');
        $where['user_id']=$list['uid'];
        $where1['id']=$list['uid'];
        $user=M('User');
        $result=$user->where($where1)->select();

        $result2=$result['0'];
        $this->assign('result2',$result2);
        $buyRecord_result=$buyRecord->where($where)->select();

        $nu=count($buyRecord_result);
        $score_record=M('ScoreRecord');

        $where2['unionid']=$result2['unionid'];
        $result3=$score_record->where($where2)->order('time desc')->select();
        $this->assign('result3',$result3);
        $this->assign('list',$list);

        $this->display();
    }

    //左侧菜单栏
    public  function  profession_test(){
        $receive = I('month');
        $this->get_session();
        $book = M('Book');

        //数据分页
        $count      = $book->count();
        $Page       = new \Think\Page($count,25);

        $show       = $Page->show();

        //根据创建时间排序

//        $lists = $this->lists($book, '', 'create_time desc');
//        $this->assign('_list', $lists);




        $list=$book->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('list',$list);
        $this->assign('page',$show);

        $this->display();

    }



}