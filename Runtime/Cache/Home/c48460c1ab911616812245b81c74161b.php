<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title></title>
    <link rel="stylesheet" href="/dzs/Public/Home/css/css.css" />
    <script type="text/javascript" src="/dzs/Public/Home/js/jquery.min.js" ></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.inactive').click(function(){
                if($(this).siblings('ul').css('display')=='none'){
                    $(this).parent('li').siblings('li').removeClass('inactives');
                    $(this).addClass('inactives');
                    $(this).siblings('ul').slideDown(100).children('li');
                    if($(this).parents('li').siblings('li').children('ul').css('display')=='block'){
                        $(this).parents('li').siblings('li').children('ul').parent('li').children('a').removeClass('inactives');
                        $(this).parents('li').siblings('li').children('ul').slideUp(100);
                    }
                }else{
                    $(this).removeClass('inactives');
                    $(this).siblings('ul').slideUp(100);
//						$(this).siblings('ul').children('li').children('ul').parent('li').children('a').addClass('inactives');
//						$(this).siblings('ul').children('li').children('ul').slideUp(100);
//						$(this).siblings('ul').children('li').children('a').removeClass('inactives');
                }
            })
        });




    </script>
    <style type="text/css">



        .search-form{float: left; background-color: #f0f0f0;padding:5px; margin-left: 500px;}
        .search-text{height:25px;line-height: 25px;float: left;width: 350px;border: 0;outline: none;}
        .search-button{background-image: url(/dzs/Public/Home/images/search-button.png);width:29px;height:29px;float: left;border: 0}



        a img{ border:none}

    </style>

</head>
<body>
<div class="head">
    <div class="head_cont">
        <a href="<?php echo U('index');?>" class="logo"></a>
        <div class="weixin">
            <a href="<?php echo U('wxLogin');?>">微信登录</a>
        </div>
        <div class="yonghu">
            <a href="#">张三丰</a>
            <a href="#">个人中心</a>
            <a href="#">退出</a>
        </div>
    </div>
</div>
<div class="con">
    <div class="ad">
        <a href="#"><img src="/dzs/Public/Home/images/m1.jpg" /></a>
        <a href="#"><img src="/dzs/Public/Home/images/m2.jpg" /></a>
        <a href="#"><img src="/dzs/Public/Home/images/m3.jpg" /></a>
    </div>
    <div class="cont">
        <div class="left">
            <div class="list">
                <ul class="yiji">
                    <li class="all"><a href="<?php echo U('index');?>">全部</a></li>
                    <li class="all_list"><a href="#" class="inactive">2016年新书</a>
                        <ul style="display: none">
                            <li><a href="#">5月</a></li>
                            <li class="last"><a href="#">4月</a></li>
                        </ul>
                    </li>
                    <li class="all_list"><a href="#" class="inactive">执业医师考试</a>
                        <ul style="display: none">
                            <li><a href="#">5月</a></li>
                            <li><a href="#">4月</a></li>
                            <li class="last"><a href="#">3月</a></li>
                        </ul>
                    </li>
                    <li class="all_list"><a href="#" class="inactive">内科</a>
                        <ul style="display: none">
                            <li><a href="#">5月</a></li>
                            <li><a href="#">4月</a></li>
                            <li class="last"><a href="#">3月</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="Public_number">
                <a href="#"><img src="/dzs/Public/Home/images/m4.jpg" ></a>
            </div>
        </div>



        <div class="right">
            <div class="bg-div">



                <form class="search-form" action="<?php echo U('Index/search');?>"  method="post">
                    <input type="text" class="search-text" name="title" placeholder="书名或ISBN码"/>
                    <input type="submit" class="search-button" value=""/>
                </form>
            </div>




        <!--<div class="right">-->
            <!--<div class="search">-->
                <!--<input id="test" name="search" type="text"  placeholder="书名或ISBN码" /><a href="<?php echo U('search');?>"></a>-->
            <!--</div>-->



            <div class="sort">
                <span>排序：</span>
                <a href="<?php echo U('index');?>" style="color:#005ea7">上架时间</a>
                <a href="<?php echo U('time_sort');?>" style="background: #fff2f2;color:#e4393c">出版时间</a>
            </div>
            <ul class="book_list">
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>

                    <a href="<?php echo U('book',array('id'=>$vo['id']));?>">
                        <img src="<?php echo ($vo["image_l"]); ?>" >
                        <p><?php echo ($vo["title"]); ?></p>
                    </a>

                </li><?php endforeach; endif; else: echo "" ;endif; ?>

            </ul>
            <div class="page">

                <?php echo ($page); ?>
</div>
<!--光标显示-->
<!--<script type="text/javascript" charset="utf-8">
  $(function(){
    $('#test').focus(function(){
      $(this).val('');
      $(this).css('color', '#333');
    })
  })
</script>-->


</body>
</html>