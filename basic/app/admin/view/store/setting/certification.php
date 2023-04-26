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
            <ul class="layui-tab-title store_system">
                <li ><a href="{{:url('store.setting/store')}}">基础配置</a></li>
                {{if $store['user']['user_rule'] =='*'}}
                <li><a href="{{:url('store.setting/storage')}}" >图片存储</a></li>
                <li><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
                {{/if}}
                <li><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li class="layui-this"><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
                <li><a href="{{:url('store.setting/service')}}">服务费配置</a></li>
                <li><a href="{{:url('store.setting/drop')}}">空投配置</a></li>
                {{if $store['user']['user_rule'] =='*'}}
                <li><a href="{{:url('store.setting/blockchain')}}">上链配置</a></li>
                <li><a href="{{:url('store.setting/resource')}}">视频配置</a></li>
                <li><a href="{{:url('store.setting/behaviorcode')}}">行为验证码</a></li>
                {{/if}}
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
                                                    <div class="widget-title am-fl">实名认证配置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">实名注册认证模式</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="certification[status]" value="10" title="手动审核" lay-filter="upload_type"  {{if $values.status==10}}checked{{/if}}  >
                                                        <input type="radio" name="certification[status]" value="20" title="二要素认证" lay-filter="upload_type"  {{if $values.status==20}}checked{{/if}} >
                                                        <input type="radio" name="certification[status]" value="40" title="三要素认证" lay-filter="upload_type"  {{if $values.status==40}}checked{{/if}} >
                                                        <input type="radio" name="certification[status]" value="30" title="四要素认证" lay-filter="upload_type"  {{if $values.status==30}}checked{{/if}} >
                                                    </div>
                                                    <div class="am-u-sm-10"><small>选择不同的要素认证;建议选择四要素认证,真实性更高</small></div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">实名认证配置(腾讯 二要素)</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">SecretId</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="certification[AppKey]" value="{{$values.AppKey?=$values.AppKey}}" required>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">SecretKey</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="certification[AppSecret]" value="{{$values.AppSecret?=$values.AppSecret}}" required>
                                                    </div>
                                                </div>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">千帆市场实名认证(三要素) <a target="_blank" href="https://market.cloud.tencent.com/products/17683">购买地址</a></div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">SecretId</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="certification[qf_AppKey]" value="{{$values.qf_AppKey?=$values.qf_AppKey}}" required>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">SecretKey</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="certification[qf_AppSecret]" value="{{$values.qf_AppSecret?=$values.qf_AppSecret}}" required>
                                                    </div>
                                                </div>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">阿里云市场(四要素) <a target="_blank" href="https://market.aliyun.com/products/57000002/cmapi00035267.html#sku=yuncode2926700001">购买地址</a></div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">AppCode</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="certification[ali_AppCode]" value="{{$values.ali_AppCode?=$values.ali_AppCode}}" required>
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
