<body class="pear-container pear-container">
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<style>
    .store_system li{
        padding: 0px;
    }
    .store_system li a{
        padding: 0 15px;
    }
    .layui-upload-drag .layui-icon {
        font-size: 50px;
        color: #1e9fff;
    }
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="edit_user_tab">
            <ul  class="layui-tab-title store_system">
                <li class="layui-this"><a href="{{:url('goods.setting/index')}}">基础配置</a></li>
            </ul>
            <!--内容区域-->
            <div class="layui-tab-content layui-store-tab-content">
                <div class="layui-tab-item layui-show layui-store-tab-show">
                    <div class="am-cf">
                        <div class="row">
                            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                                <div class="am-cf">
                                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                                        <div class="widget-body">
                                            <fieldset>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">权限配置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">商城开关</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="index[register]" value="10" title="开启" lay-filter="upload_type"  <?= $values['register'] === '10' ? 'checked' : '' ?>>
                                                        <input type="radio" name="index[register]" value="20" title="关闭" lay-filter="upload_type" <?= $values['register'] === '20' ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <div class="am-u-sm-10 am-u-sm-push-3 am-u-lg-offset-2 am-margin-top-lg">
                                                        <button type="submit" class="j-submit am-btn am-btn-secondary">确认提交</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
<script>
    layui.use(['form','colorpicker','upload'], function(){
        var $ = layui.$
            ,upload = layui.upload
            ,form = layui.form;

            colorpicker = layui.colorpicker;

        form.on('radio(customer_service_type)', function(data){
            console.log(data);
            var $alert_window = $('.alert_window')
                , $to_link = $('.to_link');
            if (data.value === '10') {
                $to_link.hide() && $alert_window.show();
            } else {
                $to_link.show() && $alert_window.hide();
            }
        });
        //表单赋值
        colorpicker.render({
            elem: '#test-form'
            ,color: '#1c97f5'
            ,done: function(color){
                $('#test-form-input').val(color);
            }
        });




    })
        $(function () {
            // 选择图片
            $('.upload-file').selectImages({
                name: 'store[invitation]',
            });
            // 选择图片
            $('.upload-file-icon').selectImages({
                name: 'store[ico_images]',
            });
            //LOGO
            $('.upload-file-logo').selectImages({
                name: 'store[login_logo_img]',
            });
            $('.upload-file-left-logo').selectImages({
                name: 'store[login_left_logo_img]',
            });
            //登录页面副标题
            $('.upload-file-logo-title').selectImages({
                name: 'store[login_logo_title_img]',
            });
            //首页广告图
            $('.upload-file-logo-ad').selectImages({
                name: 'store[login_logo_ad_img]',
            });
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
