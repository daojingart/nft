<link rel="stylesheet" href="/assets/admin/css/pear.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<script>
    BASE_URL = '<?= isset($base_url) ? $base_url : '' ?>';
    STORE_URL = '<?= isset($store_url) ? $store_url : '' ?>';
</script>
<style>
    .am-form .am-form-file .upload-file1{
        font-size: 1.24rem;
        padding: 0.6em 1em;
    }
    .edu-paid-trainee{
        display: none;
    }
    .layui-form-radio:hover *, .layui-form-radioed, .layui-form-radioed>i {
        color: #1e9fff;
    }
    .individually-paid-trainee{
        display: none;
    }
    .consignment-paid-trainee{
        display: none;
    }
</style>
<div class="am-cf"  style="overflow-x: hidden;height: inherit;">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form"  lay-filter="create_table_from" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加优先购买规则</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">持有藏品</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select id="goods_id" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择持有藏品',maxHeight:'200px'}">
                                        <?php if (isset($goods_list)): foreach ($goods_list as $first): ?>
                                            <option value="<?= $first['goods_id'] ?>" ><?= $first['goods_name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">提前购买时间</label>
                                <div class="am-u-sm-9 am-u-md-6 am-u-lg-5 am-u-end">
                                    <div class="am-input-group">
                                        <input id="purchase_time" style="border: 1px solid #ccc;" type="number" name="order[pay_time]"
                                               class="am-form-field" min="0"
                                               value="" required>
                                        <span class="am-input-group-label am-input-group-label__right">分钟</span>
                                    </div>
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
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<script src="/assets/resource/vue.js"></script>
<script src="/assets/resource/axios.min.js"></script>
<script src="/assets/resource/vod-js-sdk-v6.js"></script>
<script>

    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form
        element = layui.element;
        let laydate = layui.laydate;
        layui.code();
    });

    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').blAjaxSubmit();


    });
    var callbackdata = function () {
        return $('#my-form').serializeArray();
    }
</script>
