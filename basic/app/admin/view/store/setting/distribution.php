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
                                <div class="widget-title am-fl">分销设置【升级优化中;暂不可用】</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">一级分佣比例</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input style="border: 1px solid #ccc;" type="number" name="distribution[one_level]"
                                               class="am-form-field" min="0"
                                               value="{{$values.one_level ?? ''}}" required>
                                        <span class="am-input-group-label am-input-group-label__right">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">二级分佣比例</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input style="border: 1px solid #ccc;" type="number" name="distribution[two_level]"
                                               class="am-form-field" min="0"
                                               value="{{$values.two_level ?? ''}}" required>
                                        <span class="am-input-group-label am-input-group-label__right">%</span>
                                    </div>
                                </div>
                            </div>
<!--                            <div class="am-form-group">-->
<!--                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">-->
<!--                                    <button type="submit" class="j-submit am-btn am-btn-secondary">-->
<!--                                        确认提交-->
<!--                                    </button>-->
<!--                                </div>-->
<!--                            </div>-->
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
