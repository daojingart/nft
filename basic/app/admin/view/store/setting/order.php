<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">订单设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">支付时间</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input style="border: 1px solid #ccc;" type="number" name="order[pay_time]"
                                               class="am-form-field" min="0"
                                               value="{{$values.pay_time ?? ''}}" required>
                                        <span class="am-input-group-label am-input-group-label__right">分钟</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">锁单次数</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input style="border: 1px solid #ccc;" type="number" name="order[order_lock_number]"
                                               class="am-form-field" min="0"
                                               value="{{$values.order_lock_number ?? ''}}" required>
                                        <span class="am-input-group-label am-input-group-label__right">单</span>
                                    </div>
                                    <small>一个小时内连续锁单不支付达到多少次禁用下单功能</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">禁止交易</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input style="border: 1px solid #ccc;" type="number" name="order[order_lock_time]"
                                               class="am-form-field" min="0"
                                               value="{{$values.order_lock_time ?? ''}}" required>
                                        <span class="am-input-group-label am-input-group-label__right">小时</span>
                                    </div>
                                    <small>一个小时内连续锁单次数达到上面设置的条件,禁用多长时间的下单</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">
                                    连续下单开关:
                                </label>
                                <div class="am-u-sm-9 am-u-end layui-form">
                                    <input type="radio" name="order[create_order_open]" value="10" title="开启" <?= $values['create_order_open'] === '10' ? 'checked' : '' ?>>
                                    <input type="radio" name="order[create_order_open]" value="20" title="关闭" <?= $values['create_order_open'] === '20' ? 'checked' : '' ?>>
                                    <small>开启后用户可以连续下单,关闭后用户则需要下单支付完成后才可以进行下一单的购买</small>

                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">
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
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        layui.code();
    });
    $(function () {
        UM.getEditor('container');
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
