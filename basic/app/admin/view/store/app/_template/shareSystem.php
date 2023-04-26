<script  id="share_system" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>APP引导页配置,建议使用底部为白底的引导页配置,以免因为手机型号导致不兼容</p>
                </blockquote>
                <div class="am-cf">
                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                        <div class="widget-body">
                            <fieldset>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3  am-form-label form-require">选择引导页</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <div class="am-form-file">
                                            <div class="am-form-file">
                                                <button type="button"
                                                        class="upload-file am-btn am-btn-secondary am-radius">
                                                    <i class="am-icon-cloud-upload"></i> 选择文件
                                                </button>
                                                <div class="uploader-list am-cf">
                                                    {{each app_guide_page.guide_page item}}
                                                        <div class="file-item">
                                                            <img src="{{item}}">
                                                            <input type="hidden" name="guide[guide_page][]"
                                                                   value="{{item}}">
                                                            <i class="iconfont icon-shanchu file-item-delete"></i>
                                                        </div>
                                                    {{/each}}
                                                </div>
                                            </div>
                                            <div class="help-block am-margin-top-sm">
                                                <small>核验文件是为了配置JS域名使用,请勿上传其他核验文件,或者重复上传</small>
                                            </div>
                                        </div>
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
        // 选择图片
        $('.upload-file').selectImages({
            name: 'guide[guide_page][]',
            multiple: true
        });
        $(function () {
            /**
             * 表单验证提交
             * @type {*}
             */
            $('#my-form').superForm({
                url:"guide"
            });
        });
    </script>
</script>
