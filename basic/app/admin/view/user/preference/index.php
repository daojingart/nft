<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script src="/assets/admin/amazeui.min.js"></script>
<style>
    .layui-table-main .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
    .layui-table img {
        height: 70px ;
        width: auto;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card">
        <div class="layui-card-body">

            <div class="layui-form bl-layui-left">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="goods_name" id="keyword" placeholder="藏品名称" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                </div>

                <div class="layui-row ">
                    <div class="">
                        <a class="layui-btn layui-btn-normal" href="<?= url('user.preference/add') ?>">添加优先购</a>
                    </div>
                </div>
            </div>

            <table id="user-table" lay-filter="user-table"></table>
        </div>
    </div>
    <style>
        .layui-table-cell {
            height: auto;
        }
    </style>
    <script type="text/html" id="header-thumb1">
        <img src="{{d.goods_thumb}}" style="height:60px;width: 60px;margin-right: 5px;" class="image-show">
    </script>
    <script type="text/html" id="header-thumb2">
        <img src="{{d.avatarUrl}}" style="height:60px;width: 60px;margin-right: 5px;" class="image-show">
    </script>

    <script type="text/html" id="status">
        {{#  if(d.goods_status.value == '10'){ }}
        上架
        {{# } }}
        {{#  if(d.goods_status.value == '20'){ }}
        下架
        {{# } }}
    </script>
    <script type="text/html" id="label_type">
        {{#  if(d.label_type == 1){ }}
          标签分组
        {{# } }}
        {{#  if(d.label_type == 2){ }}
         持有藏品规则
        {{# } }}
        {{#  if(d.label_type == 3){ }}
        权益叠加
        {{# } }}
    </script>
    <script type="text/html" id="operation1">
        <a  href="edit?id={{d.id}}"    class="layui-btn layui-btn-sm  layui-btn-normal">编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-sm"  lay-event="remove">删除</a>
    </script>
    <script>
        layui.use(['table', 'form', 'jquery'], function() {
            let table = layui.table;
            let form = layui.form;
            let $ = layui.jquery;
            let cols = [
                [
                    {field: 'goods_name', align: 'center',width:230,title: '藏品名称'}
                    ,{field: 'goods_thumb' ,align: 'center',title: '藏品图片',templet: '#header-thumb1'}
                    ,{field: 'label_title' ,align: 'center',title: '分组名称'},
                    ,{field: 'label_type' ,align: 'center',title: '规则类型',templet: '#label_type'},
                    {
                        title: '操作',
                        field: 'operate',
                        align: 'center',
                        width: 130
                    }
                ]
            ]

            table.render({
                elem: '#user-table',
                url: "{{:url('user.preference/index')}}", //模拟接口
                page: true,
                cols: cols,
                skin:'line'
            });


            //搜索条件查询操作
            form.on('submit(user-query)', function(data) {
                table.reload('user-table', {
                    where: data.field,
                    page: {
                        curr:1,
                    },
                })
                return false;
            });

            //监听操作
            table.on('tool(user-table)', function(obj) {
                //监听到事件的删除操作
                var $this = $(this);
                if (obj.event === 'audit') {
                    var data =  $this.data();
                    window.audit(obj,data);
                }
                if (obj.event === 'pass') {
                    window.pass(obj);
                }
                if (obj.event === 'reject') {
                    window.reject(obj);
                }
                if (obj.event === 'remove') {
                    window.remove(obj);
                }
            });
            window.remove = function(obj) {
                layer.confirm('确认删除该优先购吗？', {
                    icon: 3,
                    title: '提示'
                }, function(index) {
                    layer.close(index);
                    let loading = layer.load();
                    $.getJSON("{{:url('common/del')}}", {'id' : obj.data['id'],'dbname':'GoodsPrecedence'}, function(result){
                        layer.close(loading);
                        if(1 === result.code){
                            layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function() {
                                obj.del();
                            });
                        }else{
                            layer.msg(result.msg, {
                                icon: 2,
                                time: 1000
                            });
                        }
                    });

                });
            }
            window.reject = function (data) {
                var id = data.data.goods_id
                var url = "<?= url('goods/up_goodsStatus') ?>";
                layer.confirm('确认要上架吗？',function(index){
                    $.post(url,{'goods_id':id,'goods_status':10},function(data){
                        if(data.code == 1){
                            layer.msg(data.msg,{icon:1,time:1000});
                            setTimeout(function(){parent.window.location.reload();},2000);
                        }else{
                            layer.msg(data.msg,{icon:5,time:1000});
                        }
                    });
                });
            }
            window.pass = function (data) {
                var id = data.data.id
                var url = "<?= url('goods.comment/up_status') ?>";
                layer.confirm('确认更改状态吗？', function (index) {
                    $.post(url, {'id': id}, function (data) {
                        if (data.code == 1) {
                            layer.msg(data.msg, {icon: 1, time: 1000});
                            setTimeout(function () {
                                parent.window.location.reload();
                            }, 2000);
                        } else {
                            layer.msg(data.msg, {icon: 5, time: 1000});
                        }
                    });
                });
            }
        })
    </script>
</body>