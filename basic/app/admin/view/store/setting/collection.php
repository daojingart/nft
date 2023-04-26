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
                <li class="layui-this"><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
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
                                                    <div class="widget-title am-fl">转增时间限制(分钟)</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">转增时间</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="collection[time]" value="<?= isset($values['time']) ? $values['time'] : '' ?>" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>购买生成藏品后多少分钟后才可以进行转增 (分钟)</small></div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">寄售时间</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="collection[sale_time]" value="<?= isset($values['sale_time']) ? $values['sale_time'] : '' ?>" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>收到藏品后多久之后可以进行挂售 (分钟)</small></div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">最低寄售金额</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="collection[consignment_percentage]" value="<?= isset($values['consignment_percentage']) ? $values['consignment_percentage'] : '' ?>" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>寄售市场寄售藏品设定的价格不允许低于这个发行价的百分比；最低寄售金额：发行价+发行价*此处百分比</small></div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">最高寄售金额</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="collection[consignment_high_percentage]" value="<?= isset($values['consignment_high_percentage']) ? $values['consignment_high_percentage'] : '' ?>" required>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>寄售市场寄售藏品设定的价格不允许高于设置的最高寄售价格,所有藏品统一最高限价</small></div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">挂售权限</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">挂售权限</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="collection[permissions]" value="10" title="开启" lay-filter="upload_type"  {{if $values.permissions==10}}checked{{/if}} >
                                                        <input type="radio" name="collection[permissions]" value="20" title="关闭" lay-filter="upload_type"  {{if $values.permissions==20}}checked{{/if}} >
                                                    </div>
                                                    <div class="am-u-sm-10"><small>关闭后,前端将不展示寄售市场、我的藏品模块将不在开启寄售</small> </div>
                                                </div>

<!--                                                <div class="widget-head am-cf">-->
<!--                                                    <div class="widget-title am-fl">官方回收</div>-->
<!--                                                </div>-->
<!--                                                <div class="am-form-group layui-form">-->
<!--                                                    <label class="am-u-sm-2 am-form-label form-require">回收按钮</label>-->
<!--                                                    <div class="am-u-sm-10 layui-form">-->
<!--                                                        <input type="radio" name="collection[reclaim_open]" value="10" title="开启" lay-filter="upload_type"  {{if $values.reclaim_open==10}}checked{{/if}} >-->
<!--                                                        <input type="radio" name="collection[reclaim_open]" value="20" title="关闭" lay-filter="upload_type"  {{if $values.reclaim_open==20}}checked{{/if}} >-->
<!--                                                    </div>-->
<!--                                                    <div class="am-u-sm-10"><small>关闭后,二级市场将不再展示官方回收按钮</small> </div>-->
<!--                                                </div>-->

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">任务权限</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">任务权限</label>
                                                    <div class="am-u-sm-9 am-u-end layui-form">
                                                        <input type="radio" name="collection[repo]" value="10" title="开启" lay-filter="upload_type"  {{if $values.repo==10}}checked{{/if}} >
                                                        <input type="radio" name="collection[repo]" value="20" title="关闭" lay-filter="upload_type"  {{if $values.repo==20}}checked{{/if}} >
                                                    </div>
                                                    <div class="am-u-sm-10"><small>任务关闭,则关闭积分体系,积分任务奖励将不再进行发放积分</small> </div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">赠送额外购买次数</label>
                                                    <div class="am-u-sm-9 am-u-end layui-form">
                                                        {{if isset($values.additional_open)}}
                                                        <input type="radio" name="collection[additional_open]" value="10" title="开启"   {{if $values.additional_open==10}}checked{{/if}} >
                                                        <input type="radio" name="collection[additional_open]" value="20" title="关闭" {{if $values.additional_open==20}}checked{{/if}} >
                                                        {{else /}}
                                                        <input type="radio" name="collection[additional_open]" value="10" title="开启">
                                                        <input type="radio" name="collection[additional_open]" value="20" title="关闭" checked>
                                                        {{/if}}
                                                    </div>
                                                    <div class="am-u-sm-10"><small>关闭后邀请人下级用户消费上级则不再增加购买次数</small> </div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">优先购提前时间(秒)</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">提前秒数</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="collection[precedence]" value="<?= isset($values['precedence']) ? $values['precedence'] : '' ?>" required>
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
