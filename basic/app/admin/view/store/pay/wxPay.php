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

                <li class="layui-this"><a href="{{:url('store.pay/wxpay')}}">微信支付</a></li>
                <li><a href="{{:url('store.pay/alipay')}}" >支付宝支付</a></li>
                <li><a href="{{:url('store.pay/balance')}}">余额支付</a></li>
                <li><a href="{{:url('store.pay/lianlianPay')}}">连连支付</a></li>
                <li><a href="{{:url('store.pay/sdPay')}}">杉德支付</a></li>
                <li><a href="{{:url('store.pay/hfPay')}}">汇付支付</a></li>
                <li><a href="{{:url('store.pay/hyPay')}}">汇元支付</a></li>
                <li><a href="{{:url('store.pay/hftPay')}}">汇付通支付</a></li>
                <li><a href="{{:url('store.pay/yeePay')}}">易宝支付</a></li>
                <li><a href="{{:url('store.pay/sxPay')}}">首信易支付</a></li>
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
                                        <div class="widget-head am-cf">
                                            <div class="widget-title am-fl">网关配置</div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                支付网关
                                            </label>
                                            <div class="am-u-sm-9 am-u-end layui-form">
                                                <input type="radio" name="wxPay[open_status]" value="10" title="开启" <?= $values['open_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="wxPay[open_status]" value="20" title="关闭" <?= $values['open_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="widget-head am-cf">
                                            <div class="widget-title am-fl">支付信息配置</div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                mch_id:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="wxPay[mch_id]"
                                                       value="{{$values.mch_id ?? ''}}" required>
                                                <small>必填-商户号;可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                mch_secret_key
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="wxPay[mch_secret_key]"
                                                       value="{{$values.mch_secret_key ?? ''}}" required>
                                                <small>必填-商户秘钥;即 API v2 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                mch_secret_cert
                                            </label>
                                            <div class="am-u-sm-9">
                                                <textarea class="tpl-form-input" name="wxPay[mch_secret_cert]"  cols="30" rows="5" required>{{$values.mch_secret_cert ?? ''}}</textarea>
                                                <small>必填-商户私钥字符串;API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得文件名形如：apiclient_key.pem</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                mch_public_cert
                                            </label>
                                            <div class="am-u-sm-9">
                                                <textarea class="tpl-form-input" name="wxPay[mch_public_cert]"  cols="30" rows="5" required>{{$values.mch_public_cert ?? ''}}</textarea>
                                                <small>必填-商户公钥证书;即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得文件名形如：apiclient_cert.pem</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <div class="am-u-sm-9 am-u-sm-push-3 am-u-lg-offset-3 am-margin-top-lg">
                                                <button type="submit" class="j-submit am-btn am-btn-secondary">
                                                    确认提交
                                                </button>
                                            </div>
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
    $(function () {

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
