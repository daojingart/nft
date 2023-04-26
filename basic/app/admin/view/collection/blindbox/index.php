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
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入盲盒名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <select name="category_id" id="">
                            <option value="">请选择分组</option>
                            {{volist name="category" id="vo"}}
                            <option value="{{$vo.category_id}}">{{$vo.name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="goods-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('collection.blindbox/add') ?>">新增盲盒</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>
    </div>

    <div class="layui-card-body">
        <table id="goods-table" lay-filter="goods-table"></table>
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
    {{#  if(d.goods_status === '显示'){ }}
        <input lay-filter="putShelf" type="checkbox" value="20" title="已显示" checked data-goods-id="{{d.goods_id}}">
    {{#  } else { }}
        <input lay-filter="putShelf" type="checkbox" value="10"  title="已隐藏" data-goods-id="{{d.goods_id}}">
    {{#  } }}
</script>
<!-- 商品状态 -->

<script>
    layui.use(['table', 'form', 'jquery','laydate'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let laydate = layui.laydate;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'goods_id',
                    align: 'center'
                },
                {
                    title: '盲盒图片',
                    field: 'goods_thumb',
                    align: 'center',
                    templet: '#goods-thumb',
                    width: 120,
                },
                {
                    title: '盲盒名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '分类名称',
                    field: 'goods_category_name',
                    align: 'center'
                },
                {
                    title: '盲盒售价',
                    field: 'goods_price',
                    align: 'center',
                },
                {
                    title: '排序',
                    field: 'goods_sort',
                    align: 'center',
                },
                {
                    title: '总发行量',
                    field: 'stock_num',
                    align: 'center',
                },
                {
                    title: '已销售',
                    field: 'sales_actual',
                    align: 'center',
                },
                {
                    title: '开售时间',
                    field: 'start_time',
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
                    align: 'center'
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 180
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('collection.blindbox/index')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("blindbox_page_curr",curr);//存储页码
            }
        });

        //搜索条件查询操作
        form.on('submit(goods-query)', function(data) {
            table.reload('goods-table', {
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });

        //监听操作
        table.on('tool(goods-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        //上下架操作
        form.on('checkbox(putShelf)', function(data){
            let goods_id = data.elem.getAttribute('data-goods-id');
            let goods_status = data.value;
            let text_tip = '确认隐藏该商品吗？';
            if(goods_status == '10'){
                text_tip = '确认显示该商品吗？';
            }
            layer.confirm(text_tip, {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.blindbox/operation')}}", {'goods_id' : goods_id,'goods_status':goods_status}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('collection.blindbox/index')}}",
                                page: {
                                    curr:localStorage.getItem("blindbox_page_curr")?localStorage.getItem("blindbox_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("blindbox_page_curr",curr);//存储页码
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
        });

        //删除
        window.remove = function(obj) {
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.blindbox/remove')}}", {'goods_id' : obj.data['goods_id']}, function(result){
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

        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

        //日期范围
        laydate.render({
            elem: '#start_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

    })
</script>
