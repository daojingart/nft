<!-- 布 局 样 式 -->
<link rel="stylesheet" href="/assets/admin/css/admin.css" />
<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<link rel="stylesheet" href="/assets/admin/css/load.css" />
<link rel="stylesheet" href="/assets/admin/layui/css/layui.css" />

<style>
    .layui-table-main .layui-table-cell {
        height: 50px !important;
        line-height: 50px !important;
    }
    .layui-table img {
        height: 50px ;
    }
    .layui-table-cell .layui-form-checkbox[lay-skin=primary]{
        top: 20px;
    }
    .layui-table-header th:first-of-type .layui-table-cell .layui-form-checkbox[lay-skin=primary]{
        top: 6px;
    }
    .layui-table-view .layui-form-checkbox[lay-skin=primary] i{
        width: 22px;
        height: 22px;
    }
    .layui-form-checked[lay-skin=primary] i{
        line-height: 22px;
        border: 1px solid #7c7c7c7d;
        background-color: #1890ff00 !important;
    }

</style>

<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输盲盒名称" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="create_time"  id="create_time" class="layui-input" placeholder="创作时间范围">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="start_time"  id="start_time" class="layui-input" placeholder="开售时间范围">
                        </div>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="goods-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <table id="goods-table" lay-filter="goods-table"></table>
    </div>
</div>
</body>

<!-- 依 赖 脚 本 -->
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>

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
                    align: 'center',
                    type:'checkbox'
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
                    title: '售卖价格',
                    field: 'goods_price',
                    align: 'center',
                },
                {
                    title: '限购数量',
                    field: 'buy_num',
                    align: 'center',
                },
                {
                    title: '发行数量',
                    field: 'stock_num',
                    align: 'center',
                },
                {
                    title: '开售时间',
                    field: 'start_time',
                    align: 'center',
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            id:'goodsId',
            url: "{{:url('collection.exchange/getblindboxlist')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("exchange_goods_page_curr",curr);//存储页码
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
