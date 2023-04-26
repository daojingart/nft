<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="edit_user_tab">
            <ul class="layui-tab-title store_system">
                <li ><a href="{{:url('store.setting/store')}}">基础配置</a></li>
                <li><a href="{{:url('store.setting/storage')}}" >图片存储</a></li>
                <li><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
                <li><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
                <li><a href="{{:url('store.setting/service')}}">服务费配置</a></li>
                <li><a href="{{:url('store.setting/drop')}}">空投配置</a></li>
                <li><a href="{{:url('store.setting/blockchain')}}">上链配置</a></li>
                <li   class="layui-this"><a href="{{:url('store.setting/resource')}}">视频配置</a></li>
                <li><a href="{{:url('store.setting/behaviorcode')}}">行为验证码</a></li>
            </ul>
            <!--内容区域-->
            <div class="layui-tab-content layui-store-tab-content">
                <div class="layui-tab-item layui-show layui-store-tab-show">
                    <link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
                    <script src="/assets/admin/amazeui.min.js"></script>
                    <div class="am-cf">
                        <div class="row">
                            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                                <div class="am-cf">
                                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                                        <div class="widget-body">
                                            <fieldset>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">资源存储 <a target="_blank" style="color: red" href="https://console.cloud.tencent.com/cam/capi">点击查看秘钥</a></div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-3 am-form-label">
                                                        默认上传方式
                                                    </label>
                                                    <div class="am-u-sm-9 am-u-end layui-form">
                                                        <input type="radio" name="resource[default]" value="tx_oss" title="腾讯云OSS" lay-filter="upload_type" <?= $values['default'] === 'tx_oss' ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                                <div id="qiniu"
                                                     class="form-tab-group <?= $values['default'] === 'tx_oss' ? 'active' : '' ?>">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-3 am-form-label">
                                                            ACCESS_KEY <span class="tpl-form-line-small-title">AK</span>
                                                        </label>
                                                        <div class="am-u-sm-9">
                                                            <input type="text" class="tpl-form-input"
                                                                   name="resource[engine][tx_oss][access_key]"
                                                                   value="<?= $values['engine']['tx_oss']['access_key'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-3 am-form-label">
                                                            SECRET_KEY <span class="tpl-form-line-small-title">SK</span>
                                                        </label>
                                                        <div class="am-u-sm-9">
                                                            <input type="text" class="tpl-form-input"
                                                                   name="resource[engine][tx_oss][secret_key]"
                                                                   value="<?= $values['engine']['tx_oss']['secret_key'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">
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
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        form.on('radio(upload_type)', function(data){
            $('.form-tab-group').removeClass('active');
            switch (data.value) {
                case 'qiniu':
                    $('#qiniu').addClass('active');
                    break;
                case 'local':
                    break;
            }

        });
    });
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
