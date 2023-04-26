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
                <li><a href="{{:url('store.pay/wxpay')}}">微信支付</a></li>
                <li><a href="{{:url('store.pay/alipay')}}" >支付宝支付</a></li>
                <li><a href="{{:url('store.pay/balance')}}">余额支付</a></li>
                <li><a href="{{:url('store.pay/lianlianPay')}}">连连支付</a></li>
                <li class="layui-this"><a href="{{:url('store.pay/sdPay')}}">杉德支付</a></li>
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
                                                快捷支付网关:
                                            </label>
                                            <div class="am-u-sm-9 am-u-end layui-form">
                                                <input type="radio" name="sdPay[open_status]" value="10" title="开启" <?= $values['open_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="sdPay[open_status]" value="20" title="关闭" <?= $values['open_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                钱包支付网关:
                                            </label>
                                            <div class="am-u-sm-9 am-u-end layui-form">
                                                <input type="radio" name="sdPay[open_purse_status]" value="10" title="开启" <?= $values['open_purse_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="sdPay[open_purse_status]" value="20" title="关闭" <?= $values['open_purse_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="widget-head am-cf">
                                            <div class="widget-title am-fl">开户费配置</div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                是否开启:
                                            </label>
                                            <div class="am-u-sm-9 am-u-end layui-form">
                                                <input type="radio" name="sdPay[account_opening_fee_status]" value="10" title="开启" <?= $values['account_opening_fee_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="sdPay[account_opening_fee_status]" value="20" title="关闭" <?= $values['account_opening_fee_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                开户费用:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="sdPay[account_opening_fee]"
                                                       value="{{$values.account_opening_fee ?? ''}}" required>
                                                <small>用户在使用杉德钱包开户时,平台收取的开户费用,此费用和杉德无关,有平台自己定义</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                收费须知:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <textarea class="tpl-form-input" name="sdPay[account_opening_fee_content]" cols="30" rows="10">{{$values.account_opening_fee_content ?? ''}}</textarea>
                                                <small>例如：用户开户时,平台展示给用户的收费须知</small>
                                            </div>
                                        </div>
                                        <div class="widget-head am-cf">
                                            <div class="widget-title am-fl">支付信息配置</div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                商户号码:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="sdPay[merchant_id]"
                                                       value="{{$values.merchant_id ?? ''}}" required>
                                                <small>例如：杉德分配的商户ID，一般是68888开头13位</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                私钥证书:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <div style="display: flex;">
                                                    <input type="text" class="tpl-form-input privatePfx" name="sdPay[privatePfx_path]"
                                                           value="{{$values.privatePfx_path ?? ''}}" required>
                                                    <button type="button" class="layui-btn layui-bg-blue" id="privatePfx">
                                                        <i class="layui-icon">&#xe621;</i>上传证书
                                                    </button>
                                                </div>
                                                <small>例如：.pfx 结尾的证书文件</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                私钥证书密码:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="sdPay[privatePfxPwd]"
                                                       value="{{$values.privatePfxPwd ?? ''}}" required>
                                                <small>例如：申请私钥证书.pfx 结尾的时候设置的密码</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                杉德云账户公钥证书:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <div style="display: flex;">
                                                    <input type="text" class="tpl-form-input publicKey" name="sdPay[publicKey_path]"
                                                           value="{{$values.publicKey_path ?? ''}}" required>
                                                    <button type="button" class="layui-btn layui-bg-blue" id="publicKey">
                                                        <i class="layui-icon">&#xe621;</i>上传证书
                                                    </button>
                                                </div>
                                                <small>云账户公钥证书</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                杉德账户侧公钥证书:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <div style="display: flex;">
                                                    <input type="text" class="tpl-form-input publicKeyPro" name="sdPay[publicKeyPro_path]"
                                                           value="{{$values.publicKeyPro_path ?? ''}}" required>
                                                    <button type="button" class="layui-btn layui-bg-blue" id="publicKeyPro">
                                                        <i class="layui-icon">&#xe621;</i>上传证书
                                                    </button>
                                                </div>
                                                <small>杉德账户侧公钥证书,证书有杉德提供</small>
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

    layui.use('upload', function(){
        var upload = layui.upload;
        //私钥证书
        upload.render({
            elem: '#privatePfx' //绑定元素
            ,url: "{{:url('upload.upload/localCertificateUpload')}}" //上传接口
            ,exts: 'pfx|cer'
            ,acceptMime:'file/pfx|file/cer'
            ,done: function(res){
                //上传完毕回调
                if(res.code == 1){
                    $(".privatePfx").val(res.data.url);
                }else{
                    layer.msg(res.msg);
                }
            }
        });
        //云账户公钥证书
        upload.render({
            elem: '#publicKey' //绑定元素
            ,url: "{{:url('upload.upload/localCertificateUpload')}}" //上传接口
            ,exts: 'pfx|cer'
            ,acceptMime:'file/pfx|file/cer'
            ,done: function(res){
                //上传完毕回调
                if(res.code == 1){
                    $(".publicKey").val(res.data.url);
                }else{
                    layer.msg(res.msg);
                }
            }
        });
        //账户侧公钥证书
        upload.render({
            elem: '#publicKeyPro' //绑定元素
            ,url: "{{:url('upload.upload/localCertificateUpload')}}" //上传接口
            ,exts: 'pfx|cer'
            ,acceptMime:'file/pfx|file/cer'
            ,done: function(res){
                if(res.code == 1){
                    $(".publicKeyPro").val(res.data.url);
                }else{
                    layer.msg(res.msg);
                }
            }
        });
    });
</script>
