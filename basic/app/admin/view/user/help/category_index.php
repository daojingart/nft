<style>
    .laytable-cell-1-0-2 {
        width: 170px;
        height: auto !important;
    }
    .operation_item {
        display: inline-block;
        cursor: pointer;
        color: #2a75ed;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <ul class="layui-tab-title store_system">
        <li><a href="{{:url('user.help/index')}}">帮助列表</a></li>
        <li class="layui-this"><a href="{{:url('user.help/categoryIndex')}}" >帮助分类</a></li>
    </ul>
    <div class="layui-card-body">
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('user.help/categoryAdd') ?>">
                        新增分类
                    </a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>

</body>
<script type="text/html" id="status">
    {{#  if(d.status == 0){ }}
    <span>
           <button type="button" lay-event="status"  class="layui-btn layui-btn-xs layui-btn-danger">禁用</button>
    </span>
    {{#  } }}

    {{#  if(d.status == 1){ }}
    <span>
        <button type="button" lay-event="status"  class="layui-btn layui-btn-xs ">正常</button>
    </span>
    {{#  } }}
</script>

<script type="text/html" id="header-thumb">
    <a href="{{d.url}}"
       title="点击查看大图" target="_blank">
        <video style="width:220px;height: 150px; src="{{d.url}}"  id="demo1" controls="controls">
    </a>
</script>

<script type="text/html" id="header-thumbs">
    <a href="{{d.headImg}}"
       title="点击查看大图" target="_blank">
        <img src="{{d.headImg}}"  width="50" height="50" alt="圈子图片" >
    </a>
</script>
<script type="text/html" id="cover_time">
    {{d.cover_time[0]}}
</script>
<script>
    layui.use(['table', 'form', 'jquery','dropdown'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let dropdown = layui.dropdown;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                    width: '5%',
                },
                {
                    title: '标题',
                    field: 'title',
                    align: 'center',
                },
                {
                    title: '排序',
                    field: 'sort',
                    align: 'center',
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center',
                },

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
            url: "{{:url('user.help/categoryIndex')}}",
            page: true,
            cols: cols,
            skin: 'line',
        });

        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            table.reload('user-table', {
                where: data.field
            })
            return false;
        });

        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
            if (obj.event === 'status') {
                window.statuss(obj);
            }

        });
        window.statuss = function(obj) {
            layer.confirm('确认更改当前状态?', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('common/status')}}", {'id' : obj.data['id'],"dbname":'News'}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function(){
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);//关闭当前页
                                window.parent.location.reload();
                            }
                        );
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });

            });
        }
        //删除
        window.remove = function(obj) {
            layer.confirm('确认删除吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('common/del')}}", {'id' : obj.data['id'],"dbname":'NewsCategory'}, function(result){
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
    })
</script>