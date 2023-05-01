<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.all.js"></script>
<style>
    .edui-body-container{
        min-height: 400px;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="notice[type]" value="{{$type_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">{{$type_id=='1'?'公告管理':"公告管理"}}</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">
                                    {{$type_id=='1'?'标题':"标题"}}
                                </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="notice[title]"
                                           value="" placeholder="{{$type_id=='1'?'请输入标题':"请输入标题"}}" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分类</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="notice[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择分类',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($category_list)): foreach ($category_list as $first): ?>
                                            <option value="<?= $first['category_id'] ?>"><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">公告封面图</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">内容详情 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <!-- 加载编辑器的容器 -->
                                    <textarea id="container" name="notice[content]" type="text/plain"></textarea>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">状态</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="notice[disabled]" value="10" title="正常" lay-filter="spec_type" checked>
                                    <input type="radio" name="notice[disabled]" value="20" title="禁用" lay-filter="spec_type">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="notice[sort]"
                                           value="100" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 作者 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="notice[author]"
                                           value="平台" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-u-lg-offset-2 am-margin-top-lg">
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
<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}
{{include file="layouts/_template/spec_many" /}}
<script>
    $(function () {
        // 富文本编辑器
        UE.getEditor('container');
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
        // 选择图片
        $('.upload-file').selectImages({
            name: 'notice[image_url]',
        });
    });
</script>
