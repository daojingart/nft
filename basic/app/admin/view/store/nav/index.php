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
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" style="display:{{if $count>=5}} none {{/if}} "  href="<?= url('store.nav/add') ?>">新增导航</a>
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
    <a href="{{d.icon}}" title="点击查看大图" target="_blank">
        <img src="{{d.icon}}" alt="图片">
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
                    field: 'id',
                    align: 'center'
                },
                {
                    title: '底部导航名称',
                    field: 'name',
                    align: 'center'
                },
                {
                    title: '图标',
                    field: 'icon',
                    align: 'center',
                    templet: '#thumb'
                },
                {
                    title: '跳转链接',
                    field: 'url',
                    align: 'center',
                },
                {
                    title: '状态',
                    field: 'status',
                    align: 'center',
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
            url: "{{:url('store.nav/index')}}",
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

    })
</script>
