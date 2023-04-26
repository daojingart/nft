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
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">公告管理</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">公告管理</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="notice[title]"
                                           value="{{$model.title}}" placeholder="公告标题" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分类</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="notice[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择分类',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($category_list)): foreach ($category_list as $first): ?>
                                            <option value="<?= $first['category_id'] ?>" <?= $model['category_id'] == $first['category_id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">公告封面图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <div class="file-item">
                                                    <img src="{{$model.image_url?:''}}">
                                                    <input type="hidden" name="product[thumbnail]" value="{{$model.image_url?:''}}">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">内容详情 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <!-- 加载编辑器的容器 -->
                                    <textarea id="container" name="notice[content]" type="text/plain">{{$model.content}}</textarea>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">状态</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="notice[disabled]" value="10" title="正常" lay-filter="spec_type" {{$model.disabled=='10'?'checked':''}}>
                                    <input type="radio" name="nav[disabled]" value="20" title="禁用" lay-filter="spec_type" {{$model.disabled=='20'?'checked':''}}>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require"> 排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="notice[sort]"
                                           value="{{$model.sort}}" required>
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
        UE.getEditor('container');
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
        $('.upload-file').selectImages({
            name: 'notice[image_url]',
        });
    });
</script>
