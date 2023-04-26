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
                    <a class="layui-btn layui-btn-normal addBoxGoods" data-id="{{$blindbox_id}}" href="javascript:void(0)">新增藏品</a>
                    <a class="layui-btn layui-btn-normal" href="<?= url('collection.blindbox/addgoods') ?>">返回列表</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>

<!-- 图片替换 -->
<script type="text/html" id="goods-thumb">
    <a href="{{d.goods_thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.goods_thumb}}" alt="藏品图片">
    </a>
</script>
<!-- 图片替换 -->

<!-- 商品状态 -->
<script type="text/html" id="goods_status">
    {{#  if(d.goods_status === 10){ }}
    <input lay-filter="putShelf" type="checkbox" value="20" title="已显示" checked data-goods-id="{{d.id}}">
    {{#  } else { }}
    <input lay-filter="putShelf" type="checkbox" value="10"  title="已隐藏" data-goods-id="{{d.id}}">
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
                    title: '藏品ID',
                    field: 'goods_id',
                    align: 'center'
                },
                {
                    title: '作品图片',
                    field: 'goods_thumb',
                    align: 'center',
                    templet: '#goods-thumb',
                    width: 120,
                },
                {
                    title: '作品名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '概率',
                    field: 'probability',
                    align: 'center'
                },
                {
                    title: '剩余库存',
                    field: 'stock_num',
                    align: 'center'
                },
                {
                    title: '已售数量',
                    field: 'sales_actual',
                    align: 'center'
                },
                {
                    title: '排序',
                    field: 'sort',
                    align: 'center'
                },
                {
                    title: '是否上链',
                    field: 'asset_status',
                    align: 'center',
                },
                {
                    title: '商品状态',
                    field: 'goods_status',
                    align: 'center',
                    templet: '#goods_status',
                    width: 120,
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

        table.render({
            elem: '#user-table',
            url: "{{:url('collection.blindbox/getGoodsList')}}",
            page: true,
            cols: cols,
            skin: 'line',
            where:{
                blindbox_id:{{$blindbox_id}}
            }
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
            }else if(obj.event === 'renewGoods'){
                window.renewGoods(obj);
            }else if(obj.event === 'windChain'){
                window.windChain(obj);
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
                $.getJSON("{{:url('collection.blindbox/removeGoods')}}", {'id' : obj.data['id']}, function(result){
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

        // 添加藏品
        $(document).on("click",".addBoxGoods",function(){
            let blindbox_id = $(this).attr('data-id');
            layer.open({
                type: 2,
                title: '添加盲盒商品',
                shade: 0.1,
                offset: 'auto',
                area: ['80%','90%'],
                content:"{{:url('collection.blindbox/addgoods')}}?blindbox_id="+blindbox_id,
            });
       })

        //编辑数据
        window.renewGoods = function(obj) {
            layer.open({
                type: 2,
                title: '编辑盲盒商品',
                shade: 0.1,
                offset: 'auto',
                area: ['80%','90%'],
                content:"{{:url('collection.blindbox/renewGoods')}}?goods_id="+obj.data['goods_id'],
            });
        }

        /**
         * 藏品发行
         * @param obj
         */
        window.windChain = function(obj) {
            layer.confirm('确认要发行吗？发行后无法进行修改', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.goods/putaway')}}", {'goods_id' : obj.data['goods_id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {

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
