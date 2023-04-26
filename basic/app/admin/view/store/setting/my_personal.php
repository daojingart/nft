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
                                                    <div class="widget-title am-fl">其他按钮开关</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">商城订单</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[shop_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.shop_status)}} {{if $values.shop_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[shop_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.shop_status)}} {{if $values.shop_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">合成藏品</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[synthesis_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.synthesis_status)}} {{if $values.synthesis_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[synthesis_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.synthesis_status)}} {{if $values.synthesis_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">藏品兑换</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[exchange_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.exchange_status)}} {{if $values.exchange_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[exchange_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.exchange_status)}} {{if $values.exchange_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>



                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">排行榜</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[ranking_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.ranking_status)}} {{if $values.ranking_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[ranking_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.ranking_status)}} {{if $values.ranking_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">藏品记录</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[log_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.log_status)}} {{if $values.log_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[log_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.log_status)}} {{if $values.log_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">我的团队</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[team_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.team_status)}} {{if $values.team_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[team_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.team_status)}} {{if $values.team_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">每日签到</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[sign_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.sign_status)}} {{if $values.sign_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[sign_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.sign_status)}} {{if $values.sign_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">邀请有礼</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[invitation_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.invitation_status)}} {{if $values.invitation_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[invitation_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.invitation_status)}} {{if $values.invitation_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">操作密码</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[pwd_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.pwd_status)}} {{if $values.pwd_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[pwd_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.pwd_status)}} {{if $values.pwd_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">关于我们</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[we_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.we_status)}} {{if $values.we_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[we_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.we_status)}} {{if $values.we_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">空投卷</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[airdrop_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.airdrop_status)}} {{if $values.airdrop_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[airdrop_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.airdrop_status)}} {{if $values.airdrop_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>


                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">挂售开关</div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">正在挂售</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[conduct_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.conduct_status)}} {{if $values.conduct_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[conduct_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.conduct_status)}} {{if $values.conduct_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">已售出</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[sold_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.sold_status)}} {{if $values.sold_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[sold_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.sold_status)}} {{if $values.sold_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">申购开关</div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">申购订单</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[shen_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.shen_status)}} {{if $values.shen_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[shen_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.shen_status)}} {{if $values.shen_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">二级市场</div>
                                                </div>


                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">官方回收</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[recovery_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.recovery_status)}} {{if $values.recovery_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[recovery_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.recovery_status)}} {{if $values.recovery_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">排行榜子开关</div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">邀请排行</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[invitation_ranking_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.invitation_ranking_status)}} {{if $values.invitation_ranking_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[invitation_ranking_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.invitation_ranking_status)}} {{if $values.invitation_ranking_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">消费排榜</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[consumption_ranking_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.consumption_ranking_status)}} {{if $values.consumption_ranking_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[consumption_ranking_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.consumption_ranking_status)}} {{if $values.consumption_ranking_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
                                                </div>

                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">持仓排行</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="my_personal[warehouse_ranking_status]" value="10" title="显示" lay-filter="upload_type"  {{if isset($values.warehouse_ranking_status)}} {{if $values.warehouse_ranking_status==10}}checked{{/if}} {{/if}} >
                                                        <input type="radio" name="my_personal[warehouse_ranking_status]" value="20" title="隐藏" lay-filter="upload_type"  {{if isset($values.warehouse_ranking_status)}} {{if $values.warehouse_ranking_status==20}}checked{{/if}} {{/if}}>
                                                    </div>
                                                    <div class="am-u-sm-10"><small>````</small></div>
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
