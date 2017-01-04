<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|OneThink管理平台</title>
    <link href="/dzs/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link href="/dzs/Public/static/zui/css/zui.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/dzs/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/dzs/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/dzs/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/dzs/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/dzs/Public/Admin/js/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="/dzs/Public/static/zui/js/zui.min.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
	<script type="text/javascript" src="/dzs/Public/static/uploadify/jquery.uploadify.min.js"></script>
	<div class="main-title cf">
		<h2>
			编辑图书
		</h2>
	</div>
	<!-- 标签页导航 -->
<div class="tab-wrap">
	<div class="tab-content">
	<!-- 表单 -->
	<form id="form" action="<?php echo U('edit');?>" method="post" class="form-horizontal">


		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">ISBN<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="isbn" id="isbn" value="<?php echo ($book["isbn"]); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">书名<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="title" value="<?php echo ($book["title"]); ?>">
			</div>
		</div>

		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">图书封面<span class="check-tips"></span></label>
			<div class="controls">
				<input type="file" id="upload_picture">
				<input type="hidden" name="image_l" id="cover_id" value="<?php echo ($book["image_l"]); ?>"/>
				<div class="upload-img-box">
					<?php if(!empty($book["image_l"])): ?><div class="upload-pre-item"><img src="<?php echo (get_image_url($book["image_l"])); ?>"/></div><?php endif; ?>
				</div>
			</div>
			<script type="text/javascript">
				//上传图片
				/* 初始化上传插件 */
				$("#upload_picture").uploadify({
					"height"          : 30,
					"swf"             : "/dzs/Public/static/uploadify/uploadify.swf",
					"fileObjName"     : "download",
					"buttonText"      : "上传图片",
					"uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
					"width"           : 120,
					'removeTimeout'	  : 1,
					'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
					"onUploadSuccess" : uploadPicture,
					'onFallback' : function() {
						alert('未检测到兼容版本的Flash.');
					}
				});
				function uploadPicture(file, data){
					var data = $.parseJSON(data);
					var src = '';
					if(data.status){
						$("#cover_id").val(data.id);
						src = data.url || '/dzs' + data.path
						$("#cover_id").parent().find('.upload-img-box').html(
								'<div class="upload-pre-item"><img src="' + src + '"/></div>'
						);
					} else {
						updateAlert(data.info);
						setTimeout(function(){
							$('#top-alert').find('button').click();
							$(that).removeClass('disabled').prop('disabled',false);
						},1500);
					}
				}
			</script>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">出版社<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="publisher" value="<?php echo ($book["publisher"]); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">出版时间<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text time input-large" name="pubdate" value="<?php echo (time_format($book["pubdate"],'Y-m-d')); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">版次<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="pubversion" value="<?php echo ($book["pubversion"]); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">页数<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="pages" value="<?php echo ($book["pages"]); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">价格<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="price" value="<?php echo ($book["price"]); ?>">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">图书网址<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="sale_url" value="<?php echo ($book["sale_url"]); ?>" placeholder="京东当当等的网址">
			</div>
		</div>

		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">下载地址1<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="download_url1" value="<?php echo ($book["download_url1"]); ?>" placeholder="">
			</div>
		</div>
		<div class="form-item cf">
			<label class="item-label col-md-2 col-lg-2">下载地址2<span class="check-tips"></span></label>
			<div class="controls col-md-10 col-lg-10">
				<input type="text" class="text input-large" name="download_url2" value="<?php echo ($book["download_url2"]); ?>" placeholder="">
			</div>
		</div>

		<div class="form-item cf">
			<label class="item-label col-md-2">分类<span class="check-tips"></span></label>

			<select data-placeholder="选择分类" class="chosen-select form-control col-md-10" tabindex="-1" name="category[]" multiple="">
				<?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if(($vo["selected"]) == "selected"): ?>selected="selected"<?php endif; ?>><?php echo ($vo["title"]); ?></option>
					<?php if(!empty($vo['_'])): if(is_array($vo['_'])): $i = 0; $__LIST__ = $vo['_'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$id): $mod = ($i % 2 );++$i;?><option value="<?php echo ($id['id']); ?>" <?php if(($id["selected"]) == "selected"): ?>selected="selected"<?php endif; ?>>
								<?php if($id["pid"] > 0): ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?>
								<?php echo ($id['title']); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</div>


		<div class="form-item cf">
			<button class="btn submit-btn ajax-post " id="submit" type="submit" target-form="form-horizontal">确 定</button>
			<a class="btn btn-return" href="<?php echo (cookie('__forward__')); ?>">返 回</a>

			<input type="hidden" name="id" value="<?php echo ((isset($book["id"]) && ($book["id"] !== ""))?($book["id"]):''); ?>"/>
		</div>
	</form>
	</div>
</div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.onethink.cn" target="_blank">OneThink</a>管理平台</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/dzs", //当前网站地址
            "APP"    : "/dzs/admin.php?s=", //当前项目地址
            "PUBLIC" : "/dzs/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/dzs/Public/static/think.js"></script>
    <script type="text/javascript" src="/dzs/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<link href="/dzs/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
	<link href="/dzs/Public/static/zui/lib/chosen/chosen.css" rel="stylesheet" type="text/css">
<?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/dzs/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
<link href="/dzs/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/dzs/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/dzs/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
	<script type="text/javascript" src="/dzs/Public/static/zui/lib/chosen/chosen.js"></script>
<script type="text/javascript">



$('#submit').click(function(){
	$('#form').submit();
});

$(function(){
	highlight_subnav('<?php echo U('Book/index');?>');
    $('.date').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
	$('#isbn').blur(function(){
		var url = "<?php echo U('Book/getBookByIsbn');?>";
		var isbn = $(this).val();
		$.post(url,{'isbn':isbn}).success(function(data){
			if(data!=null){
				for(var k in data){
					$('input[name='+k+']').val(data[k]);
					if(k=='image_l'){
						$('.upload-img-box').html('<div class="upload-pre-item"><img src="'+data[k]+'"/></div>');
					}

				}
			}
		});
	});
	$('.chosen-select').chosen({
		no_results_text: '没有找到',    // 当检索时没有找到匹配项时显示的提示文本
		disable_search_threshold: 10, // 10 个以下的选择项则不显示检索框
		search_contains: true         // 从任意位置开始检索
	});


});
</script>

</body>
</html>