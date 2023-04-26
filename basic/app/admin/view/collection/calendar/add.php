<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加发售日历</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="calendar[sort]" value="" placeholder="请输入排序值" required>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">是否展示 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="calendar[disabled]" value="0" title="启用" checked>
                                    <input type="radio" name="calendar[disabled]" value="1" title="隐藏">
                                </div>
                            </div>

                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
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

<script src="/assets/admin/amazeui.min.js"></script>
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var element = layui.element;
        let laydate = layui.laydate;

        //  时间
        laydate.render({
            elem: '#calendar_time' //指定元素
            ,type: 'datetime'
            ,format: 'MM-dd HH:mm'
        });

        layui.code();
    });

    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
