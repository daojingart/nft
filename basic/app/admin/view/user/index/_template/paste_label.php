<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="member[member_id]" value="{{$member_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">选择标签</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择标签</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select multiple data-am-selected name="member[label][]">
                                        {{volist name="member_label_list" id="vo"}}
                                        <option value="{{$vo.id}}"<?= in_array($vo['id'], $label_ids) ? 'selected' : '' ?>>{{$vo.label_title}}</option>
                                        {{/volist}}
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg" style="margin-left: 30px">
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
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
