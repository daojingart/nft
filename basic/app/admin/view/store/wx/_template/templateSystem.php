<script  id="template_system" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>微信模板消息配置,微信菜单左侧菜单点击模板消息</p>
                    <p>注意:2>没有模板消息的用户,需要点击添加功能插件->开通模板消息|模板消息类目为消费品/消费品</p>
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
                                        订单支付提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[order_pay]"
                                               value="{{wx_template.order_pay}}" required>
                                        <small>搜索"订单支付成功通知",模板包含字段[商品名称、订单编号、支付金额]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        订单发货提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[order_ship]"
                                               value="{{wx_template.order_ship}}" required>
                                        <small>搜索"订单发货提醒",模板包含字段[订单流水号、物流公司、物流单号]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        收益消息提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[income_message]"
                                               value="{{wx_template.income_message}}" required>
                                        <small>搜索"收益通知",模板包含字段[收益类型、收益金额、收益时间、剩余金额]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        提现到账提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[arrive_message]"
                                               value="{{wx_template.arrive_message}}" required>
                                        <small>搜索"提现审核通知",模板包含字段[订单编号、提现金额、提现时间]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        拼团成团提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[team_success_message]"
                                               value="{{wx_template.team_success_message}}" required>
                                        <small>搜索"拼团成功通知",模板包含字段[商品、时间]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        积分消费提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[score_message]"
                                               value="{{wx_template.score_message}}" required>
                                        <small>搜索"积分消费通知",模板包含字段[消费店铺、消费积分、积分余额、消费时间]</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        入驻审核提醒
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="template[merchant_review]"
                                               value="{{wx_template.merchant_review}}" required>
                                        <small>搜索"商户审核结果通知",模板包含字段[商户名称、审核结果、失败原因]</small>
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
