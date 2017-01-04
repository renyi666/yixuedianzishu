<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>医学电子书</title>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?90c03209a96512a48c732354120dc956";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Home/css/module.css">
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Home/css/default_color.css">
    <link rel="stylesheet" href="/dzs/Public/Home/css/css.css" />
    <script src="/dzs/Public/Home/js/thinkbox/jquery.thinkbox.js"></script>

    <script type="text/javascript" src="/dzs/Public/Home/js/jquery.min.js" ></script>
    <script type="text/javascript">
        $(document).ready(function() {

            var a=$('.page .current').text();
            var end=$('.page .end').text();

            if(a=='1'){
           $('#page').css("width",'515px')
            }
            $('.tiaozhuan').click(function(){




                var url=$('.tiaozhuan').attr("href");
                var p=$('#z').val();

                if(a=='1'){


                    if(p=='1'){

                        $('.tiaozhuan').attr("href","/aaaa/index.php?s=/Home/Index/index.html");


                    }else {

                        url=url.substr(0,30);
                        url=url+"/p/"+p+".html";


                        $('.tiaozhuan').attr("href",url);


                    }

                }else {

                    url=url.substr(0,30);
                    url=url+"/p/"+p+".html";

                    $('.tiaozhuan').attr("href",url);

                }



            })

         var nk=   $('.nickname').text();
            var uid=   $('.uid').val();

            if(uid!=''&&uid!=null&&typeof (uid)!=undefined){

                $('.weixin').hide();
                $('.yonghu').show();
            }else{

                $('.weixin').show();
                $('.yonghu').hide();
            }
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



        .search-form{float: left; background-color: #f0f0f0;padding:5px; margin-top: 10px;}
        .search-text{height:25px;line-height: 25px;float: left;width: 350px;border: 0;outline: none;}
        .search-button{background-image: url(/dzs/Public/Home/images/search-button.png);width:29px;height:29px;float: left;border: 0;cursor: pointer}

.yonghu{display: none}

        a img{ border:none}

    </style>


</head>
<body>
<div class="head">
    <div class="head_cont">
        <a href="<?php echo U('index');?>" class="logo"></a>
        <div class="tishi" style="padding-left: 70px;padding-top: 15px;font-size: 18px;float: left;color: red;padding-right: 5px">建议用火狐浏览器登录本网站下载电子书</div>

        <div class="weixin">
            <a href="<?php echo U('wxLogin');?>">微信登录</a>

        </div>
        <div class="yonghu" >
            <input type="hidden" class="uid" value="<?php echo ($data["uid"]); ?>">

            <a href="<?php echo U('Index/score');?>" class="nickname"><?php echo ($data["nickname"]); ?></a>
            <a href="<?php echo U('Index/score');?>">积分中心</a>
            <a href="<?php echo U('out');?>">退出</a>

        </div>
    </div>
</div>


<div class="con">
    <!--<div class="ad">-->
        <!--<a href="#"><img src="/dzs/Public/Home/images/m1.jpg" /></a>-->
        <!--<a href="#"><img src="/dzs/Public/Home/images/m2.jpg" /></a>-->
        <!--<div class="vvv">-->
            <!--<script type="text/javascript">-->
                <!--/*468*60 创建于 2016/9/26*/-->
                <!--var cpro_id = "u2773551";-->
            <!--</script>-->
            <!--<script type="text/javascript" src="http://cpro.baidustatic.com/cpro/ui/c.js"></script>-->
       <!--</div>-->
    <!--</div>-->
    <!--<?php echo ($result4["pid"]); ?>-->
    <!--<?php echo ($result["id"]); ?>-->
    <div class="cont">
        <div class="left">
            <div class="list">
                <div class="line">
                    <a href="<?php echo U('index');?>"><span>全部</span></a>
                </div>
                <!--<?php if($result["id"] == $result4["pid"] ): ?>style="display: block"<?php endif; ?>-->
                <?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$result): $mod = ($i % 2 );++$i;?><dl class="system_log">

                    <dt onClick="changeImage()"><?php echo ($result["title"]); ?><img src="/dzs/Public/Home/images/off.png"></dt>
                    <dd <?php if($result["id"] == $result4["pid"] ): ?>style="display: block"<?php else: ?> style="display: none"<?php endif; ?>><a href="<?php echo U('index',array('id'=>$result['id']));?>"><?php echo ($result["title"]); ?></a></dd>
                    <!--<dd class="dd" value=$result{'id'}><?php echo ($result["title"]); ?></dd>-->
                    <?php if(is_array($result['item'])): $i = 0; $__LIST__ = $result['item'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><!--<dd class="first_dd"><a href="#">123</a></dd>-->
                    <!--<dd><a href="#">12345</a></dd>-->
                    <!--<dd><a href="#">5678</a></dd>-->
                    <dd <?php if($result["id"] == $result4["pid"] ): ?>style="display: block"<?php else: ?> style="display: none"<?php endif; ?>><a href="<?php echo U('index',array('id'=>$item['id']));?>"><?php echo ($item["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
                </dl>

                        <!--<dl class="system_log">-->

                            <!--<dt onClick="changeImage()"><?php echo ($result["title"]); ?><img src="/dzs/Public/Home/images/off.png"></dt>-->
                            <!--<dd style="display: none"><a href="<?php echo U('index',array('id'=>$result['id']));?>"><?php echo ($result["title"]); ?></a></dd>-->
                            <!--&lt;!&ndash;<dd class="dd" value=$result{'id'}><?php echo ($result["title"]); ?></dd>&ndash;&gt;-->
                            <!--<?php if(is_array($result['item'])): $i = 0; $__LIST__ = $result['item'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>-->
                                <!--&lt;!&ndash;<dd class="first_dd"><a href="#">123</a></dd>&ndash;&gt;-->
                                <!--&lt;!&ndash;<dd><a href="#">12345</a></dd>&ndash;&gt;-->
                                <!--&lt;!&ndash;<dd><a href="#">5678</a></dd>&ndash;&gt;-->
                                <!--<dd style="display: none"><a href="<?php echo U('index',array('id'=>$item['id']));?>"><?php echo ($item["title"]); ?></a></dd>-->
                            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                        <!--</dl>--><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>







            <!--<div class="list">-->
                <!--<ul class="yiji">-->
                    <!--<li class="all"><a href="<?php echo U('index');?>">全部</a></li>-->
                    <!--<?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$result): $mod = ($i % 2 );++$i;?>-->
                    <!--<li class="all_list"><a href="#" class="inactive"><?php echo ($result["title"]); ?></a>-->
                        <!--<ul style="display: none">-->
                            <!--<?php if(is_array($result['item'])): $i = 0; $__LIST__ = $result['item'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>-->
                            <!--<li><a href="<?php echo U('index',array('id'=>$item['id']));?>"><?php echo ($item["title"]); ?></a></li>-->
                            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                            <!--&lt;!&ndash;<li class="last"><a href="<?php echo U('new_4');?>">4月</a></li>&ndash;&gt;-->
                        <!--</ul>-->
                    <!--</li>-->
                    <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
                    <!--<li class="all_list"><a href="#" class="inactive">执业医师考试</a>-->
                        <!--<ul style="display: none">-->
                            <!--<li><a href="<?php echo U('kaoshi_5');?>" >5月</a></li>-->
                            <!--<li><a href="<?php echo U('kaoshi_4');?>">4月</a></li>-->
                            <!--<li class="last"><a href="<?php echo U('kaoshi_3');?>">3月</a></li>-->
                        <!--</ul>-->
                    <!--</li>-->
                    <!--<li class="all_list"><a href="#" class="inactive">内科</a>-->
                        <!--<ul style="display: none">-->
                            <!--<li><a href="<?php echo U('neike5');?>">5月</a></li>-->
                            <!--<li><a href="<?php echo U('neike4');?>">4月</a></li>-->
                            <!--<li class="last"><a href="<?php echo U('neike3');?>">3月</a></li>-->
                        <!--</ul>-->
                    <!--</li>-->
                <!--</ul>-->

            <!--<div class="Public_number">-->
                <!--<a href="<?php echo U('Index/earn_score');?>"><img src="/dzs/Public/Home/images/erweima.jpg"  style="width: 220px;height: 220px"></a>-->
            <!--</div>-->
        </div>
        <div class="right">
        <div class="bg-div">



                <form class="search-form" action="<?php echo U('Index/search');?>"  method="post">
                    <input type="text" class="search-text" name="title" placeholder="书名或ISBN码"/>
                    <input type="submit" class="search-button" value=""/>
                </form>
            </div>





            <div class="sort">

                <span>排序：</span>
                <a href="<?php echo U('Index/index',array('id'=>$id['id']));?>" style="background: #fff2f2;color:#e4393c">上架时间</a>
                <a href="<?php echo U('Index/time_sort',array('id'=>$id['id']));?>" style="color:#005ea7">出版时间</a>
            </div>
            <ul class="book_list">
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>

                    <a href="<?php echo U('book',array('id'=>$vo['id']));?>"  target="_blank">
                        <img src="<?php echo ($vo["image_l"]); ?>" >
                        <p><?php echo ($vo["title"]); ?></p>
                    </a>

                </li><?php endforeach; endif; else: echo "" ;endif; ?>

            </ul>
            <div class="page">
                <?php echo ($_page); ?>
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

            <script type="text/javascript" src="js/jquery.js"></script>
            <script type="text/javascript">
                $(function(){
//                    $(".list dd").hide();
                    $(".list dt").click(function(){
                        $(".list dt").css({"background-color":"#209810;"})
//		$(this).css({"background-color": "#317eb4"});
                        $(this).parent().find('dd').removeClass("menu_chioce");
                        $(".list dt img").attr("src","/dzs/Public/Home/images/off.png");
                        $(this).parent().find('img').attr("src","/dzs/Public/Home/images/on.png");
                        $(".menu_chioce").slideUp();
                        $(this).parent().find('dd').slideToggle();
                        $(this).parent().find('dd').addClass("menu_chioce");
                    });
                })
            </script>

</body>
</html>