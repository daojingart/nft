<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入作品名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="order_no" name="order_no" placeholder="请输入订单编号" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="member_id" name="member_id" placeholder="请输入会员ID" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="sale_member_id" name="sale_member_id" placeholder="请输入售卖会员ID" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="create_time"  id="test" class="layui-input" placeholder="时间范围">
                        </div>
                    </div>
                    <div class="layui-btn-group">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="order-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-url="{{:url('order.backbuy/index')}}">全部</li>
<!--                <li data-url="{{:url('order.backbuy/payList')}}">已支付</li>-->
<!--                <li data-url="{{:url('order.backbuy/cancelList')}}">已取消</li>-->
<!--                <li data-url="{{:url('order.backbuy/refundList')}}">已退款</li>-->
            </ul>
            <!--内容区域-->
            <table id="order-table" lay-filter="order-table"></table>
        </div>
    </div>
</div>
</body>
{{include file="order/backbuy/_template/order" /}}
<script>
    let table;
    let cols = [
        [
            {
                title: 'ID',
                field: 'order_id',
                align: 'center',
            },
            {
                title: '订单编号',
                field: 'order_no',
                align: 'center',
            },
            {
                title: '用户ID',
                field: 'member_id',
                align: 'center',
            },
            {
                title: '用户信息',
                field: 'member_info',
                align: 'center',
            },
            {
                title: '作品名称',
                field: 'goods_name',
                align: 'center',
            },
            {
                title: '金额',
                field: 'pay_price',
                align: 'center',
            },
            {
                title: '订单状态',
                field: 'order_status_text',
                align: 'center',
            },
            {
                title: '支付类型',
                field: 'pay_type_text',
                align: 'center',
            },
            {
                title: '创建时间',
                field: 'create_time',
                align: 'center',
            },
            {
                title: '支付时间',
                field: 'pay_time',
                align: 'center',
            },
            // {
            //     title: '操作',
            //     field: 'operate',
            //     align: 'center',
            // }
        ]
    ];
    let request_url = '';
    layui.use(['table', 'form', 'jquery','element','excel','laydate'], function() {
        let element = layui.element;
        table = layui.table;
        form = layui.form;
        excel = layui.excel;
        laydate = layui.laydate;
        let $ = layui.jquery;

        //一些事件触发
        element.on('tab(shopOrder)', function(data){
            let that = $(this);
            //获取请求的URL
            $("#table_content").html(template('order_list'));
            $("#goods_name").val(""); //清空搜索框的值
            $("#order_sn").val(""); //清空搜索框的值
            $("#member_id").val(""); //清空搜索框的值
            getTableData(that.attr('data-url'),1);
        });
        $("#table_content").html(template('order_list'));
        request_url = "{{:url('order.backbuy/index')}}";
        getTableData(request_url,localStorage.getItem("order_page_curr"));
    });

    function getTableData(url,page)
    {
        table.render({
            elem: '#order-table',
            url: url,
            limit: 10, //每页默认显示的数量
            cols: cols,
            skin: 'line',
            page: {
                curr:page,
            },
            done: function (res, curr, count) {
                localStorage.setItem("order_page_curr",curr);//存储页码
            }
        });
        //搜索条件查询操作
        form.on('submit(order-query)', function(data) {
            table.reload('order-table', {
                where: data.field,
                page: {
                    curr:1,
                },
            });
            return false;
        });
        //日期范围
        laydate.render({
            elem: '#test'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

    }
</script>