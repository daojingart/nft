<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<style>
    .layui-form-checked span, .layui-form-checked:hover span {
        background-color: #219fff00 !important
    }
    .layui-form-checked[lay-skin=primary] i {
        border-color: #158efb!important;
        background-color: #ffb80000;
    }
    .layui-form-checkbox[lay-skin=primary]:hover i {
        border-color: #158efb!important;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="award_setting_id" value="{{$info.award_setting_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">编辑奖励设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖项名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="name"
                                           value="{{$info.name}}" placeholder="请输入奖项名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖项说明 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="explain"
                                           value="{{$info.explain}}" placeholder="请输入奖项说明" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">邀请人数 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="invite_num"
                                           value="{{$info.invite_num}}" placeholder="请输入邀请人数" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">奖励荣誉值 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="honor_num"
                                           value="{{$info.honor_num}}" placeholder="请输入奖励荣誉值" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交保存
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
<script src="/assets/admin/amazeui.min.js"></script>
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
