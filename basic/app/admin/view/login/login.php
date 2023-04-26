
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible" aria-keyshortcuts="19970424000772991">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>数字藏品系统</title>
    <link rel="stylesheet" href="/assets/admin/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/assets/login/loginstyle.css" />
    <meta name="author" content="河南八六互联信息技术有限公司" />
    <link href="{{$setting['values']['ico_images']}}" rel="shortcut icon">
</head>
<style>
    .submit:active {
        transform: scale(0.98);
        box-shadow: 3px 2px 22px 1px rgba(0, 0, 0, 0.24);
    }
</style>
<body>
    <div class="wrapcon">
        <div class="bagimg">
            <div class="content">
                <div class="layui-carousel" id="test1">
                    <div carousel-item>
                        <!--里面可以加图片，文字等-->
                        <div><img src="/assets/login/images/1.png" /></div>
                        <div><img src="/assets/login/images/2.png" /></div>
                        <div><img src="/assets/login/images/3.png" /></div>
                    </div>
                </div>
                <form id="login-form" class="login-form">
                    <div style="background-color: #313437" class="login_con">
                    <img src="{{$setting['values']['login_logo_img']}}">
                    <p class="desc" style="color: #fffaea">{{$setting['values']['system_name']}}</p>
                    <div class="loginForm">
                        <div class="form_every">
                            <img src="/assets/login/images/5.png">
                            <input  style="background-color: #313437;color: #ffff;" type="text" placeholder="请输入登录账号" name="User[user_name]"  required>
                        </div>
                        <div class="form_every">
                            <img src="/assets/login/images/12.png">
                            <input style="background-color: #313437;color: #ffff;" type="password" name="User[password]" placeholder="请输入登录密码">
                        </div>
                        <div class="code">
                            <div id="slideBar"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button  class="submit"  style="color: #3F3F3F" id="login_btn" type="submit">
                            登录
                        </button>
                    </div>
                </div>
                </form>
            </div>
            <div class="footer">
                <div>{{$setting['values']['copyright']}},构建企业级NFT数字藏品发行系统<span class="dot"></span> 技术支持电话:{{$setting['values']['contact_number']}}</div>
                <div class="ying-records">Copyright © 2015-2021 {{$setting['values']['copyright']}} All Rights Reserved. {{$setting['values']['icp_number']}}</div>
            </div>
        </div>
    </div>
</body>
</html>
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script type="text/javascript">
    var lock = false;
    $(function () {
        layui.config({
            base: '/assets/admin/modules/sliderVerify/'
        }).use(['sliderVerify', 'jquery', 'form'], function() {
            sliderVerify = layui.sliderVerify,
                form = layui.form;
            slider = sliderVerify.render({
                elem: '#slideBar',
                isAutoVerify:true,//关闭自动验证
                bg : 'layui-bg-red_blue',//自定义背景样式名
                text : '请拖动滑动块验证',
                onOk: function(){ //当验证通过回调
                    lock = true;
                }
            })
        })
        var $form = $('#login-form');
        $form.submit(function () {
            if(lock == false){
                var msg = '请先拖动验证码验证奥';
                layer.alert(msg, {time: 1500, anim: 6}); return false;;
            }
            var $btn_submit = $('#btn-submit');
            $btn_submit.attr("disabled", true);
            $form.ajaxSubmit({
                type: "post",
                dataType: "json",
                success: function (result) {
                    $btn_submit.attr('disabled', false);
                    if (result.code === 1) {
                        lock = true;
                        layer.msg(result.msg, {time: 1500, anim: 1}, function () {
                            window.location = result.url;
                        });
                        return true;
                    }
                    lock = false;
                    layer.msg(result.msg, {time: 2500, anim: 6},function () {
                        window.location.reload()
                    });
                }
            });
            return false;
        });
    });
</script>
<script>
    //第一个轮播图
    layui.use('carousel', function() {
        var carousel = layui.carousel;
        //建造实例化
        carousel.render({
            elem: '#test1',
            width: '780px', //设置背景容器的宽度
            arrow: 'none', //始终显示箭头,不会消失
            //anim: 'updown' //切换动画方式:anim
             //indicator:'outside',
                indicator: 'outside'	//这个属性：小圆点在外面	
        });
    });
</script>