<script  id="template_system" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>APP系统升级版本，下载链接请填写正确</p>
                    <p>注意:2>注意:版本号和当前下载链接请保持一致</p>
                </blockquote>
                <div class="am-cf">
                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                        <div class="widget-body">
                            <fieldset>
                                <div class="widget-head am-cf">
                                    <div class="widget-title am-fl">APP系统升级配置</div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        安卓下载链接
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="upgrade[android_down_link]"
                                               value="{{app_upgrade.android_down_link}}" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        安卓当前升级版本
                                    </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" name="upgrade[android_vision]"
                                               value="{{app_upgrade.android_vision}}" required>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        更新内容
                                    </label>
                                    <div class="am-u-sm-9">
                                        <textarea name="upgrade[android_content]" id="" cols="30" rows="10">{{app_upgrade.android_content}}</textarea>
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
        $(function () {
            /**
             * 表单验证提交
             * @type {*}
             */
            $('#my-form').superForm({
                url:"upgrade"
            });
        });
    </script>
</script>
