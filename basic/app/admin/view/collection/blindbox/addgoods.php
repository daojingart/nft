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
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="goods[goods_name]" value="" placeholder="请输入作品名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品展示图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸750x750像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情图片格式</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[is_suffix]" value="10" title="gif动态图片" checked>
                                    <input type="radio" name="goods[is_suffix]" value="20" title="GLB3D展示" >
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情3D图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file1 am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸750x750像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属作者 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="goods[writer_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属作者',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($writer)): foreach ($writer as $first): ?>
                                            <option value="<?= $first['id'] ?>"><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分组 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="goods[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属分组',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($category)): foreach ($category as $first): ?>
                                            <option value="<?= $first['category_id'] ?>"><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="am-margin-left-xs">
                                        <a href="<?= url('collection.category/add') ?>">去添加</a>
                                    </small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品类型</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[goods_type]" value="1" title="视频"  lay-filter="goods_type"	 checked>
                                    <input type="radio" name="goods[goods_type]" value="3" title="图片"  lay-filter="goods_type">
                                </div>
                            </div>
                            <!-- 视频 -->
                            <div class="am-form-group  edu-paid-many" id="main-video">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">视频内容</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button"  @click="vExampleAdd" class="upload-file am-btn am-btn-secondary am-radius">
                                                上传视频
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <input type="text" class="tpl-form-input video_link_url" name="goods[video_link_url]" value="" required>
                                                <input type="file" style="display:none;" ref="vExampleFile" @change="vExampleUpload" />
                                            </div>
                                        </div>
                                        <div class="note_controller" style="display: none;margin-top: 10px;" >
                                            <div class="layui-progress layui-progress-big" lay-showPercent="true" lay-filter="demo">
                                                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>上传文件前,请先前往-->系统管理-->视频存储处配置点播配置；大文件上传比较慢,请耐心等待</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 视频 -->

                            <!-- 音频 -->
                            <div class="am-form-group edu-paid-trainee" id="main-audio">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">音频内容</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button"  @click="vExampleAdd" class="upload-file am-btn am-btn-secondary am-radius">
                                                上传音频
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <input type="text" class="tpl-form-input audio_link_url" name="goods[audio_link_url]" value="" required>
                                                <input type="file" style="display:none;" ref="vExampleFile" @change="vExampleUpload" />
                                            </div>
                                        </div>
                                        <div class="note_controller" style="display: none;margin-top: 10px;" >
                                            <div class="layui-progress layui-progress-big" lay-showPercent="true" lay-filter="demo">
                                                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>上传文件前,请先前往-->系统管理-->视频存储处配置点播配置；大文件上传比较慢,请耐心等待</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 音频 -->

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">购买配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品编码 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" maxlength="6" class="tpl-form-input" name="goods[goods_no]" value="" placeholder="请输入作品编码" required>
                                    <small>作品唯一性</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行数量</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="goods[original_number]" value="" placeholder="请输入发行数量" required>
                                    <small>发行数量一旦确定后不可更改;用于生成作品编号的最后使用规则</small>
                                </div>
                            </div>
                            <input type="hidden" id="start_time" name="goods[start_time]" placeholder="请输入开售时间" class="layui-input" value="<?=date("Y-m-d H:i:s",time())?>">

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品售价 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[goods_price]" placeholder="请输入作品售价" class="layui-input" value="">
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">盲盒配置</div>
                            </div>
                            {{if $open_box_type == 10}}
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">概率配置 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[probability]" placeholder="请输入1-100 的概率" class="layui-input" value="">
                                </div>
                            </div>
                            {{/if}}
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">库存数量</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[stock_num]" placeholder="请输入库存数量" class="layui-input" value="">
                                    <small>请合理设置库存、库存和概率要做适当匹配</small>
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">回购配置</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">回收状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[recovery_status]" value="0" title="否" checked>
                                    <input type="radio" name="goods[recovery_status]" value="1" title="是">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">回收数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[recovery_num]" placeholder="请输入回收数量" class="layui-input" value="">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">回收价格 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[recovery_price]" placeholder="请输入回收价格" class="layui-input" value="">
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">作品配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行方 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="goods[issue_name]" placeholder="请输入发行方" class="layui-input" value="">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行标签 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="goods[issue_tag]" placeholder="请输入发行标签" class="layui-input" value="">
                                    <small>","隔开 表示多个标签</small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">转增时间设置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[is_open_increase]" value="10" title="系统配置" lay-filter="is_open_increase" checked>
                                    <input type="radio" name="goods[is_open_increase]" value="20" title="单独设置" lay-filter="is_open_increase">
                                    <input type="radio" name="goods[is_open_increase]" value="30" title="关闭转增" lay-filter="is_open_increase">
                                    <small>根据不同的藏品单独设置转增时间 或者直接关闭转增</small>
                                </div>
                            </div>
                            <!-- 转增 -->
                            <div class="am-form-group individually-paid-trainee">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设置转增时间</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="goods[increas_minute]" placeholder="请输入转增时间(分钟)" class="layui-input" value="">
                                    </div>
                                </div>
                            </div>
                            <!-- 转增 -->

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售时间设置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[is_open_consignment]" value="10" title="系统配置" lay-filter="is_open_consignment" checked>
                                    <input type="radio" name="goods[is_open_consignment]" value="20" title="单独设置" lay-filter="is_open_consignment">
                                    <input type="radio" name="goods[is_open_consignment]" value="30" title="关闭寄售" lay-filter="is_open_consignment">
                                    <small>根据不同的藏品单独设置寄售时间 或者直接关闭寄售</small>
                                </div>
                            </div>
                            <!-- 转增 -->
                            <div class="am-form-group consignment-paid-trainee">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设置转增时间</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="goods[consignment_minute]" placeholder="请输入寄售时间(分钟)" class="layui-input" value="">
                                    </div>
                                </div>
                            </div>
                            <!-- 转增 -->

                            <!-- 限制价格 -->
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售价格限制配置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[limit_consignment_open]" value="10" title="系统配置" lay-filter="limit_consignment_open" checked>
                                    <input type="radio" name="goods[limit_consignment_open]" value="20" title="单独设置" lay-filter="limit_consignment_open">
                                    <small>可以针对单个藏品设置最高价和最低价</small>
                                </div>
                            </div>
                            <div class="am-form-group limit_consignment_open_content">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最高价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="goods[top_price_limit]" placeholder="请输入固定的限制最高价格" class="layui-input" value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最低价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="goods[minimum_consignment]" placeholder="请输入固定的最低限制价格" class="layui-input" value="">
                                    </div>
                                </div>
                            </div>
                            <!-- 限制价格 -->


                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">作品详情</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">作品详情 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea id="container" name="goods[content]" type="text/plain"></textarea>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="goods[goods_sort]" placeholder="请输入排序值" class="layui-input" value="">
                                </div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="goods[goods_status]" value="10" title="显示" checked>
                                    <input type="radio" name="goods[goods_status]" value="20" title="隐藏">
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
