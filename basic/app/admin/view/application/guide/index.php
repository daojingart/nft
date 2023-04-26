<style>
    .layui-table-main .layui-table-cell {
        height: 50px !important;
        line-height: 50px !important;
    }
    .layui-table img {
        height: 50px ;
    }
</style>

<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">

                </div>
            </form>
        </div>
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('application.guide/add') ?>">新增引导页</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>
    </div>

    <div class="layui-card-body">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>


<!--创建时间 -->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!-- 创建时间 -->

<!-- 图片替换 -->
<script type="text/html" id="thumb">
    <a href="{{d.thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.thumb}}" alt="图片">
    </a>
</script>
<!-- 图片替换 -->



<script>
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'banner_id',
                    align: 'center'
                },
                {
                    title: '图片',
                    field: 'thumb',
                    align: 'center',
                    templet: '#thumb'
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
            url: "{{:url('application.guide/index')}}",
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
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('store.banner/remove')}}", {'banner_id' : obj.data['banner_id']}, function(result){
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
