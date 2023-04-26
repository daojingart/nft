<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-row">
            <div class="layui-col-md12 layui-card-body layui-form bl-layui-left">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <input type="text" name="label_title" id="keyword" placeholder="请输入标签名称" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                </div>
            </div>
            <!--添加按钮-->

            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('user.label/add') ?>">新增标签管理</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>

</body>

<!--  账号状态 -->
<script type="text/html" id="user-enable">
    {{d.status == '1'?"启用":"禁用"}}
</script>
<!--   账号状态     -->

<!--   创建时间     -->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!--    创建时间    -->

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
                },
                {
                    title: '标签名称',
                    field: 'label_title',
                    align: 'center',
                },
                {
                    title: '标签类型',
                    field: 'label_type',
                    align: 'center'
                },
                {
                    title: '标签人数',
                    field: 'count',
                    align: 'center',
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 170
                }
            ]
        ]

        table.render({
            elem: '#user-table',
            url: "{{:url('user.label/index')}}",
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
            layer.confirm('确定要删除该标签', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.label/remove')}}", {'id' : obj.data['id']}, function(result){
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
