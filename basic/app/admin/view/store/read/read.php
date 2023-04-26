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
<!--<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">-->
<script src="/assets/admin/amazeui.min.js"></script>
<!--<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>-->
<!--<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>-->
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
                                                    <div class="widget-title am-fl">须知配置</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">兑换说明</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[exchange]" type="text/plain">{{$values.exchange?=$values.exchange}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">合成须知</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[synthetic]" type="text/plain">{{$values.synthetic?=$values.synthetic}}</textarea>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">购买须知</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[buy]" type="text/plain">{{$values.buy?=$values.buy}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">挂售须知</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[hangSell]" type="text/plain">{{$values.hangSell?=$values.hangSell}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">转赠须知</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[donation]" type="text/plain">{{$values.donation?=$values.donation}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">温馨提示</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[tips]" type="text/plain">{{$values.tips?=$values.tips}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">实物商城须知</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[shop_tips]" type="text/plain">{{$values.shop_tips?=$values.shop_tips}}</textarea>
                                                    </div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">空投规则</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">空投怎么获得</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[drop_obtain]" type="text/plain">{{$values.drop_obtain?=$values.drop_obtain}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">空投会在什么时候下发</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[drop_shelves]" type="text/plain">{{$values.drop_shelves?=$values.drop_shelves}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">空投发放的顺序是什么</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea style="height: 200px" name="read[drop_order]" type="text/plain">{{$values.drop_order?=$values.drop_order}}</textarea>
                                                    </div>
                                                </div>
<!--                                                <div class="widget-head am-cf">-->
<!--                                                    <div class="widget-title am-fl">客服须知(为什么我的实名认证不通过)</div>-->
<!--                                                </div>-->
<!--                                                <div class="am-form-group">-->
<!--                                                    <label class="am-u-sm-2 am-form-label form-require">客服须知</label>-->
<!--                                                    <div class="am-u-sm-10">-->
<!--                                                        <textarea id="container6" style="height: 200px" name="read[service]" type="text/plain">{{$values.service?=$values.service}}</textarea>-->
<!--                                                    </div>-->
<!--                                                </div>-->


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
        // UM.getEditor('container');
        // UM.getEditor('container2');
        // UM.getEditor('container3');
        // UM.getEditor('container4');
        // UM.getEditor('container5');
        // UM.getEditor('container6');
        // UM.getEditor('container7');
        // UM.getEditor('container8');
        // UM.getEditor('container9');
        // UM.getEditor('container10');
        // UM.getEditor('container11');

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
