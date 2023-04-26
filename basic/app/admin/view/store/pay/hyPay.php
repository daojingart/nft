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
                <li><a href="{{:url('store.pay/sdPay')}}">杉德支付</a></li>
                <li><a href="{{:url('store.pay/hfPay')}}">汇付支付</a></li>
                <li class="layui-this"><a href="{{:url('store.pay/hyPay')}}">汇元支付</a></li>
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
                                                <input type="radio" name="hyPay[open_status]" value="10" title="开启" <?= $values['open_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="hyPay[open_status]" value="20" title="关闭" <?= $values['open_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                钱包支付网关:
                                            </label>
                                            <div class="am-u-sm-9 am-u-end layui-form">
                                                <input type="radio" name="hyPay[open_purse_status]" value="10" title="开启" <?= $values['open_purse_status'] === '10' ? 'checked' : '' ?>>
                                                <input type="radio" name="hyPay[open_purse_status]" value="20" title="关闭" <?= $values['open_purse_status'] === '20' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="widget-head am-cf">
                                            <div class="widget-title am-fl">支付信息配置</div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                app_id:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_app_id]"
                                                       value="{{$values.hy_app_id ?? ''}}" required>
                                                <small>例如：汇元银通发放的APPID  具体咨询汇元银通业务经理</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                md5Key:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_md5Key]"
                                                       value="{{$values.hy_md5Key ?? ''}}" required>
                                                <small>例如：汇元银通发放的md5Key  具体咨询汇元银通业务经理</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                des3Key:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_des3Key]"
                                                       value="{{$values.hy_des3Key ?? ''}}" required>
                                                <small>例如：汇元银通发放的des3Key  具体咨询汇元银通业务经理</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                owerId:
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_owerId]"
                                                       value="{{$values.hy_owerId ?? ''}}" required>
                                                <small>例如：汇元银通发放的owerId  具体咨询汇元银通业务经理</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                快捷手续费
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_fast_handling]"
                                                       value="{{$values.hy_fast_handling ?? ''}}" required>
                                                <small>例如：每笔交易的手续费具体咨询汇元银通的商务；请按照汇元银通的要求填写</small>
                                            </div>
                                        </div>
                                        <div class="am-form-group">
                                            <label class="am-u-sm-3 am-form-label form-require">
                                                钱包余额手续费
                                            </label>
                                            <div class="am-u-sm-9">
                                                <input type="text" class="tpl-form-input" name="hyPay[hy_wallet_handling]"
                                                       value="{{$values.hy_wallet_handling ?? ''}}" required>
                                                <small>例如：每笔交易的手续费具体咨询汇元银通的商务；请按照汇元银通的要求填写</small>
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
