<script  id="wx_system" type="text/template">
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <blockquote class="layui-elem-quote">
                <p>注意:1>微信公众号配置,<a style="color: #da0909;font-size: 20px;" target="_blank" href="https://e0fc39.baklib-free.com/6fff">点击直达教程</a></p>
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
                                    <input type="text" class="tpl-form-input" name="wx[wx_app_id]"
                                           value="{{wx_app_id}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">
                                    AppSecret
                                </label>
                                <div class="am-u-sm-9">
                                    <input type="text" class="tpl-form-input" name="wx[wx_app_secret]"
                                           value="{{wx_app_secret}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3  am-form-label form-require">核验文件</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button id="uploadVerification" type="button"
                                                    class="am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择文件
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <div class="file-item" style="display: {{verification_file_status?'inline':'none'}} ">
                                                    <img src="/assets/admin/images/file_load.png">
                                                    <input type="hidden" name="wx[verification_file]" value="{{verification_file}}">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>核验文件是为了配置JS域名使用,请勿上传其他核验文件,或者重复上传</small>
                                        </div>
                                    </div>
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
                ,url:"{{:url('upload.upload/originalUpload')}}"//上传接口
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

