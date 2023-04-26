<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.all.js"></script>

<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">奖品设置</div>
                            </div>


                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖品类型</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="type" value="1" title="红包"  lay-filter="goods_type"	 checked>
                                    <input type="radio" name="type" value="2" title="藏品"  lay-filter="goods_type">
                                    <input type="radio" name="type" value="3" title="产品"  lay-filter="goods_type">
                                    <input type="radio" name="type" value="4" title="空奖"  lay-filter="goods_type">
                                </div>
                            </div>



                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖品名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="name" value="" placeholder="请输入奖品名称" required>
                                </div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖品等级 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="level" value="0" title="普通"   checked>
                                    <input type="radio" name="level" value="1" title="一等奖" >
                                    <input type="radio" name="level" value="2" title="二等奖" >
                                    <input type="radio" name="level" value="3" title="三等奖">
                                </div>
                            </div>








                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">中奖几率(权重) </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="probability" value="" placeholder="请输入中奖权重" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖品库存 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="stock" value="" placeholder="请输入奖品库存" required>
                                </div>
                            </div>


                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖品图片 </label>
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
                                            <small>尺寸750x750像素以上</small>
                                        </div>
                                    </div>
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

        layui.code();
    });

    $(function () {
        // 选择图片
        $('.upload-file').selectImages({
            name: 'image',
        });
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
