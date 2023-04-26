<script src="/assets/admin/jquery.min.js"></script>
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<link rel="stylesheet" href="/assets/admin/layui/css/layui.css" />
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">编辑会员信息</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">会员昵称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="stores[name]" value="{{$member_info.name}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">手机号 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="stores[phone]" value="{{$member_info.phone}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">密码</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="password" class="tpl-form-input" name="stores[password]" value="" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">操作密码</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="password" class="tpl-form-input" name="stores[operation_pwd]" value="" >
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-6 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;

    });
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').blAjaxSubmit();
    });
</script>
