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
                <li><a href="{{:url('store.setting/storage')}}" >图片存储</a></li>
                <li class="layui-this"><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
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
                                <blockquote class="layui-elem-quote">
                                    <p>注意:1>短信配置请认真填写配置,阿里云和腾讯云需要审核通过后方可使用</p>
                                    <p>注意:2>上海互亿短信获取参数步骤[ 登录用户中心，进入【验证码通知短信】模块，在【产品总揽】页面右上角：<a target="_blank" href="https://www.ihuyi.com/userfiles/lEcsmhqq5vl0mOf5__thumbnail.png">点击查看</a>]</p>
                                    <p>注意:3>阿里云AccessKeyID和AccessKey Secret获取请点击 <a target="_blank" href="https://usercenter.console.aliyun.com/?spm=5176.12207334.0.0.62ea1cbeUEdA5q#/manage/ak">此处</a></p>
                                    <p>注意:4>腾讯云短信AppID和 Key获取请点击 <a target="_blank" href="https://console.cloud.tencent.com/smsv2/app-manage">此处</a></p>
                                    <p>注意:5>系统默认启动发送短信防刷机制,系统内部只能简单防止,建议根据不同平台进行设置不同的防刷规则以保证短信资产的流失</p>
                                </blockquote>
                                <div class="am-cf">
                                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                                        <div class="widget-body">
                                            <fieldset>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">短信配置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">默认短信服务商</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="sms[default]" value="TY" title="郑州腾域" lay-filter="sms_type"  {{$values['default'] === 'TY' ? 'checked' : '' }}>
                                                        <input type="radio" name="sms[default]" value="HY" title="上海互亿" lay-filter="sms_type"  {{$values['default'] === 'HY' ? 'checked' : '' }}>
                                                        <input type="radio" name="sms[default]" value="AL" title="阿里云" lay-filter="sms_type"   {{$values['default'] === 'AL' ? 'checked' : '' }}>
                                                        <input type="radio" name="sms[default]" value="TX" title="腾讯云" lay-filter="sms_type"   {{$values['default'] === 'TX' ? 'checked' : '' }}>
                                                    </div>
                                                </div>

                                                <div id="TY" class="form-tab-group {{$values['default'] === 'TY' ? 'active' : ''}}">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">账号 <span class="tpl-form-line-small-title">AK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TY][access_key]" value="{{$values['engine']['TY']['access_key']?=$values['engine']['TY']['access_key']}}">
                                                            <small>例如：腾域商务提供的账号</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">密码 <span class="tpl-form-line-small-title">SK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TY][secret_key]" value="{{$values['engine']['TY']['secret_key']?=$values['engine']['TY']['secret_key']}}">
                                                            <small>例如：腾域商务提供的密码</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">短信签名 <span class="tpl-form-line-small-title">签名</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TY][signature]" value="{{$values['engine']['TY']['signature']?=$values['engine']['TY']['signature']}}">
                                                            <small>例如：例如平台名称或者公司简称即可,保持在5个字以内</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="HY" class="form-tab-group {{$values['default'] === 'HY' ? 'active' : ''}}">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">账号 <span class="tpl-form-line-small-title">AK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][HY][access_key]" value="{{$values['engine']['HY']['access_key']?=$values['engine']['HY']['access_key']}}">
                                                            <small>例如：短信提供商提供的账号,此账号不是登录平台的账号</small>
                                                        </div>
                                                    </div>

                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">秘钥 <span class="tpl-form-line-small-title">SK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][HY][secret_key]" value="{{$values['engine']['HY']['secret_key']?=$values['engine']['HY']['secret_key']}}">
                                                            <small>例如：短信提供商提供的秘钥</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="AL" class="form-tab-group {{$values['default'] === 'AL' ? 'active' : ''}}">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">ACCESS_KEY <span class="tpl-form-line-small-title">AK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][AL][access_key]" value="{{$values['engine']['AL']['access_key']?=$values['engine']['AL']['access_key']}}">
                                                            <small>例如：阿里云的主账号 AccessKey</small>
                                                        </div>
                                                    </div>

                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">SECRET_KEY <span class="tpl-form-line-small-title">SK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][AL][secret_key]" value="{{$values['engine']['AL']['secret_key']?=$values['engine']['AL']['secret_key']}}">
                                                            <small>例如：阿里云的主账号 AccessKey 对应的 Secret</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">短信签名 <span class="tpl-form-line-small-title">签名</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][AL][signature]" value="{{$values['engine']['AL']['signature']?=$values['engine']['AL']['signature']}}">
                                                            <small>阿里云审核通过的短信签名,切记是审核通过后的签名</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">短信模板ID <span class="tpl-form-line-small-title">模板ID</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][AL][Template_ID]" value="{{$values['engine']['AL']['Template_ID']?=$values['engine']['AL']['Template_ID']}}">
                                                            <small>阿里云短信模板ID，申请注册短信模板,申请通过后,填写模板ID一般为SMS_开头的</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="TX"  class="form-tab-group <?= $values['default'] === 'TX' ? 'active' : '' ?>">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">APPID</label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TX][access_key]" value="{{$values['engine']['TX']['access_key']?=$values['engine']['TX']['access_key']}}">
                                                            <small>短信平台提供的应用APPID</small>
                                                        </div>
                                                    </div>

                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">APP_KEY <span class="tpl-form-line-small-title">AK</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TX][secret_key]" value="{{$values['engine']['TX']['secret_key']?=$values['engine']['TX']['secret_key']}}">
                                                            <small>短信平台提供的应用APP_KEY</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">短信签名 <span class="tpl-form-line-small-title">签名</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TX][signature]" value="{{$values['engine']['TX']['signature']?=$values['engine']['TX']['signature']}}">
                                                            <small>腾讯云审核通过的短信签名,切记是审核通过后的签名</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">短信模板ID <span class="tpl-form-line-small-title">模板ID</span></label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="sms[engine][TX][Template_ID]" value="{{$values['engine']['TX']['Template_ID']?=$values['engine']['TX']['Template_ID']}}">
                                                            <small>腾讯云短信模板ID，申请注册短信模板,申请通过后,填写模板ID一般为SMS_开头的</small>
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
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        form.on('radio(sms_type)', function(data){
            $('.form-tab-group').removeClass('active');
            switch (data.value) {
                case 'TY':
                    $('#TY').addClass('active');
                    break;
                case 'HY':
                    $('#HY').addClass('active');
                    break;
                case 'AL':
                    $('#AL').addClass('active');
                    break;
                case 'TX':
                    $('#TX').addClass('active');
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
