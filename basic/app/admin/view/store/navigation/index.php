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
        <blockquote class="layui-elem-quote">
            <p>注意:1>导航为一排5个,导航链接填写带http或者https 为外链协议，内链请在浏览器复制</p>
            <p>注意:2>内链配置格式：复制浏览器网址 # 后面的全部填写到链接处</p>
        </blockquote>
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('store.navigation/add') ?>">新增导航</a>
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
    <a href="{{d.icon_image}}" title="点击查看大图" target="_blank">
        <img src="{{d.icon_image}}" alt="图片">
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
                    title: '标题',
                    field: 'title',
                    align: 'center'
                },
                {
                    title: '图片',
                    field: 'thumb',
                    align: 'center',
                    templet: '#thumb'
                },
                {
                    title: '跳转链接',
                    field: 'link_url',
                    align: 'center',
                },
                {
                    title: '状态',
                    field: 'disabled',
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
            url: "{{:url('store.navigation/index')}}",
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
                $.getJSON("{{:url('store.navigation/remove')}}", {'id' : obj.data['id']}, function(result){
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
