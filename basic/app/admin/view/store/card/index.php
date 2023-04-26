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
                    <a class="layui-btn layui-btn-normal" href="<?= url('store.card/add') ?>">新增银行</a>
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
<div style="background: {{d.color}}">
    {{d.color}}
</div>
</script>
<!-- 创建时间 -->

<!-- 图片替换 -->
<script type="text/html" id="thumb">
    <a href="{{d.thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.thumb}}" alt="图片">
    </a>
</script>
<!-- 图片替换 -->

<!--创建时间 -->
<script type="text/html" id="type">
    {{#  if(d.type === 1){ }}
        <span>数字藏品</span>
    {{#  } else if(d.type === 2){ }}
        <span>发售日历</span>
    {{#  } else if(d.type === 3){ }}
        <span>往期回顾</span>
    {{#  } }}
</script>
<!-- 创建时间 -->

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
                    title: '名称',
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
                    title: '颜色',
                    field: 'color',
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
            url: "{{:url('store.card/index')}}",
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
                $.getJSON("{{:url('store.card/remove')}}", {'id' : obj.data['id']}, function(result){
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
