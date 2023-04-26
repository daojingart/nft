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
    .layui-form-checked[lay-skin=primary] i {
        border-color: #1890ff!important;
        background-color: #1890ff00;
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
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
        <button class="layui-btn layui-btn-normal puton">
            <i class="layui-icon layui-icon-search"></i>
            批量下架
        </button>
        <button class="layui-btn layui-btn-danger lock">
            <i class="layui-icon layui-icon-password"></i>
            批量加锁
        </button>
        <button class="layui-btn layui-btn layui-btn-warm unlock">
            <i class="layui-icon layui-icon-delete"></i>
            批量解锁
        </button>
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
                templet: '#ID',
                type:'checkbox'
            },
            {
                title: 'ID',
                field: 'id',
                align: 'center',
                templet: '#ID',
            },
            {
                title: '藏品图片',
                field: 'goods_thumb',
                align: 'center',
                templet: '#goods-thumb'
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
                title: '挂售价格',
                field: 'sale_price',
                align: 'center',
            },
            {
                title: '作品hash地址',
                field: 'hash_url',
                align: 'center',
            },
            {
                title: '藏品状态',
                field: 'goods_status',
                align: 'center',
                templet: '#type'
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
        request_url = "{{:url('collection.market/secureMemberDeal')}}";
        getTableData(request_url,localStorage.getItem("withdrawal_page_curr"));
    });


    function getTableData(url,page)
    {
        table.render({
            elem: '#user-table',
            id:'marketID',
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
            table.reload('marketID', {
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });


        $(document).on("click",".puton",function(){
            var value = table.checkStatus('marketID').data;
            if(value.length<='0'){
                layer.alert('请您先选择数据在进行对应的数据操作！');
                return;
            }
            var ids=[];//如果你想获得每个选中行的ID,如下操作
            for(var i=0;i<value.length;i++){
                ids[i] = value[i].id;
            }
            $.ajax({
                type: "POST",
                url: "{{:url('collection.market/batchAllOperation')}}",
                data: {id:ids},
                success: function(result){
                    if(result.code === 1){
                        layer.alert(result.msg);
                        request_url = "{{:url('collection.market/secureMemberDeal')}}";
                        getTableData(request_url,localStorage.getItem("goods_page_curr")?localStorage.getItem("goods_page_curr"):1);
                    }else{
                        layer.alert(result.msg);
                    }
                }
            });

        })


    }
    //解锁
    $(document).on("click",".unlock",function(){
        var value = table.checkStatus('marketID').data;
        if(value.length<='0'){
            layer.alert('请您先选择数据在进行对应的数据操作！');
            return;
        }
        var ids=[];//如果你想获得每个选中行的ID,如下操作
        for(var i=0;i<value.length;i++){
            ids[i] = value[i].id;
        }
        $.ajax({
            type: "POST",
            url: "{{:url('collection.market/batchAllunlock')}}",
            data: {id:ids},
            success: function(result){
                if(result.code === 1){
                    layer.alert(result.msg);
                    request_url = "{{:url('collection.market/secureMemberDeal')}}";
                    getTableData(request_url,localStorage.getItem("goods_page_curr")?localStorage.getItem("goods_page_curr"):1);
                }else{
                    layer.alert(result.msg);
                }
            }
        });

    })


    //加锁
    $(document).on("click",".lock",function(){
        var value = table.checkStatus('marketID').data;
        if(value.length<='0'){
            layer.alert('请您先选择数据在进行对应的数据操作！');
            return;
        }
        var ids=[];//如果你想获得每个选中行的ID,如下操作
        for(var i=0;i<value.length;i++){
            ids[i] = value[i].id;
        }
        $.ajax({
            type: "POST",
            url: "{{:url('collection.market/batchAlllock')}}",
            data: {id:ids},
            success: function(result){
                if(result.code === 1){
                    layer.alert(result.msg);
                    request_url = "{{:url('collection.market/secureMemberDeal')}}";
                    getTableData(request_url,localStorage.getItem("goods_page_curr")?localStorage.getItem("goods_page_curr"):1);
                }else{
                    layer.alert(result.msg);
                }
            }
        });

    })


</script>
