<link rel="stylesheet" href="/assets/admin/css/pear.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<style>
    .layui-form-radio:hover *, .layui-form-radioed, .layui-form-radioed>i {
        color: #e6a602;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">调整上级</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-4 am-form-label form-require">
                                    上级ID
                                </label>
                                <div class="am-u-sm-8">
                                    <input type="text" class="tpl-form-input" name="member[p_id]"
                                           value="" required placeholder="请输入上级ID">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg" style="margin-left: 30px">
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
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
