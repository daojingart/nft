<link rel="stylesheet" href="/assets/admin/css/pear.css" />
<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<style>
    .layui-form-radio>i:hover, .layui-form-radioed>i {
        color: #e6a602;
    }
    .layui-form-radio:hover *, .layui-form-radioed, .layui-form-radioed>i {
        color: #e6a60a;
    }
</style>
<div class="row-content am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">添加{{$info.node_name}}管理</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">节点名称 </label>
                                <div class="am-u-sm-9 am-input-group">
                                    <input type="text" class="tpl-form-input" name="node[node_name]"
                                           value="" placeholder="请输入节点名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属节点 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="node[p_id]"   data-am-selected="{ btnSize: 'sm'}">>
                                        <option value="{{$info.id}}">{{$info.node_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">控制器名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="node[controller_name]"
                                           value="" placeholder="请输入控制器名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">方法名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="node[action_name]"
                                           value="" placeholder="请输入方法名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">节点图标 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="node[menu_icon]"
                                           value="" placeholder="请输入节点图标">
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">是否显示节点</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="node[disabled]" value="1" title="显示" checked>
                                    <input type="radio" name="node[disabled]" value="0" title="隐藏">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="node[sort]"
                                           value="" placeholder="请输入排序序号;正序排列;数字越小越在前">

                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">确认提交
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

<script type="text/javascript">
    layui.use(['form','element','code'], function() {
        var form = layui.form;
        var element = layui.element;
        layui.code();
    });
    var index = '';
    function showStart(){
        index = layer.load(0, {shade: false});
        return true;
    }
    function showSuccess(res){
        layer.ready(function(){
            layer.close(index);
            if(1 == res.code){
                layer.alert(res.msg, {title: '友情提示', icon: 1, closeBtn: 0}, function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layer.close(index);//关闭当前页
                    window.parent.location.reload();
                });
            }else if(111 == res.code){
                window.location.reload();
            }else{
                layer.msg(res.msg, {anim: 6});
            }
        });
    }

    $(document).ready(function(){
        // 添加节点控制器
        var options = {
            beforeSubmit:showStart,
            success:showSuccess
        };

        $('#my-form').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });
    });
</script>

