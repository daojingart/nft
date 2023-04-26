<style>
    .store_system li{
        padding: 0px;
    }
    .store_system li a{
        padding: 0 15px;
    }
</style>
<body class="pear-container pear-container">
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="edit_user_tab">
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
                                                    <div class="widget-title am-fl">协议配置</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">连连支付服务协议-用户协议（ACS）</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea id="container" name="payprotocol[user]" type="text/plain">{{$values.user?=$values.user}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">《连连支付隐私权政策》</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea id="container2" name="payprotocol[privacy]" type="text/plain">{{$values.privacy?=$values.privacy}}</textarea>
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
    layui.use(['form','colorpicker'], function(){
        var $ = layui.$
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
        UM.getEditor('container');
        UM.getEditor('container2');
        UM.getEditor('container3');

        // 选择图片
        $('.logo_img_link').selectImages({
            name: 'store[logo_img_link]',
        });
        $('.public_img_url').selectImages({
            name: 'store[guide][public_img_url]',
        });

        $('.customer_service_img').selectImages({
            name: 'store[guide][customer_service_img]',
        });
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
