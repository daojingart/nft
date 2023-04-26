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
</style>
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
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li class="layui-this"><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
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
                                                    <div class="widget-title am-fl">提现配置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">提现开关</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="withdrawal[status]" value="10" title="开启" lay-filter="upload_type"  {{if $values.status==10}}checked{{/if}} >
                                                        <input type="radio" name="withdrawal[status]" value="20" title="关闭" lay-filter="upload_type"  {{if $values.status==20}}checked{{/if}} >
                                                    </div>
                                                    <div class="am-u-sm-10"><small>关闭提现入口后前端提示</small></div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">关闭提示</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="withdrawal[close_text]" value="{{$values.close_text?=$values.close_text}}" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>用于提现关闭后，用户点击提现给用户的提示公告</small></div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">最低提现金额</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="withdrawal[minimum_withdrawal]" value="{{$values.minimum_withdrawal?=$values.minimum_withdrawal}}" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>用于单次最低可以提现的金额</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">手续费百分比
                                                    </label>
                                                    <div class="am-u-sm-10">
                                                        <div class="am-input-group">
                                                            <input style="border: 1px solid #ccc;" type="number" name="withdrawal[handling_fee]" class="am-form-field" min="0" value="{{$values.handling_fee?=$values.handling_fee}}" required>
                                                            <span class="am-input-group-label am-input-group-label__right">%</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">提现规则</label>
                                                    <div class="am-u-sm-10">
                                                        <textarea rows="5" name="withdrawal[rules]" placeholder="提现规则">{{$values.rules?=$values.rules}}</textarea>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <div class="am-u-sm-10 am-u-sm-push-3  am-margin-top-lg">
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
