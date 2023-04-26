<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<style>
    .collection_list{
        display: none;
    }
</style>
<style>
    .chose-class-wrap{
        position: relative;
        margin-top: 10px;
    }
    .assort-class-wrap {
        padding: 8px 0 8px 18px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        width: 640px;
        height: 92px;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        background: rgba(245,247,250,0.5);
    }
    .assort-class-img {
        height: 60px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
    }
    .assort-class-img img {
        max-width: 100%;
    }
    .class-title-wrap {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        height: 60px;
        margin: 0 10px;
    }
    .class-title {
        color: #353535;
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        max-width: 430px;
        white-space: nowrap;
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .class-type {
        margin-bottom: auto;
        font-size: 12px;
        color: #666;
    }
    .change-class-btn {
        width: 64px;
        height: 28px;
        margin-left: 10px;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        border: solid 1px #e5e7eb;
        line-height: 26px;
        text-align: center;
        font-size: 12px;
        color: #666;
        cursor: pointer;
        margin-right: 19px;
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
                                <div class="widget-title am-fl">添加标签</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">标签标题 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="label[label_title]" value="" placeholder="请输入标签标题" required>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">标签类型</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="label[label_type]" value="1" title="手动标签" checked lay-filter="label_type">
                                    <input type="radio" name="label[label_type]" value="2" title="指定藏品" lay-filter="label_type">
                                </div>
                            </div>
                            <div class="am-form-group collection_list" id="collection_list">
                                <div class="am-form-group layui-form">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">选择藏品</label>
                                    <div class="am-u-sm-9 am-u-end" id="course_info">
                                        <input type="hidden" id="goods_id" class="layui-input layui-disabled" name="label[goods_id]"  value="">
                                        <i class="layui-btn layui-btn-normal addMembers" id="label_none">
                                            <i class="layui-icon layui-icon-add-1"></i>
                                            选择藏品
                                        </i>
                                        <div style="display: none" id="goods_show" class="chose-class-wrap"><div class="assort-class-wrap">
                                                <img src="" id="goods_thumb" class="assort-class-img">
                                                <div class="class-title-wrap">
                                                    <p class="class-title" id="goods_name" ></p>
                                                </div>
                                                <p class="change-class-btn addMembers">更换藏品</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
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
<script src="/assets/admin/amazeui.min.js"></script>
<script>
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;

        //是否开启转增
        form.on('radio(label_type)', function(data){
            var $eduPaidMany = $('.collection_list');
            switch (data.value)
            {
                case "2":
                    $eduPaidMany.show();
                    break;
                case "1":
                    $eduPaidMany.hide()
                    break;
            }
        });
        layui.code();
    });
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

        /**
         * 选择藏品 持有自动标签功能
         */
        $(document).on("click",".addMembers",function(){
            layer.open({
                type: 2,
                title: '选择藏品',
                shade: 0.1,
                btn:["确定","取消"],
                offset: 'auto',
                area: ['1020px','670px'],
                content: "{{:url('goods')}}?types=kt"
            });
        })

    });
</script>

