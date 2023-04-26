<style>
    .store_system li{
        padding: 0px;
    }
    .store_system li a{
        padding: 0 15px;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="edit_user_tab">
            <ul class="layui-tab-title store_system">
                <li><a href="{{:url('store.setting/store')}}">基础配置</a></li>
                <li class="layui-this" ><a href="{{:url('store.setting/storage')}}" >图片存储</a></li>
                <li><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
                <li><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
                <li><a href="{{:url('store.setting/service')}}">服务费配置</a></li>
                <li><a href="{{:url('store.setting/drop')}}">空投配置</a></li>
                <li><a href="{{:url('store.setting/blockchain')}}">上链配置</a></li>
                <li><a href="{{:url('store.setting/resource')}}">视频配置</a></li>
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
                                                    <div class="widget-title am-fl">文件上传设置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">默认上传方式</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="storage[default]" value="local" title="本地存储" lay-filter="upload_type"  <?= $values['default'] === 'local' ? 'checked' : '' ?>>
                                                        <input type="radio" name="storage[default]" value="qiniu" title="七牛云存储" lay-filter="upload_type" <?= $values['default'] === 'qiniu' ? 'checked' : '' ?>>
                                                        <input type="radio" name="storage[default]" value="aliyun" title="阿里云存储" lay-filter="upload_type" <?= $values['default'] === 'aliyun' ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                                <div id="qiniu" class="form-tab-group <?= $values['default'] === 'qiniu' ? 'active' : '' ?>">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">存储空间名称 <span class="tpl-form-line-small-title">Bucket</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][qiniu][bucket]" value="<?= $values['engine']['qiniu']['bucket'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">ACCESS_KEY <span class="tpl-form-line-small-title">AK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][qiniu][access_key]" value="<?= $values['engine']['qiniu']['access_key'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">SECRET_KEY <span class="tpl-form-line-small-title">SK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][qiniu][secret_key]" value="<?= $values['engine']['qiniu']['secret_key'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">空间域名 <span class="tpl-form-line-small-title">Domain</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][qiniu][domain]" value="<?= $values['engine']['qiniu']['domain'] ?>">
                                                            <small>例如：http://static.cloud.com</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="aliyun" class="form-tab-group <?= $values['default'] === 'aliyun' ? 'active' : '' ?>">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">存储空间名称 <span class="tpl-form-line-small-title">Bucket</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][aliyun][bucket]" value="<?= $values['engine']['aliyun']['bucket'] ?>">
                                                        </div>
                                                        <small>填写Bucket名称，例如examplebucket;名称获取路径：阿里云控制台==》对象存储 OSS==》Bucket 列表；如果Bucket 列表为空则新建即可</small>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">accessKeyId <span class="tpl-form-line-small-title">yourAccessKeyId</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][aliyun][access_key]" value="<?= $values['engine']['aliyun']['access_key'] ?>">
                                                        </div>
                                                        <small>阿里云个人中心==》AccessKey 管理==》AccessKey 列表；获取AccessKey ID</small>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">accessKeySecret <span class="tpl-form-line-small-title">yourAccessKeySecret</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][aliyun][secret_key]" value="<?= $values['engine']['aliyun']['secret_key'] ?>">
                                                        </div>
                                                        <small>阿里云个人中心==》AccessKey 管理==》AccessKey 列表；==》查看 Secret  将获取的参数填写到此处</small>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">Endpoint<span class="tpl-form-line-small-title">Endpoint</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][aliyun][endpoint]" value="<?= $values['engine']['aliyun']['endpoint'] ?>">
                                                        </div>
                                                        <small>阿里云控制台==》对象存储 OSS==》Bucket 列表==》点击对应的存储 Bucket 进入详情==》点击概览==》获取外网访问的Endpoint（地域节点）</small>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">Bucket 域名 <span class="tpl-form-line-small-title">Domain</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="storage[engine][aliyun][domain]" value="<?= $values['engine']['aliyun']['domain'] ?>">
                                                        </div>
                                                        <small>阿里云控制台==》对象存储 OSS==》Bucket 列表==》点击对应的存储 Bucket 进入详情==》点击概览==》获取外网访问的Bucket 域名即可;注意请带上协议头 https</small>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <div class="am-u-sm-10 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-2">
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
                case 'aliyun':
                    $('#aliyun').addClass('active');
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
