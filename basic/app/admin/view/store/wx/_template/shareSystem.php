<script  id="share_system" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>微信分享配置,使用微信转发朋友圈,分享给朋友配置,注明:只配置首页,在抓取不到的图片和标题的情况下使用</p>
                </blockquote>
                <div class="am-cf">
                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                        <div class="widget-body">
                            <fieldset>
                                <div class="widget-head am-cf">
                                    <div class="widget-title am-fl">分享配置</div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        分享标题
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="share[title]"
                                               value="{{share_info.title}}" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        分享简介
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="share[desc]"
                                               value="{{share_info.desc}}" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3  am-form-label form-require">分享封面图</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <div class="am-form-file">
                                            <div class="am-form-file">
                                                <button type="button"
                                                        class="upload-file am-btn am-btn-secondary am-radius">
                                                    <i class="am-icon-cloud-upload"></i> 选择文件
                                                </button>
                                                <div class="uploader-list am-cf">
                                                    <div class="file-item" style="display: {{share_info['images'] ?'inline':'none'}} ">
                                                        <img src="{{share_info['images']}}">
                                                        <input type="hidden" name="wx[verification_file]" value="{{share_info.images}}">
                                                        <i class="iconfont icon-shanchu file-item-delete"></i>
                                                    </div>
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
            name: 'share[images]'
        });
        $(function () {
            /**
             * 表单验证提交
             * @type {*}
             */
            $('#my-form').superForm({
                url:"share"
            });
        });
    </script>
</script>
