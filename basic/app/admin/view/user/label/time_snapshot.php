<link rel="stylesheet" href="/assets/admin/css/pear.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<script>
    BASE_URL = '<?= isset($base_url) ? $base_url : '' ?>';
    STORE_URL = '<?= isset($store_url) ? $store_url : '' ?>';
</script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.all.js"></script>
<style>
    .am-form .am-form-file .upload-file1{
        font-size: 1.24rem;
        padding: 0.6em 1em;
    }
    .edu-paid-trainee{
        display: none;
    }
    .layui-form-radio:hover *, .layui-form-radioed, .layui-form-radioed>i {
        color: #1e9fff;
    }
    .individually-paid-trainee{
        display: none;
    }
    .consignment-paid-trainee{
        display: none;
    }
    .limit_consignment_open_content{
        display: none;
    }
</style>
<div class="am-cf"  style="overflow-x: hidden">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="goods[product_types]" value="5">
                    <input type="hidden" name="goods[blindbox_id]" value="{{$blindbox_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本信息</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择的藏品 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="goods[writer_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属作者',maxHeight:'200px'}">
                                        <option value=""></option>
										<?php if (isset($writer)): foreach ($writer as $first): ?>
                                            <option value="<?= $first['id'] ?>"><?= $first['name'] ?></option>
										<?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">筛选条件</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[is_open_consignment]" value="10" title="手动设置" lay-filter="is_open_consignment" checked>
                                    <input type="radio" name="goods[is_open_consignment]" value="20" title="藏品叠加" lay-filter="is_open_consignment">
                                    <small>手动设置;则本次添加的会员购买次数是一样的;藏品地接</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设定次数 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" maxlength="6" class="tpl-form-input" name="goods[goods_no]" value="" placeholder="请输入作品编码" required>
                                    <small>作品唯一性</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">提前购买时间</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" maxlength="6" class="tpl-form-input" name="goods[goods_no]" value="" placeholder="请输入作品编码" required>
                                    <small>作品唯一性</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
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

<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}

