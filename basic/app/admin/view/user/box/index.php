<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入盲盒名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="member_id" name="member_id" placeholder="请输入会员ID" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="member_id" name="phone" placeholder="会员手机号" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="create_time"  id="test" class="layui-input" placeholder="请输入时间范围">
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
                <li class="layui-this" data-url="{{:url('user.box/get_all')}}">全部</li>
                <li data-url="{{:url('user.box/no_open')}}">未开盒</li>
                <li data-url="{{:url('user.box/open_box')}}">已开盒</li>
                <li data-url="{{:url('user.box/airdrop_box')}}">空投盒</li>
            </ul>
            <!--内容区域-->
            <table id="order-table" lay-filter="order-table"></table>
        </div>
    </div>
</div>
</body>
{{include file="order/box/_template/order" /}}
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
                title: '用户ID',
                field: 'member_id',
                align: 'center',
            },
            {
                title: '会员手机号',
                field: 'phone',
                align: 'center',
            },
            {
                title: '会员昵称',
                field: 'name',
                align: 'center',
            },
            {
                title: '盲盒名称',
                field: 'goods_name',
                align: 'center',
            },
            {
                title: '盲盒状态',
                field: 'is_open',
                align: 'center',
            },
            {
                title: '盲盒类型',
                field: 'order_type',
                align: 'center',
            },
            {
                title: '创建时间',
                field: 'create_time',
                align: 'center',
            },
            {
                title: '开盒时间',
                field: 'open_box_time',
                align: 'center',
            },
            {
                title: '藏品名称',
                field: 'open_box_goods_name',
                align: 'center',
            },
            {
                title: '操作',
                field: 'operate',
                align: 'center',
            }
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
        request_url = "{{:url('user.box/get_all')}}";
        getTableData(request_url,localStorage.getItem("box_order_list"));
    });
    function getTableData(url,page)
    {
        table.render({
            elem: '#order-table',
            url: url,
            limit: 15, //每页默认显示的数量
            cols: cols,
            skin: 'line',
            page: {
                curr:page,
            },
            done: function (res, curr, count) {
                localStorage.setItem("box_order_list",curr);//存储页码
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

        //监听操作
        table.on('tool(order-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        //删除
        window.remove = function(obj) {
            console.log(obj);
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.box/remove')}}", {'order_id' : obj.data['order_id']}, function(result){
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
    }
</script>