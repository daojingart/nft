<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加导航</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">图标</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">

                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸24x24像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选中图标</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file1 am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">

                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸24x24像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="am-form-group">
                                <label class="am-u-sm-2 am-form-label form-require">标题</label>
                                <div class="am-u-sm-10">
                                    <input type="text" class="tpl-form-input" name="navigation[name]" value="" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-2 am-form-label form-require">跳转URL</label>
                                <div class="am-u-sm-10">
                                    <input type="text" class="tpl-form-input" name="navigation[url]" value="" required>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">是否显示</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="navigation[status]" value="10" title="显示" checked>
                                    <input type="radio" name="navigation[status]" value="20" title="隐藏">
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

<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}

<script src="/assets/admin/amazeui.min.js"></script>
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        layui.code();
    });
    $(function () {

        // 选择图片
        $('.upload-file').selectImages({
            name: 'navigation[icon]',
        });

        $('.upload-file1').selectImages({
            name: 'navigation[selected_icon]',
        });




        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
