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
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入藏品名称" class="layui-input">
                    </div>

                    <div class="layui-input-inline">
                        <select name="writer_id" id="">
                            <option value="">请选择作家</option>
                            {{volist name="writer" id="vo"}}
                            <option value="{{$vo.id}}">{{$vo.name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="category_id" id="">
                            <option value="">请选择分组</option>
                            {{volist name="category" id="vo"}}
                            <option value="{{$vo.category_id}}">{{$vo.name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="recovery_status" id="">
                            <option value="">回收状态</option>
                            <option value="1">回收中</option>
                            <option value="0">未回收</option>
                        </select>
                    </div>

                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <div class="layui-tab layui-tab-brief" lay-filter="shopOrder">
            <!--内容区域-->
            <div class="layui-tab-content" id="table_content">
                <div class="layui-tab-item layui-show" id="table_content_initialization">
                </div>
            </div>
        </div>
    </div>

</div>
</body>

{{include file="collection/market/_template/order" /}}

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
                title: '藏品图片',
                field: 'goods_thumb',
                align: 'center',
                templet: '#goods-thumb'
            },
            {
                title: '作品编号',
                field: 'goods_no',
                align: 'center',
            },
            {
                title: '藏品名称',
                field: 'goods_name',
                align: 'center',
            },
            {
                title: '回收数量',
                field: 'recovery_num',
                align: 'center',
            },
            {
                title: '回收价格',
                field: 'recovery_price',
                align: 'center',
            },
            {
                title: '回收状态',
                field: 'sale_status',
                align: 'center',
                templet: '#w_status'
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
        request_url = "{{:url('collection.recycl/secureOfficialDeal')}}";
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

        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的操作

        });
        form.on('checkbox(putShelf)', function(data){
            let goods_id = data.elem.getAttribute('data-goods-id');
            let goods_status = data.value;
            let text_tip = '确认下架该商品吗？';
            if(goods_status == '10'){
                text_tip = '确认上架该商品吗？';
            }
            layer.confirm(text_tip, {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.recycl/operation')}}", {'goods_id' : goods_id,'goods_status':goods_status}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('collection.market/secureMemberDeal')}}",
                                page: {
                                    curr:localStorage.getItem("goods_page_curr")?localStorage.getItem("goods_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("goods_page_curr",curr);//存储页码
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


    }


</script>
