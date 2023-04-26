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
                <li><a href="{{:url('store.setting/resource')}}">视频配置</a></li>
                <li   class="layui-this"><a href="{{:url('store.setting/behaviorcode')}}">行为验证码</a></li>
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
                                                    <div class="widget-title am-fl">行为验证码核验<a target="_blank" style="color: red" href="https://console.cloud.tencent.com/captcha/graphical">点击直达控制台</a>；点击左侧菜单栏目的图形验证--》内容区点击新建验证--》名称随意填写、验证域名、验证渠道填写Web端/App(IOS或Android)、验证场景、短信验证码</div>
                                                    <div class="widget-title am-fl">行为验证码核验；主要用于发送短信、领取空投卷、兑换藏品等场景使用能够有效防止机器进行恶意注册<a href="https://cloud.tencent.com/document/product/1110/36334" style="color: red">详细介绍</a></div>
                                                    <div class="widget-title am-fl">上线模式,会实际扣除腾讯购买的套餐包,测试模式不会扣除,但是需要用户滑动无法达到实际的拦截效果;请合理选择</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label">
                                                        CaptchaAppId <span class="tpl-form-line-small-title">控制台秘钥栏目的CaptchaAppId</span>
                                                    </label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input"
                                                               name="behaviorcode[appid]"
                                                               value="<?= isset($values['appid']) ? $values['appid'] : '' ?>">
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label">
                                                        AppSecretKey <span class="tpl-form-line-small-title">控制台秘钥栏目的AppSecretKey</span>
                                                    </label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input"
                                                               name="behaviorcode[secretkey]"
                                                               value="<?= isset($values['secretkey']) ? $values['secretkey'] : '' ?>">
                                                    </div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">行为验证码</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        {{if isset($values['tx_verification_open'])}}
                                                        <input type="radio" name="behaviorcode[tx_verification_open]" value="1" title="上线"   <?= $values['tx_verification_open'] === '1' ? 'checked' : '' ?>>
                                                        <input type="radio" name="behaviorcode[tx_verification_open]" value="2" title="调式"  <?= $values['tx_verification_open'] === '2' ? 'checked' : '' ?>>
                                                        {{else /}}
                                                        <input type="radio" name="behaviorcode[tx_verification_open]" value="1" title="上线" >
                                                        <input type="radio" name="behaviorcode[tx_verification_open]" value="2" title="调式" checked>
                                                        {{/if}}
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
    });
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
