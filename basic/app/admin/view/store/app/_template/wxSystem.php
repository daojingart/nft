<script  id="wx_system" type="text/template">
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <blockquote class="layui-elem-quote">
                <p>注意:1>APP微信登录配置</p>
                <p>注意:2>根据配置教程获取以下信息参数即可,入不会配置,请联系商务老师进行咨询</p>
            </blockquote>
            <div class="am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">微信配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">
                                    AppID
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="tpl-form-input" name="wx[wxapp_id]"
                                           value="{{wxapp_id}}" required placeholder="请输入开放平台部移动应用的APPID">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">
                                    AppSecret
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="tpl-form-input" name="wx[wxapp_sercret]"
                                           value="{{wxapp_sercret}}" required placeholder="请输入开放平台部移动应用的AppSecret">
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
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm({
            url:"setting"
        });
        //处理文件上传表单
        layui.use('upload', function(){
            var upload = layui.upload;
            //执行实例
            var uploadInst = upload.render({
                elem: '#uploadVerification' //绑定元素
                ,url:"/admin/upload.upload/originalUpload"//上传接口
                ,accept:'file'
                ,exts:'txt'
                ,done: function(res){
                    //上传完毕回调
                    console.log(res);
                    if(res.code == 1){
                        let HTML = '<div class="file-item">'
                             HTML+= '<img src="/assets/admin/images/file_load.png">'
                             HTML+= '<input type="hidden" name="wx[verification_file]" value="'+res.data.url+'">'
                             HTML+= '<i class="iconfont icon-shanchu file-item-delete"></i></div>'
                        $(".uploader-list").html(HTML);
                        layer.msg("核验文件上传成功");
                    }else{
                        layer.msg("核验文件上传失败");
                    }

                }
            });
        });
    });
</script>
</script>

