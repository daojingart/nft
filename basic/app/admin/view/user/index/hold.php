<style>
    .layui-tab-content {
        padding: 10px;
        padding-left: 15px;
        background: #f5f5f500;
    }
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
                        <input type="text" id="name" name="goods_name" placeholder="请输入作品名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <select name="source_type" id="">
                            <option value="">请选择藏品来源</option>
                            <option value="1">抢购</option>
                            <option value="2">预约</option>
                            <option value="3">盲盒</option>
                            <option value="4">空投</option>
                            <option value="5">合成</option>
                        </select>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
                <div class="layui-row layui-col-space12">
                    <div class="layui-col-md12">
                        <a  class="layui-btn layui-btn-normal" href="{{:url('turnadd',['member_id'=>$member_id])}}">转增藏品</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-url="{{:url('collection.order/all_list',['member_id'=>$member_id])}}">全部</li>
                <li data-url="{{:url('collection.order/cast_list',['member_id'=>$member_id])}}">铸造中</li>
                <li data-url="{{:url('collection.order/hold_list',['member_id'=>$member_id])}}">持有中</li>
                <li data-url="{{:url('collection.order/listing_list',['member_id'=>$member_id])}}">挂售中</li>
                <li data-url="{{:url('collection.order/trade_list',['member_id'=>$member_id])}}">交易中</li>
                <li data-url="{{:url('collection.order/sell_list',['member_id'=>$member_id])}}">已出售</li>
                <li data-url="{{:url('collection.order/donation_list',['member_id'=>$member_id])}}">已转增</li>
                <li data-url="{{:url('collection.order/synthesis_list',['member_id'=>$member_id])}}">已合成</li>
                <li data-url="{{:url('collection.order/castfail_list',['member_id'=>$member_id])}}">铸造失败</li>
            </ul>
            <!--内容区域-->
            <div class="layui-tab-content" id="table_content">
                <div class="layui-tab-item layui-show" id="table_content_initialization">

                </div>
            </div>
        </div>
    </div>

</div>
</body>
{{include file="collection/order/_template/order" /}}
<script>
    let table;
    let cols = [
        [
            {
                title: 'ID',
                field: 'id',
                align: 'center',
                templet: '#ID'
            },
            {
                title: '手机号',
                field: 'phone',
                align: 'center',
                templet: '#phone'
            },
            {
                title: '作品图片',
                field: 'goods_thumb',
                align: 'center',
                templet: '#goods-thumb'
            },
            {
                title: '作品名称',
                field: 'goods_name',
                align: 'center',
                templet: '#goods_name'
            },
            {
                title: '价格',
                field: 'goods_price',
                align: 'center',
                templet: '#goods_price'
            },
            {
                title: '状态',
                field: 'status',
                align: 'center',
                templet: '#status',
            },
            {
                title: '作品编号',
                field: 'collection_number',
                align: 'center',
            },
            {
                title: 'HASH地址',
                field: 'hash_url',
                align: 'center',
                templet: '#hash_url'
            },
            {
                title: '挂售时间',
                field: 'list_time',
                align: 'center',
                templet: '#list_time',
                width: 200
            },
            {
                title: '藏品来源',
                field: 'source_type',
                align: 'center',
                templet: '#source_type'
            },
            {
                title: '创建时间',
                field: 'create_time',
                align: 'center',
                templet: '#user-createTime'
            },
        ]
    ]

    let request_url = '';
    layui.use(['table', 'form', 'jquery','element','excel','upload','laydate'], function() {
        let element = layui.element;
        table = layui.table;
        form = layui.form;
        excel = layui.excel;
        laydate = layui.laydate;
        let $ = layui.jquery;

        //一些事件触发
        element.on('tab(shopOrder)', function(data){
            let that = $(this);
            // 获取请求的URL
            $("#table_content").html(template('order_list'));
            $("#goods_name").val(""); //清空搜索框的值
            $("#order_sn").val(""); //清空搜索框的值
            $("#member_id").val(""); //清空搜索框的值
            $("#periods_number").val(""); //清空搜索框的值
            request_url = that.attr('data-url');
            getTableData(that.attr('data-url'),1);
        });

        $("#table_content_initialization").html(template('order_list'));
        request_url = "{{:url('collection.order/all_list',['member_id'=>$member_id])}}";
        getTableData(request_url,localStorage.getItem("member_order_page_curr"));
    });


    function getTableData(url,page)
    {
        table.render({
            elem: '#user-table',
            url: url,
            limit: 10, //每页默认显示的数量
            cols: cols,
            skin: 'line',
            page: {
                curr:page,
            },
            done: function (res, curr, count) {
                localStorage.setItem("member_order_page_curr",curr);//存储页码
            }
        });

        //日期范围
        laydate.render({
            elem: '#test'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            table.reload('user-table', {
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });
    }


</script>
