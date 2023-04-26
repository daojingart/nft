<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<style>
    .uploader-list .file-item img {
        width: 200px;
        height: auto;
    }
    .uploader-list .file-item {
        float: left;
        width: 210px;
        position: relative;
        margin: 20px 25px 0 0;
        padding: 4px;
        border: 1px solid #ddd;
        background: #fff;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="message[member_id]" value="{{$member_id}}">
                    <input type="hidden" name="message[type]" value="1">

                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加消息通知</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3  am-u-lg-2 am-form-label form-require">通知内容</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea rows="5" name="message[content]"
                                              placeholder="通知内容"></textarea>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg am-u-lg-offset-3">
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
