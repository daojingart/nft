<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/modules/umeditor/themes/default/css/umeditor.css">

<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="period[id]" value="{{$model.id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">编辑往期</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">标题 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="period[title]" value="{{$model.title}}" placeholder="请输入标题" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">副标题</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="period[desc]" value="{{$model.desc}}" placeholder="请输入副标题" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">封面图</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                {{if isset($model['image'])}}
                                                    <div class="file-item">
                                                        <img src="<?= $model['image'] ?>">
                                                        <input type="hidden" name="period[image]" value="<?= $model['image'] ?>">
                                                        <i class="iconfont icon-shanchu file-item-delete"></i>
                                                    </div>
                                                {{/if}}
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸690X280像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">是否展示作品 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="period[disabled]" value="0" title="显示" {{if $model.disabled == 0}} checked {{/if}}>
                                    <input type="radio" name="period[disabled]" value="1" title="隐藏" {{if $model.disabled == 1}} checked {{/if}}>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">内容 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea id="container" name="period[content]" type="text/plain">{{$model.content}}</textarea>
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

<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        layui.code();
    });
    $(function () {

        UM.getEditor('container');

        // 选择图片
        $('.upload-file').selectImages({
            name: 'period[image]',
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
