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
                        <input type="text" id="name" name="goods_name" placeholder="请输入作品的标题" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" id="name" name="phone" placeholder="请输入会员手机号" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <select name="source_type" id="">
                            <option value="">请选择藏品来源</option>
                            <option value="1">抢购</option>
                            <option value="2">预约</option>
                            <option value="3">盲盒</option>
                            <option value="4">空投</option>
                            <option value="5">合成</option>
                            <option value="6">兑换码</option>
                            <option value="7">空投卷</option>
                            <option value="8">荣誉值</option>
                        </select>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                    <button type="button" class="layui-btn layui-bg-orange" lay-submit lay-filter="order-export">
                        <i class="layui-icon layui-icon-export"></i>
                        导出会员信息
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <ul class="layui-tab-title">
                <li class="layui-this" data-url="{{:url('collection.order/all_list')}}">全部</li>
                <li data-url="{{:url('collection.order/cast_list')}}">铸造中</li>
                <li data-url="{{:url('collection.order/hold_list')}}">持有中</li>
                <li data-url="{{:url('collection.order/listing_list')}}">挂售中</li>
                <li data-url="{{:url('collection.order/trade_list')}}">交易中</li>
                <li data-url="{{:url('collection.order/sell_list')}}">已出售</li>
                <li data-url="{{:url('collection.order/donation_list')}}">已转增</li>
                <li data-url="{{:url('collection.order/synthesis_list')}}">已合成</li>
                <li data-url="{{:url('collection.order/castfail_list')}}">铸造失败</li>
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
                title: '藏品编号',
                field: 'collection_number',
                align: 'center',
                templet: '#collection_number',
                width: 300
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
                width: 200
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
            {
                title: '操作',
                field: 'operate',
                align: 'center',
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
        request_url = "{{:url('collection.order/all_list')}}";
        getTableData(request_url,localStorage.getItem("member_order_page_curr"));
    });


    function getTableData(url,page)
    {
        //订单导出
        form.on('submit(order-export)', function(data) {
            var loading = layer.load(0, {
                shade: false,
            });
            $.ajax({
                url: request_url+"?type=export"
                ,data:data.field
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;

                    data = excel.filterExportData(data,{
                        goods_name:'goods_name',
                        nickname:'nickname',
                        phone:'phone',
                    });
                    data.unshift({
                        goods_name: "藏品名称",
                        nickname: "会员昵称",
                        phone: "会员手机号",

                    });
                    var colConf = excel.makeColConfig({
                        'A': 180,
                        'B': 180,
                        'C': 180,
                    },100);
                    var time=new Date();
                    var date = time.getFullYear() + '-' + (time.getMonth() + 1) + '-' + time.getDate() + ' ' + time.getHours()
                        + ':' + time.getMinutes() + ':' + time.getSeconds();
                    let ecel_name = date+".xlsx";
                    excel.exportExcel({
                        sheet1: data
                    },ecel_name,'xlsx',{
                        extend: {
                            '!cols': colConf
                        }
                    });
                    layer.close(loading);
                }
                ,error() {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
            return false;
        });


        table.render({
            elem: '#user-table',
            url: url,
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

        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });


        //删除
        window.remove = function(obj) {
            layer.confirm('确认回收用户的藏品吗？回收后将会销毁这个藏品',{
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.order/destroyCollection')}}", {'id' : obj.data['id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg,{
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

    /**
     * 修改作品编号
     */
    $(document).on("click",".bl-Edit-title",function () {
        let id = $(this).attr('data-id');
        let title = $(this).attr('data-name');
        layer.prompt({title: '修改作品编号', formType: 3,value:title}, function(text, index){
            layer.close(index);
            let loading = layer.load();
            $.getJSON("{{:url('collection.order/editModificationNumber')}}", {'id' : id,'course_name':text}, function(result){
                layer.close(loading);
                if(1 === result.code){
                    layer.msg(result.msg, {
                        icon: 1,
                        time: 1000
                    }, function() {
                        request_url = "{{:url('collection.order/all_list')}}";
                        getTableData(request_url,localStorage.getItem("member_order_page_curr"));
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

</script>
