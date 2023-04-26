<body class="pear-container pear-container">
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<style>
    .store_system li{
        padding: 0px;
    }
    .store_system li a{
        padding: 0 15px;
    }
    .layui-upload-drag .layui-icon {
        font-size: 50px;
        color: #1e9fff;
    }
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="edit_user_tab">
            <ul  class="layui-tab-title store_system">
                <li class="layui-this"><a href="{{:url('store.setting/store')}}">基础配置</a></li>
                {{if $store['user']['user_rule'] =='*'}}
                <li><a href="{{:url('store.setting/storage')}}" >图片存储</a></li>
                <li><a href="{{:url('store.setting/sms')}}">短信配置</a></li>
                {{/if}}
                <li><a href="{{:url('store.setting/collection')}}">藏品配置</a></li>
                <li><a href="{{:url('store.setting/certification')}}">实名认证</a></li>
                <li><a href="{{:url('store.setting/agreement')}}">协议配置</a></li>
                <li><a href="{{:url('store.setting/withdrawal')}}">提现配置</a></li>
                <li><a href="{{:url('store.setting/service')}}">服务费配置</a></li>
                <li><a href="{{:url('store.setting/drop')}}">空投配置</a></li>
                {{if $store['user']['user_rule'] =='*'}}
                <li><a href="{{:url('store.setting/blockchain')}}">上链配置</a></li>
                <li><a href="{{:url('store.setting/resource')}}">视频配置</a></li>
                <li><a href="{{:url('store.setting/behaviorcode')}}">行为验证码</a></li>
                {{/if}}
            </ul>
            <!--内容区域-->
            <div class="layui-tab-content layui-store-tab-content">
                <div class="layui-tab-item layui-show layui-store-tab-show">
                    <div class="am-cf">
                        <div class="row">
                            <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
                                <blockquote class="layui-elem-quote">
                                    <p>注意:1>系统基础配置项目,请严格按照要求配置系统以保证系统正常运行</p>
                                    <p>注意:2>红色*号为系统运行必须配置项目</p>
                                </blockquote>
                                <div class="am-cf">
                                    <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                                        <div class="widget-body">
                                            <fieldset>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">基础配置</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">系统名称</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[system_name]" value="<?= isset($values['system_name']) ? $values['system_name'] : '' ?>" required>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">系统简介</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[share_content]" value="<?= isset($values['share_content']) ? $values['share_content'] : '' ?>" required>
                                                        <small>微信分享调用此处的介绍</small>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">联系电话</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[contact_number]" value="<?= isset($values['contact_number']) ? $values['contact_number'] : '' ?>" required>
                                                    </div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">版权配置</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">版权归属</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[copyright]" value="<?= isset($values['copyright']) ? $values['copyright'] : '' ?>" required>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">ICP备案号</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[icp_number]" value="<?= isset($values['icp_number']) ? $values['icp_number'] : '' ?>" required>
                                                    </div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">注册机制</div>
                                                </div>
                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">默认上传方式</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="store[register]" value="10" title="邀请码注册" lay-filter="upload_type"  <?= $values['register'] === '10' ? 'checked' : '' ?>>
                                                        <input type="radio" name="store[register]" value="20" title="无邀请码注册" lay-filter="upload_type" <?= $values['register'] === '20' ? 'checked' : '' ?>>
                                                    </div>
                                                    <div class="am-u-sm-10">
                                                        <small>1.邀请码注册时必须是有邀请码才可以注册，没有邀请码不能注册，扫描邀请码海报自动填写邀请码</small>
                                                        <small>2.无邀请码注册，则不强制使用邀请码注册功能，填写不填写都可以注册</small>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-u-sm-2 am-form-label form-require">荣誉值名称</label>
                                                    <div class="am-u-sm-10">
                                                        <input type="text" class="tpl-form-input" name="store[honor]" value="<?= isset($values['honor']) ? $values['honor'] : '' ?>" required>
                                                    </div>
                                                </div>

                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">邀请海报</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">图片</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                    <img src="{{$values.invitation?=$values.invitation}}" alt="" style="width: 150px">
                                                                    <input type="hidden" name="store[invitation]" value="{{$values.invitation?=$values.invitation}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.图片大小 900 × 1329</small>
                                                                    <small>2. 图片右边区域为二维码留置区域 请按照样图设计保留二维码区域</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="widget-head am-cf">
                                                    <div class="widget-title am-fl">系统图片配置</div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">ICO 图片上传</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-icon am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values.ico_images?=$values.ico_images}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="store[ico_images]" value="{{$values.ico_images?=$values.ico_images}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.文件名称需要为：favicon.ico</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">登录页面LOGO</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-logo am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values.login_logo_img?=$values.login_logo_img}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="store[login_logo_img]" value="{{$values.login_logo_img?=$values.login_logo_img}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.登录页面的LOGO显示；图片尺寸：1:1</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">首页左上角LOGO</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-left-logo am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values.login_left_logo_img?=$values.login_left_logo_img}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="store[login_left_logo_img]" value="{{$values.login_left_logo_img?=$values.login_left_logo_img}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.首页左上角的LOGO；249 × 63</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">登录页面副标题图片</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-logo-title am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values.login_logo_title_img?=$values.login_logo_title_img}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="store[login_logo_title_img]" value="{{$values.login_logo_title_img?=$values.login_logo_title_img}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.登录页面的LOGO显示；1125 × 222</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="am-form-group">
                                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">首页广告图</label>
                                                    <div class="am-u-sm-9 am-u-end">
                                                        <div class="am-form-file">
                                                            <div class="am-form-file">
                                                                <button type="button" class="upload-file-logo-ad am-btn am-btn-secondary am-radius">
                                                                    <i class="am-icon-cloud-upload"></i> 选择图片
                                                                </button>
                                                                <div class="uploader-list am-cf">
                                                                    <div class="file-item">
                                                                        <img src="{{$values.login_logo_ad_img?=$values.login_logo_ad_img}}" alt="" style="width: 150px">
                                                                        <input type="hidden" name="store[login_logo_ad_img]" value="{{$values.login_logo_ad_img?=$values.login_logo_ad_img}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="help-block am-margin-top-sm">
                                                                <div class="am-u-sm-10">
                                                                    <small>1.登录页面的LOGO显示；1035X780</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>




                                                <div class="am-form-group layui-form">
                                                    <label class="am-u-sm-2 am-form-label form-require">首页广告开关</label>
                                                    <div class="am-u-sm-10 layui-form">
                                                        <input type="radio" name="store[advertisement]" value="10" title="开启" lay-filter="upload_type"  {{if isset($values['advertisement'])}} {{if $values['advertisement']==10}} checked {{/if}} {{/if}}>
                                                        <input type="radio" name="store[advertisement]" value="20" title="关闭" lay-filter="upload_type"  {{if isset($values['advertisement'])}} {{if $values['advertisement']==20}} checked {{/if}} {{/if}}>
                                                    </div>
                                                </div>





                                                <div class="am-form-group">
                                                    <div class="am-u-sm-10 am-u-sm-push-3 am-u-lg-offset-2 am-margin-top-lg">
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
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
<script>
    layui.use(['form','colorpicker','upload'], function(){
        var $ = layui.$
            ,upload = layui.upload
            ,form = layui.form;

            colorpicker = layui.colorpicker;

        form.on('radio(customer_service_type)', function(data){
            console.log(data);
            var $alert_window = $('.alert_window')
                , $to_link = $('.to_link');
            if (data.value === '10') {
                $to_link.hide() && $alert_window.show();
            } else {
                $to_link.show() && $alert_window.hide();
            }
        });
        //表单赋值
        colorpicker.render({
            elem: '#test-form'
            ,color: '#1c97f5'
            ,done: function(color){
                $('#test-form-input').val(color);
            }
        });




    })
        $(function () {
            // 选择图片
            $('.upload-file').selectImages({
                name: 'store[invitation]',
            });
            // 选择图片
            $('.upload-file-icon').selectImages({
                name: 'store[ico_images]',
            });
            //LOGO
            $('.upload-file-logo').selectImages({
                name: 'store[login_logo_img]',
            });
            $('.upload-file-left-logo').selectImages({
                name: 'store[login_left_logo_img]',
            });
            //登录页面副标题
            $('.upload-file-logo-title').selectImages({
                name: 'store[login_logo_title_img]',
            });
            //首页广告图
            $('.upload-file-logo-ad').selectImages({
                name: 'store[login_logo_ad_img]',
            });
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
