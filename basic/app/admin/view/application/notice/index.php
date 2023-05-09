<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md9 bl-layui-left">
                    <a class="layui-btn layui-btn-normal"
                       href="<?= url('application.notice/add',['type'=>'2']) ?>">
                        <i class="layui-icon layui-icon-add-1"></i>
                        添加公告
                    </a>
                </div>
            </div>
            <!--添加按钮-->
        </div>
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>

<script>
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: '标题',
                    field: 'title',
                    align: 'center',
                },
                {
                    title: '公告分类',
                    field: 'category_name',
                    align: 'center',
                },
                {
                    title: '排序',
                    field: 'sort',
                    align: 'center',
                },

                {
                    title: '添加时间',
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
            url: "{{:url('application.notice/index')}}",
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
            }else if(obj.event === 'copy'){
                window.copy(obj);
            }
        });
        //删除
        window.remove = function(obj) {
            layer.confirm('确认删除这个内容吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('application.notice/remove')}}", {'id' : obj.data['id']}, function(result){
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
        window.copy = function(obj) {
            $link_url = '/pagesA/index/realDet?id='+obj.data['id'];
            layer.alert($link_url,{title:'站内链接地址'});
        }
    })
</script>