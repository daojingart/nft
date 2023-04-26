<body class="pear-container pear-container">
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
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
                                                <div class="widget-title am-fl">客服配置</div>
                                            </div>
                                            <div class="am-form-group layui-form">
                                                <label class="am-u-sm-2 am-form-label form-require">默认客服类型</label>
                                                <div class="am-u-sm-10 layui-form">
                                                    <input type="radio" name="customer[default]" value="10" title="关闭" lay-filter="customer_type"  {{$values['default'] === '10' ? 'checked' : '' }}>
                                                    <input type="radio" name="customer[default]" value="link" title="链接" lay-filter="customer_type"  {{$values['default'] === 'link' ? 'checked' : '' }}>
                                                    <input type="radio" name="customer[default]" value="code" title="二维码" lay-filter="customer_type"   {{$values['default'] === 'code' ? 'checked' : '' }}>
                                                </div>
                                            </div>

                                            <div id="link" class="form-tab-group {{$values['default'] === 'link' ? 'active' : ''}}">
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label">客服链接</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="customer[engine][link][link_url]" value="{{$values['engine']['link']['link_url']?=$values['engine']['link']['link_url']}}">
                                                        <small>请填写客服窗口的链接</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="code" class="form-tab-group {{$values['default'] === 'code' ? 'active' : ''}}">
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">二维码</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-icon am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values['engine']['code']['link_url']?=$values['engine']['code']['link_url']}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="customer[engine][code][link_url]" value="{{$values['engine']['code']['link_url']?=$values['engine']['code']['link_url']}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>请选择上传客服的二维码</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="am-form-group">
                                                <div class="am-u-sm-10 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">
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
</body>
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
        // 选择图片
        $('.upload-file-icon').selectImages({
            name: 'customer[engine][code][link_url]',
        });

    });
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        form.on('radio(customer_type)', function(data){
            $('.form-tab-group').removeClass('active');
            switch (data.value) {
                case 'link':
                    $('#link').addClass('active');
                    break;
                case 'code':
                    $('#code').addClass('active');
                    break;
            }

        });
    });
</script>
