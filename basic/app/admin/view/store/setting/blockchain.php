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
                <li><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
                <li><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
                <li><a href="{{:url('store.setting/service')}}">服务费配置</a></li>
                <li><a href="{{:url('store.setting/drop')}}">空投配置</a></li>
                <li  class="layui-this"><a href="{{:url('store.setting/blockchain')}}">上链配置</a></li>
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
                                                    <div class="widget-title am-fl">上链配置</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">默认上链选择</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="blockchain[default]" value="SL" title="私链" lay-filter="sms_type"  {{$values['default'] === 'SL' ? 'checked' : '' }}>
                                                        <input type="radio" name="blockchain[default]" value="BD" title="百度超级链" lay-filter="sms_type"  {{$values['default'] === 'BD' ? 'checked' : '' }}>
                                                        <input type="radio" name="blockchain[default]" value="WC" title="文昌链" lay-filter="sms_type"   {{$values['default'] === 'WC' ? 'checked' : '' }}>
                                                        <input type="radio" name="blockchain[default]" value="TH" title="天河链" lay-filter="sms_type"   {{$values['default'] === 'TH' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                                <div id="BD" class="form-tab-group {{$values['default'] === 'BD' ? 'active' : ''}}">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">appid</label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[BD][appid]" value="{{$values.BD.appid?=$values.BD.appid}}">
                                                            <small></small>
                                                        </div>
                                                    </div>

                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">AccessKey </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[BD][AccessKey]" value="{{$values.BD.AccessKey?=$values.BD.AccessKey}}">
                                                            <small> </small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">SecretKey </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[BD][SecretKey]" value="{{$values.BD.SecretKey?=$values.BD.SecretKey}}">
                                                            <small> </small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="WC" class="form-tab-group {{$values['default'] === 'WC' ? 'active' : ''}}">
                                                    <div class="am-form-group layui-form">
                                                        <label class="am-u-sm-2 am-form-label form-require">版本选择</label>
                                                        <div class="am-u-sm-10 layui-form">
                                                            <input type="radio" name="blockchain[WC][version]" value="1" title="V1"  {{$values['WC']['version'] === '1' ? 'checked' : '' }}>
                                                            <input type="radio" name="blockchain[WC][version]" value="2" title="V2"   {{$values['WC']['version'] === '2' ? 'checked' : '' }}>
                                                            <small>V2版本需要配置回调域名：系统域名+/notice/Notifywc/classesCallback</small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">项目ID</label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[WC][appid]" value="{{$values.WC.appid?=$values.WC.appid}}">
                                                            <small></small>
                                                        </div>
                                                    </div>

                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">APIKEY </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[WC][APIKEY]" value="{{$values.WC.APIKEY?=$values.WC.APIKEY}}">
                                                            <small> </small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">APISECRET </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[WC][APISECRET]" value="{{$values.WC.APISECRET?=$values.WC.APISECRET}}">
                                                            <small> </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="TH" class="form-tab-group {{$values['default'] === 'TH' ? 'active' : ''}}">
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">app_id </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[TH][APIKEY]" value="{{$values.TH.APIKEY?=$values.TH.APIKEY}}">
                                                            <small> </small>
                                                        </div>
                                                    </div>
                                                    <div class="am-form-group">
                                                        <label class="am-u-sm-2 am-form-label">app_key </label>
                                                        <div class="am-u-sm-10">
                                                            <input type="text" class="tpl-form-input" name="blockchain[TH][APISECRET]" value="{{$values.TH.APISECRET?=$values.TH.APISECRET}}">
                                                            <small> </small>
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
        form.on('radio(sms_type)', function(data){
            $('.form-tab-group').removeClass('active');
            switch (data.value) {
                case 'SL':
                    $('#SL').addClass('active');
                    break;
                case 'BD':
                    $('#BD').addClass('active');
                    break;
                case 'WC':
                    $('#WC').addClass('active');
                    break;
                case 'TH':
                    $('#TH').addClass('active');
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

