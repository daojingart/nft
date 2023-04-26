<script  id="protocol" type="text/template">
    <div class="am-cf">
        <div class="row">
            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                <blockquote class="layui-elem-quote">
                    <p>注意:1>APP服务协议和隐私协议配置</p>
                </blockquote>
                <div class="am-cf">
                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                        <div class="widget-body">
                            <fieldset>
                                <div class="widget-head am-cf">
                                    <div class="widget-title am-fl">协议配置</div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        用户服务协议
                                    </label>
                                    <div class="am-u-sm-9">
                                        <textarea id="user_agreement" name="protocol[user_agreement]" type="text/plain">{{wx_protocol.user_agreement}}</textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-form-label form-require">
                                        APP隐私协议
                                    </label>
                                    <div class="am-u-sm-9">
                                        <!-- 加载编辑器的容器 -->
                                        <textarea id="app_agreement" name="protocol[app_agreement]" type="text/plain">{{wx_protocol.app_agreement}}</textarea>
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
            // 富文本编辑器
            UM.getEditor('user_agreement');
            // 富文本编辑器
            UM.getEditor('app_agreement');
            /**
             * 表单验证提交
             * @type {*}
             */
            $('#my-form').superForm({
                url:"protocol"
            });
        });
</script>
</script>
