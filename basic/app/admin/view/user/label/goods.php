<!-- 依 赖 样 式 【河南八六互联信息技术有限公司】 -->
<link rel="stylesheet" href="/assets/admin/css/pear.css" />
<!-- 加 载 样 式-->
<link rel="stylesheet" href="/assets/admin/css/load.css" />
<!-- 布 局 样 式 -->
<link rel="stylesheet" href="/assets/admin/css/admin.css" />
<!-- 依 赖 脚 本 -->
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<style>
    .layui-table-main .layui-table-cell {
        /*height: 50px !important;*/
        line-height: 50px !important;
    }
    .layui-table img {
        height: 50px;
    }
    .layui-table-view .layui-form-radio{
        line-height:35px;
        padding: 0;
    }
    .layui-table-view .layui-form-radio>i {
        margin: 0;
        font-size: 20px !important;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入藏品名称" class="layui-input">
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
<input type="hidden" id="types" value="{{$types}}">
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
        <input  type="checkbox" value="20" title="已显示" checked data-goods-id="{{d.goods_id}}">
    {{#  } else { }}
        <input  type="checkbox" value="10"  title="已隐藏" data-goods-id="{{d.goods_id}}">
    {{#  } }}
</script>
<!-- 商品状态 -->

<!-- 商品状态 -->
<script type="text/html" id="product_types">
    {{#  if(d.product_types == '1'){ }}
    发行藏品
    {{#  } else if(d.product_types == '2') { }}
    空投库藏品
    {{#  } else if(d.product_types == '3') { }}
    盲盒
    {{#  } else if(d.product_types == '4') { }}
    申购藏品
    {{#  } else if(d.product_types == '5') { }}
    盲盒内的藏品
    {{#  } }}
</script>
<!-- 商品状态 -->

<script>
    var index = parent.layer.getFrameIndex(window.name);
    layui.use(['table', 'form', 'jquery','laydate'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let laydate = layui.laydate;

        let cols = [
            [
                {type: 'radio', fixed: 'left'},
                {
                    title: 'ID',
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
                    title: '作品类型',
                    field: 'product_types',
                    templet: '#product_types',
                    align: 'center'
                },
                {
                    title: '作品名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '分类名称',
                    field: 'goods_category_name',
                    align: 'center'
                },

            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('collection.goods/synthesis',['types'=>$types])}}",
            page: true,
            cols: cols,
            skin: 'line',
            limit:8,
            where:{
                'types':$("#types").val()
            },
            done: function (res, curr, count) {
                localStorage.setItem("goods_page_curr",curr);//存储页码
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
            // 监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        table.on('radio(goods-table)', function(obj){ //test 是 table 标签对应的 lay-filter 属性
            var goods_id = obj.data.goods_id; //选中行的相关数据
            var goods_name = obj.data.goods_name; //选中行的相关数据
            var goods_thumb = obj.data.goods_thumb; //选中行的相关数据
            parent.$('#goods_name').html(goods_name);
            parent.$('#goods_id').val(goods_id);
            parent.$('#goods_thumb').attr('src',goods_thumb);
            parent.$('#goods_none').hide();
            parent.$('#goods_show').show();
            parent.layer.close(index);

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
