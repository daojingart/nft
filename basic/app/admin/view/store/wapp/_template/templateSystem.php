<script  id="template_system" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>小程序订阅消息配置,微信菜单左侧菜单->功能专区里面的订阅消息-->没有开通先点击开通</p>
                    <p>注意:2>小程序类目为商家自营 > 服装/鞋/箱包，类目订阅消息为一次性通知,用户订阅既通知</p>
                </blockquote>
                <div class="am-cf">
                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                        <div class="widget-body">
                            <fieldset>
                                <div class="widget-head am-cf">
                                    <div class="widget-title am-fl">模板消息通知</div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        订单支付成功通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_order_pay]"
                                               value="{{wxapp_template.wxapp_order_pay}}" required>
                                        <small>订单支付成功通知《订单编号、支付时间、商品名称、支付金额、备注》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        订单发货通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_order_ship]"
                                               value="{{wxapp_template.wxapp_order_ship}}" required>
                                        <small>订单发货通知《订单号、商品名称、快递公司、快递单号、备注》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        收益到账通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_income_message]"
                                               value="{{wxapp_template.wxapp_income_message}}" required>
                                        <small>收益到账通知《下单人、商品名称、下单时间、获得佣金、备注》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        提现成功通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_arrive_message]"
                                               value="{{wxapp_template.wxapp_arrive_message}}" required>
                                        <small>提现成功通知《提现金额、手续费、打款方式、温馨提示》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        拼团进度通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_team_success_message]"
                                               value="{{wxapp_template.wxapp_team_success_message}}" required>
                                        <small>拼团进度通知《拼团商品、拼团状态、支付金额、温馨提示》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        积分变更提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_score_message]"
                                               value="{{wxapp_template.wxapp_score_message}}" required>
                                        <small>积分变更提醒《变更数量、积分余额、商品名称、订单编号》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        审核结果通知
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="wxapp_template[wxapp_merchant_review]"
                                               value="{{wxapp_template.wxapp_merchant_review}}" required>
                                        <small>审核结果通知《审核对象、审核结果、审核时间、审核说明、备注》</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3 am-u-lg-offset-3 am-margin-top-lg">
                                        <button type="submit" class="j-submit am-btn am-btn-secondary">
                                            确认提交
                                        </button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        $(function () {
            /**
             * 表单验证提交
             * @type {*}
             */
            $('#my-form').superForm({
                url:"template"
            });
        });
    </script>
</script>
