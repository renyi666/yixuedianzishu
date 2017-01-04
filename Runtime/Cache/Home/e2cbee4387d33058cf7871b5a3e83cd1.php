<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($list['0']["title"]); ?>_医学电子书</title>
    <link rel="stylesheet" href="/dzs/Public/Home/css/css.css" />
    <script type="text/javascript" src="/dzs/Public/Home/js/jquery.min.js" ></script>
    <script type="text/javascript">
        $(document).ready(function() {


            var uid= $('.uid').val();
           var score= $('.score').text();
            var buy_result=$('.buy_result').val();
            var rep_result=$('.rep_result').val();
            if(uid!=''&&uid!=null&&typeof (uid)!=undefined){

                if(buy_result==1){

                    if(rep_result==1){
                        $('.weixin').hide();
                        $('.wx_login').hide();
                        $('.yonghu').show();
                        $('.download_url').show();
                        $('.download_jubao').hide();
                        $('.thanks').show();

                    }else{

                        $('.weixin').hide();
                        $('.wx_login').hide();
                        $('.yonghu').show();
                        $('.download_url').show();
                        $('.download_jubao').show();
                    }

                }else{
              if(score>=1){

                  $('.weixin').hide();
                   $('.yonghu').show();
                  $('.buy').show();
                   $('.wx_login').hide();


                  //积分购买地址的ajax请求
                  $('.buy_book').click(function () {
                      var user_id = $('.uid').val();
                      var book_id = $('.id').val();
                      var book_name=$('.title').text();
                      $.ajax(
                              {
                                  url: '<?php echo U('/Home/Index/buy_book');?>',
                                  data: {user_id: user_id, book_id: book_id,book_name:book_name},
                                  dataType: 'json',
                                  method: 'post',
                                  success: function (data) {
                                      if (data = "ok") {
                                          $('.download_url').show();
                                          $('.download_jubao').show();
                                          $('.buy').hide();
                                      }
                                      else {
                                          alert("购买失败，请确认积分是否足够");
                                      }


                                  }


                              }
                      )


                  })
                  ;

              }else{
                  $('.weixin').hide();
                  $('.yonghu').show();
                  $('.earn_score').show();
                  $('.wx_login').hide();

              }
                }

            }else{
                $('.weixin').show();
                $('.yonghu').hide();
            }

           //举报失效的ajax请求
            $('.report').click(function () {
                var user_id = $('.uid').val();
                var book_id = $('.id').val();
                $.ajax(
                        {
                            url: '<?php echo U('/Home/Index/report');?>',
                            data: {user_id: user_id, book_id: book_id},
                            dataType: 'json',
                            method: 'post',
                            success: function (data) {
                                if ($data = "ok") {
                                    $('.download_url').show();
                                    $('.download_jubao').hide();
                                    $('.thanks').show();
                                }
                                else {
                                    alert("cc");
                                }


                            }


                        }
                )


            })
            ;


        });


    </script>
    <style type="text/css">



        .search-form{float: left; background-color: #f0f0f0;padding:5px; margin-top: 10px;}
        .search-text{height:25px;line-height: 25px;float: left;width: 350px;border: 0;outline: none;}
        .search-button{background-image: url(/dzs/Public/Home/images/search-button.png);width:29px;height:29px;float: left;border: 0}

        .yonghu{display: none}
        .earn_score{display: none}
        .buy{display: none}
        .download_url{display: none}

        .download_jubao{display: none}
        .thanks{display: none}
        a img{ border:none}

    </style>

    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?90c03209a96512a48c732354120dc956";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body>
<div class="head">
    <div class="head_cont">
        <a href="<?php echo U('index');?>" class="logo"></a>
        <div class="tishi" style="padding-left: 70px;padding-top: 15px;font-size: 18px;float: left;color: red;padding-right: 5px">建议用火狐浏览器登录本网站下载电子书</div>        <div class="weixin">
            <a href="<?php echo U('wxLogin');?>">微信登录</a>
        </div>
        <div class="yonghu">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data): $mod = ($i % 2 );++$i;?><input type="hidden" class="uid" value="<?php echo ($data["uid"]); ?>">
                <a href="<?php echo U('Index/score');?>" class="nickname"><?php echo ($data["nickname"]); ?></a>
                <a href="<?php echo U('Index/score');?>">积分中心</a>
                <a href="<?php echo U('out');?>">退出</a><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo ($buy_result['res']); ?>"class="buy_result">
