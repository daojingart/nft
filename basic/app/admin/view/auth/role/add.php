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
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加角色管理</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">角色名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="role[role_name]"
                                           value="" placeholder="请输入角色名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">选择权限节点</label>
                                <div class="am-u-sm-9 am-u-end">
                                    {{volist name="node_list" id="vo"}}
                                    <div class="bl-layui-card layui-form">
                                        <div class="layui-card-header">
                                            <input lay-filter="encrypt" value="{{$vo.id}}" type="checkbox" name="role[rule][]" title="{{$vo.node_name}}" lay-skin="primary">
                                        </div>
                                        {{if isset($vo['submenu'])}}
                                        <div class="layui-card-body layui-row layui-col-space10" style="margin-left: 25px">
                                            <div class="layui-col-md12">
                                                {{volist name="$vo['submenu']" id="v"}}
                                                <input class="checknode{{$vo.id}}" lay-filter="encrypt" value="{{$v.id}}" type="checkbox" name="role[rule][]" title="{{$v.node_name}}" lay-skin="primary">
                                                {{if isset($v['submenu'])}}
                                                    <div class="layui-card-body layui-row layui-col-space10" style="margin-left: 25px">
                                                        <div class="layui-col-md12">
                                                            {{volist name="$v['submenu']" id="v1"}}
                                                            <input class="checknode{{$vo.id}} checknode{{$v.id}}" value="{{$v1.id}}" type="checkbox" name="role[rule][]" title="{{$v1.node_name}}" lay-skin="primary">
                                                            {{/volist}}
                                                        </div>
                                                    </div>
                                                {{/if}}
                                                {{/volist}}
                                            </div>
                                        </div>
                                        {{/if}}
                                    </div>
                                    {{/volist}}
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
    layui.use(['form','element'], function() {
        var form = layui.form;
        form.on('checkbox(encrypt)', function(data){
            let is_checked = data.elem.checked;
            let father_value = data.value;
            let class_name = ".checknode"+father_value;
            console.log(class_name);
            if(is_checked === true){
                $(class_name).prop("checked", true);
            }else{
                $(class_name).prop("checked", false);
            }
            form.render('checkbox');
        });
    });
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();
    });
</script>