<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>
<script src="/assets/resource/vue.js"></script>
<script src="/assets/resource/axios.min.js"></script>
<script src="/assets/resource/vod-js-sdk-v6.js"></script>
<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form
            element = layui.element;
        let laydate = layui.laydate;

        //  时间
        laydate.render({
            elem: '#start_time' //指定元素
            ,type: 'datetime'
            // ,format: 'HH:mm'
        });

        // 藏品方式
        form.on('radio(goods_type)', function(data){
            var $eduPaidMany = $('.edu-paid-many');
            var $eduPaidTrainee = $('.edu-paid-trainee');

            console.log(data.value);

            switch (data.value)
            {
                case "1":
                    $eduPaidMany.show() && $eduPaidTrainee.hide();
                    break;
                case "2":
                    $eduPaidMany.hide() && $eduPaidTrainee.show();
                    break;
                case "3":
                    $eduPaidMany.hide() && $eduPaidTrainee.hide();
                    break;
            }
        });

        //是否开启转增
        form.on('radio(is_open_increase)', function(data){
            var $eduPaidMany = $('.individually-paid-trainee');
            switch (data.value)
            {
                case "20":
                    $eduPaidMany.show();
                    break;
                case "10":
                    $eduPaidMany.hide()
                    break;
                case "30":
                    $eduPaidMany.hide()
                    break;
            }
        });

        //是否开启寄售
        form.on('radio(is_open_consignment)', function(data){
            var $eduPaidMany = $('.consignment-paid-trainee');
            switch (data.value)
            {
                case "20":
                    $eduPaidMany.show();
                    break;
                case "10":
                    $eduPaidMany.hide()
                    break;
                case "30":
                    $eduPaidMany.hide()
                    break;
            }
        });

        //价格限制
        form.on('radio(limit_consignment_open)', function(data){
            var $eduPaidMany = $('.limit_consignment_open_content');
            switch (data.value)
            {
                case "10":
                    $eduPaidMany.hide()
                    break;
                case "20":
                    $eduPaidMany.show();
                    break;
            }
        });

        layui.code();
    });

    $(function () {

        // 富文本编辑器
        UE.getEditor('container');

        // 选择图片
        $('.upload-file').selectImages({
            name: 'goods[goods_thumb]',
        });

        // 选择图片
        $('.upload-file1').selectImages({
            name: 'goods[d_images]',
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').blAjaxSubmit();


        /**
         * 计算签名。
         **/
        function getSignature() {
            return axios.post("{{:url('common/getSignature')}}", JSON.stringify({
                "Action": "GetUgcUploadSign"
            })).then(function (response) {
                return response.data;
            })
        }

        /**
         * 获取资源元信息
         * @param FileId
         * @returns {*}
         */
        function getDescribeMediaInfos(FileId) {
            return axios.post("{{:url('common/getDescribeMediaInfos')}}",{
                'FileId':FileId
            }).then(function (response) {
                $(".duration_length").val(response.data);
            })
        }

        // 上传视频
        let app = new Vue({
            el: '#main-video',
            data: {
                uploaderInfos: [],
                vcExampleVideoName: '',
                vcExampleCoverName: '',
                cExampleFileId: '',
                videoName:'',
            },
            created: function () {
                this.tcVod = new TcVod.default({
                    getSignature: getSignature
                })
            },
            methods: {
                /**
                 * 添加视频
                 **/
                vExampleAdd: function () {
                    this.$refs.vExampleFile.click();
                },
                /**
                 * 传视频过程。
                 **/
                vExampleUpload: function () {
                    var self = this;
                    var mediaFile = this.$refs.vExampleFile.files[0];
                    var arr_type = ['video/mp4']; //判断上传的类型是否符合播放的标准
                    if(arr_type.indexOf(mediaFile.type) == -1){
                        layer.alert("请先上传视频文件");return;
                    }
                    element.progress('demo', '0%');
                    var uploader = this.tcVod.upload({
                        mediaFile: mediaFile,
                    });
                    $(".note_controller").show();
                    //视频上传进度
                    uploader.on('media_progress', function (info) {
                        // console.log(info.percent);
                        let lenght = (info.percent)*100;
                        element.progress('demo', lenght + '%');
                    });
                    // console.log(uploader, 'uploader')
                    var uploaderInfo = {
                        videoInfo: uploader.videoInfo,
                        isVideoUploadSuccess: false,
                        isVideoUploadCancel: false,
                        progress: 0,
                        fileId: '',
                        videoUrl: '',
                        cancel: function() {
                            uploaderInfo.isVideoUploadCancel = true;
                            uploader.cancel()
                        },
                    };
                    this.uploaderInfos.push(uploaderInfo);
                    uploader.done().then(function(doneResult) {
                        //上传成功获取上传之后的视频或者音频资源文件
                        getDescribeMediaInfos(doneResult.fileId);
                        uploaderInfo.fileId = doneResult.fileId;
                        var  url_link = doneResult.video.url;  //播放视频音频的链接
                        $(".video_link_url").val(url_link);
                    }).then(function (videoUrl) {
                    })
                },

                setVcExampleVideoName: function () {
                    this.vcExampleVideoName = this.$refs.vcExampleVideo.files[0].name;

                },
                setVcExampleCoverName: function () {
                    this.vcExampleCoverName = this.$refs.vcExampleCover.files[0].name;
                },
            },
        })

        // 上传音频
        let apps = new Vue({
            el: '#main-audio',
            data: {
                uploaderInfos: [],
                vcExampleVideoName: '',
                vcExampleCoverName: '',
                cExampleFileId: '',
                videoName:'',
            },
            created: function () {
                this.tcVod = new TcVod.default({
                    getSignature: getSignature
                })
            },
            methods: {
                /**
                 * 添加视频
                 **/
                vExampleAdd: function () {
                    this.$refs.vExampleFile.click();
                },
                /**
                 * 传视频过程。
                 **/
                vExampleUpload: function () {
                    var self = this;
                    var mediaFile = this.$refs.vExampleFile.files[0];
                    var arr_type = ['audio/mp3','audio/mpeg']; //判断上传的类型是否符合播放的标准
                    if(arr_type.indexOf(mediaFile.type) == -1){
                        layer.alert("请上传mp3文件");return;
                    }
                    element.progress('demo', '0%');
                    var uploader = this.tcVod.upload({
                        mediaFile: mediaFile,
                    });
                    $(".note_controller").show();
                    //视频上传进度
                    uploader.on('media_progress', function (info) {
                        // console.log(info.percent);
                        let lenght = (info.percent)*100;
                        element.progress('demo', lenght + '%');
                    });
                    // console.log(uploader, 'uploader')
                    var uploaderInfo = {
                        videoInfo: uploader.videoInfo,
                        isVideoUploadSuccess: false,
                        isVideoUploadCancel: false,
                        progress: 0,
                        fileId: '',
                        videoUrl: '',
                        cancel: function() {
                            uploaderInfo.isVideoUploadCancel = true;
                            uploader.cancel()
                        },
                    };
                    this.uploaderInfos.push(uploaderInfo);
                    uploader.done().then(function(doneResult) {
                        //上传成功获取上传之后的视频或者音频资源文件
                        getDescribeMediaInfos(doneResult.fileId);

                        uploaderInfo.fileId = doneResult.fileId;
                        var  url_link = doneResult.video.url;  //播放视频音频的链接
                        $(".audio_link_url").val(url_link);
                    }).then(function (videoUrl) {
                    })
                },

                setVcExampleVideoName: function () {
                    this.vcExampleVideoName = this.$refs.vcExampleVideo.files[0].name;
                },
                setVcExampleCoverName: function () {
                    this.vcExampleCoverName = this.$refs.vcExampleCover.files[0].name;
                },
            },
        })

    });
</script>
