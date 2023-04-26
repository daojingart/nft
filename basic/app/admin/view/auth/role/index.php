<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal"
                       href="<?= url('auth.role/add') ?>">
                        新增角色
                    </a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
<!--        账号状态-->
<script type="text/html" id="user-enable">
    {{d.disabled == '1'?"启用":"禁用"}}
</script>
<!--        账号状态-->

<!--        创建时间-->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!--        创建时间-->

<script>
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                    width: 50
                },
                {
                    title: '角色名称',
                    field: 'role_name',
                    align: 'center',
                    width: 130
                },
                {
                    title: '状态',
                    field: 'disabled',
                    align: 'center',
                    templet: '#user-enable'
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center',
                    templet: '#user-createTime',
                    width: 160
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
            url: "{{:url('auth.role/index')}}",
            page: true,
            cols: cols,
            skin: 'line'
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
        });
        //删除
        window.remove = function(obj) {
            layer.confirm('确定要删除该角色吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('auth.role/remove')}}", {'id' : obj.data['id']}, function(result){
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
</body>