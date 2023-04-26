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
                        <input type="text" id="phone" name="phone" placeholder="请输入会员手机号" class="layui-input">
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                    <div class="layui-btn-group">
                        <button class="layui-btn  layui-btn-danger" lay-submit lay-filter="order-export">
                            <i class="layui-icon layui-icon-export"></i>
                            导出提现管理
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-url="{{:url('finance.withdrawal/all_list')}}">全部</li>
                <li data-url="{{:url('finance.withdrawal/examine_list')}}">未审核</li>
                <li data-url="{{:url('finance.withdrawal/used_list')}}">已通过</li>
                <li data-url="{{:url('finance.withdrawal/refuse_list')}}">已拒绝</li>
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

{{include file="finance/withdrawal/_template/order" /}}

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
                title: '昵称',
                field: 'nickname',
                align: 'center',
                templet: '#nickname'
            },
            {
                title: '提现金额',
                field: 'amount',
                align: 'center',
                templet: '#amount'
            },
            {
                title: '手续费',
                field: 'service_price',
                align: 'center',
                templet: '#service_price'
            },
            {
                title: '实际到账',
                field: 'actual_amount',
                align: 'center',
                templet: '#actual_amount'
            },
            {
                title: '审核状态',
                field: 'status',
                align: 'center',
                templet: '#w_status'
            },
            {
                title: '提现类型',
                field: 'type',
                align: 'center',
                templet: '#type'
            },
            {
                title: '银行卡名称',
                field: 'bank_name',
                align: 'center'
            },
            {
                title: '账号',
                field: 'bank_card',
                align: 'center'
            },
            {
                title: '开户人',
                field: 'bank_nickname',
                align: 'center'
            },
            {
                title: '申请时间',
                field: 'create_time',
                align: 'center',
                width: 160
            },
            {
                title: '审核时间',
                field: 'audio_time',
                align: 'center',
                width: 160
            },
            {
                title: '操作',
                field: 'operate',
                align: 'center'
            }
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
        request_url = "{{:url('finance.withdrawal/all_list')}}";
        getTableData(request_url,localStorage.getItem("withdrawal_page_curr"));
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
                localStorage.setItem("withdrawal_page_curr",curr);//存储页码
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
        form.on('submit(order-export)', function (data) {
            var ress = data.field;
            var recodePage = $(".layui-laypage-skip .layui-input").val();
            var recodeLimit = $(".layui-laypage-limits").find("option:selected").val();
            ress['page'] = recodePage;
            ress['limit'] = 10000;

            $.ajax({
                url: "{{:url('finance.withdrawal/all_list')}}"
                , data: ress
                , dataType: 'json'
                , success(res) {
                    var data = res.data;
                    console.log(data);
                    data = excel.filterExportData(data, {
                        id: 'id',
                        phone: 'phone',
                        nickname: 'nickname',
                        amount: 'amount',
                        service_price: 'service_price',
                        actual_amount: 'actual_amount',
                        status: function (value) {
                            switch (value) {
                                case 0:
                                    return '待审核';
                                    break;
                                case 1:
                                    return '审核成功';
                                    break;
                                case 2:
                                    return '审核拒绝';
                                    break;
                                default:
                                    return '未知';
                            }
                        },
                        type: function (value) {
                            switch (value) {
                                case 1:
                                    return '支付宝';
                                    break;
                                case 2:
                                    return '银行卡';
                                    break;
                                default:
                                    return '未知';
                            }
                        },
                        bank_name: 'bank_name',
                        bank_card: 'bank_card',
                        bank_nickname: 'bank_nickname',
                        create_time: 'create_time',
                        audio_time: 'audio_time',
                    });
                    // console.log(data);return;
                    data.unshift({
                        id: 'id',
                        phone: '手机号',
                        nickname: '昵称',
                        amount: '提现金额',
                        service_price: '手续费',
                        actual_amount: '实际到账',
                        status: '审核状态',
                        type: '提现类型',
                        bank_name: '银行卡名称',
                        bank_card: '账号',
                        bank_nickname: '开户人',
                        create_time: '申请时间',
                        audio_time: '审核时间',
                    });
                    var colConf = excel.makeColConfig({
                        'A': 80,
                        'B': 150,
                        'C': 100,
                        'D': 100,
                        'E': 180,
                        'F': 180,
                        'G': 100,
                        'H': 100,
                        'I': 100,
                        'J': 100,
                        'K': 100,
                        'L': 100,
                        'M': 100,
                        // 'N': 650,
                    }, 100);
                    var time = new Date();
                    var date = time.getFullYear() + '-' + (time.getMonth() + 1) + '-' + time.getDate() + ' ' + time.getHours()
                        + ':' + time.getMinutes() + ':' + time.getSeconds();
                    let ecel_name = date + ".xlsx";
                    excel.exportExcel({
                        sheet1: data
                    }, ecel_name, 'xlsx', {
                        extend: {
                            '!cols': colConf
                        }
                    });
                    layer.close(loading);
                }
                , error() {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
            return false;
        });
        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的操作
            if (obj.event === 'adopt') {
                window.adopt(obj);
            }
            if (obj.event === 'refuse') {
                window.refuse(obj);
            }
        });

        //  通过
        window.adopt = function(obj) {
            layer.confirm('确认审核通过并打款吗？', {
                icon: 3,
                title: '提示',
                btn: ['支付宝打款', '连连银行卡付款','线下转账', '取消审核'] //可以无限个按钮
                ,btn3: function(index, layero){  //线下打款
                    layer.close(index);
                    let loading = layer.load();
                    $.getJSON("{{:url('finance.withdrawal/upStatus')}}", {'id' : obj.data['id'],'status':1,'pay_type':2}, function(result){
                        layer.close(loading);
                        if(1 === result.code){
                            layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function() {
                                //  关闭按钮 刷新页面
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);// 关闭弹出层
                                parent.window.location.reload(); // 刷新父页面
                            });
                        }else{
                            layer.msg(result.msg, {
                                icon: 2,
                                time: 1000
                            });
                        }
                    });
                }
            }, function(index) {  //支付宝付款
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('finance.withdrawal/upStatus')}}", {'id' : obj.data['id'],'status':1,'pay_type':1}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            //  关闭按钮 刷新页面
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);// 关闭弹出层
                            parent.window.location.reload(); // 刷新父页面
                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });

            }, function(index){  //银行卡打款
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('finance.withdrawal/upStatus')}}", {'id' : obj.data['id'],'status':1,'pay_type':3}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            //  关闭按钮 刷新页面
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.close(index);// 关闭弹出层
                            parent.window.location.reload(); // 刷新父页面
                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });
            },function(index){

            });
        };

        //  拒绝
        window.refuse = function(obj) {
            layer.prompt({
                formType: 2,
                value: '填写信息错误,请输入正确的打款信息',
                title: '请输入拒绝理由'
            }, function(value, index, elem){
                layer.close(index);
                let loading = layer.load();
                $.ajax({
                    type: "post",
                    url: "{{:url('finance.withdrawal/upStatus')}}",
                    data: {
                        "id" : obj.data['id'],
                        "status":2,
                        "reason":value
                    },
                    success:function(result){
                        layer.close(loading);
                        if(1 === result.code){
                            layer.msg(result.msg, {
                                icon: 1,
                                time: 1000
                            }, function() {
                                var index = parent.layer.getFrameIndex(window.name);
                                parent.layer.close(index);//关闭弹出的子页面窗口
                                parent.location.reload(); //重新加载父页面
                            });
                        }else{
                            layer.msg(result.msg, {
                                icon: 2,
                                time: 1000
                            });
                        }
                    }
                });
            });
        }

    }


</script>