<input type="hidden" value="<?php echo ($rep_result['res']); ?>"class="rep_result">
<div class="con">
    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="second_cont">
        <div class="home_text">
            <a href="<?php echo U('index');?>" class="home">首页</a>&nbsp;<span><img src="/dzs/Public/Home/images/right1.jpg" >&nbsp;</span><a href="" class="home_next"><?php echo ($list["title"]); ?></a>
        </div>
        <div class="second_left">
            <div class="tb-booth">
                <div style="float:left"><a href="<?php echo ($list["image_l"]); ?>"><div class="tb-booth_img"><img src="<?php echo ($list["image_l"]); ?>"  style="width: 272px;height: 339px"/></div></a></div>
                <div class="tb-wrap">
                    <input type="hidden" class="id" value="<?php echo ($list["id"]); ?>">
                    <h2 class="title"><?php echo ($list["title"]); ?>_医学电子书</h2>
                    <p><span>ISBN：</span><?php echo ($list["isbn"]); ?></p>
                    <p><span>出版社： </span><?php echo ($list["publisher"]); ?></p>
                    <p><span>出版时间：</span><?php echo ($list["pubdate"]); ?></p>
                    <p><span>版次：</span><?php echo ($list["pubversion"]); ?></p>

                    <p><span>所属分类：</span>
                    <?php if(is_array($result_cate)): $i = 0; $__LIST__ = $result_cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$result_cate): $mod = ($i % 2 );++$i;?>| <?php echo ($result_cate["title"]); endforeach; endif; else: echo "" ;endif; ?>
                    </p>

                </div>
            </div>
            <div class="download" id="maoji">
                <h4>电子书下载地址</h4>
                <div class="download_content">
                    <p class="wx_login"><span>您还未登录，不能查看。本网站不用注册，可以直接用微信登录查看。</span><a href="<?php echo U('wxLogin');?>">用微信登录</a></p>
                    <?php if(is_array($score_result)): $i = 0; $__LIST__ = $score_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$score_result): $mod = ($i % 2 );++$i;?><p class="score" hidden><?php echo ($score_result["score"]); ?></p><?php endforeach; endif; else: echo "" ;endif; ?>
                    <p class="earn_score"><span>电子书1积分/本，您的余额不足，点击</span><a href="<?php echo U('Index/earn_score');?>">快速赚积分</a></p>

                    <p class="buy"><span>电子书1积分/本，点击查看将消耗1积分。</span><a href="#lianjie" class="buy_book">查看</a></p>
                    <p class="download_url" id="lianjie"><span>链接：</span><span><a href="<?php echo ($list["download_url1"]); ?>" target="_blank"><?php echo ($list["download_url1"]); ?></a></span><span>密码：<?php echo ($list["download_url1_mima"]); ?></span></p>
<br>
                    <br>
                    <p class="download_jubao"><span>如果下载地址失效，请点击 <button class="report">举报已经失效</button>我们会在一个工作日改进。</span></p>
                    <p class="thanks"><span>感谢举报，我们会在一个工作日改进，请一个工作日后再查看此页。</span></p>

                </div>
            </div>
            <!--<div class="download1">-->
                <!--<p><span>读书笔记</span></p>-->
                <!--<div></div>-->
                <!--<p><span>内容简介</span></p>-->
                <!--<div>-->
                    <!--简介-->
                <!--</div>-->
            <!--</div>-->
        </div>
        <div class="second_right">
            <div class="tishi"  style="color: red;font-size: 20px;margin-left: 18px;">关注公众号，每天领积分</div>
            <div class="Public_number">
                <a href="<?php echo U('Index/earn_score');?>"><img src="/dzs/Public/Home/images/erweima1.jpg"></a>
            </div>
            <!--<div class="Public_number">-->
                <!--<a href=""><img src="/dzs/Public/Home/images/m5.jpg"></a>-->
            <!--</div>-->
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
</body>


    </html>