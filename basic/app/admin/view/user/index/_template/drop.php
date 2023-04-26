<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<link rel="stylesheet" href="/assets/admin/css/goods.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<link rel="stylesheet" href="/assets/admin/layui/css/layui.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<style>
    .layui-table-view .layui-form-radio {
        padding-top: 10px;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" enctype="multipart/form-data" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择商品</label>
                                <div class="am-u-sm-4 am-u-end">
                                    <input type="hidden" id="goods_id" name="precedence[goods_id]" value="">
                                    <input type="text" name="" id="goods_name" disabled readonly value="">
                                    <i class="layui-icon layui-icon-addition addMember" style="font-size: 30px; color: #1E9FFF;"></i>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-5 am-u-sm-push-6 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交

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
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var laydate = layui.laydate;

    });


    $(document).on("click",".addMember",function(){
        layer.open({
            type: 2,
            title: '选择优先购藏品',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('goods')}}"
        });
    })
    $(document).on("click",".addMembers",function(){
        layer.open({
            type: 2,
            title: '选择优先购标签分组',
            shade: 0.1,
            btn:["确定","取消"],
            offset: 'auto',
            area: ['1020px','670px'],
            content: "{{:url('label')}}"
        });
    })

    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
