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
    <div class="layui-card-body">
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="javascript:void(0)" id="addCode">新增兑换码</a>
                    <a class="layui-btn layui-btn-normal" href="<?= url('collection.goods/index') ?>">返回列表</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>

<!-- 商品状态 -->
<script type="text/html" id="status">
    {{#  if(d.status === 0){ }}
        <span style="color:red;">未使用</span>
    {{#  } else { }}
        <span style="color:black;">已使用</span>
    {{#  } }}
</script>
<!-- 商品状态 -->

<!--创建时间 -->
<script type="text/html" id="create_time">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!-- 创建时间 -->

<script>
    layui.use(['table', 'form', 'jquery','toast'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let toast = layui.toast;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center'
                },
                {
                    title: '兑换码',
                    field: 'code_num',
                    align: 'center'
                },
                {
                    title: '状态',
                    field: 'status',
                    align: 'center',
                    templet: '#status',
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center',
                    templet: '#create_time',
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

        let tableIns = table.render({
            elem: '#user-table',
            url: "{{:url('collection.goods/getConvertList')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("goods_getconvertlist_page_curr",curr);//存储页码
            },
            where:{
                goods_id:{{$goods_id}}
            },


        })

        // 搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            table.reload('user-table', {
                where: data.field
            })
            return false;
        });

        // 监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        // 删除
        window.remove = function(obj) {
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.goods/removeConvert')}}", {'convert_id' : obj.data['id']}, function(result){
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

        // 增加兑换码
        $(document).on("click","#addCode",function(){

            // 获取藏品id
            var goods_id = {{$goods_id}}

            layer.prompt({
                title: '输入兑换码数量[1-200正整数]',
                formType: 3
            }, function(pass, index){
                layer.close(index);
                let loading = layer.load();
                $.post("{{:url('collection.goods/addConvert')}}", {'goods_id':goods_id,'num':pass}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('collection.goods/getConvertList')}}",
                                page: {
                                    curr:localStorage.getItem("goods_getconvertlist_page_curr")?localStorage.getItem("goods_getconvertlist_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("goods_getconvertlist_page_curr",curr);//存储页码
                                }
                            });
                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });
            });
        })
    })
</script>
