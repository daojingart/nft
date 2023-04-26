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
                <input type="hidden" name="types" value="1">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <label>
                            <input type="text" name="phone" placeholder="请输入手机号" class="layui-input">
                        </label>
                    </div>
                    <div class="layui-btn-group">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                </div>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-bg-orange" lay-submit lay-filter="order-export">
                        <i class="layui-icon layui-icon-export"></i>
                        导出数据
                    </button>
                    <button type="button" class="layui-btn layui-btn layui-btn-danger" lay-submit lay-filter="clean">
                        <i class="layui-icon layui-icon-fonts-clear"></i>
                        重置清空数据
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
<!--消费排行榜金额修改 -->
<script type="text/html" id="amount_spent">
    <span style="cursor:pointer;text-align: center;" class="bl-table-name-title bl-Edit-title" data-id="{{d.member_id}}" data-field="z" data-name="{{d.amount_spent}}">
            {{d.amount_spent}} <i class="layui-icon layui-icon-survey" style="font-size: 18px !important;"></i>
        </span>
</script>
<!-- 消费排行榜金额修改 -->

<script>
    layui.use(['table', 'form', 'jquery','laydate','excel'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        excel = layui.excel;
        let laydate = layui.laydate;
        let cols = [
            [
                {
                    title: '会员ID',
                    field: 'member_id',
                    align: 'center'
                },
                {
                    title: '会员昵称',
                    field: 'name',
                    align: 'center'
                },
                {
                    title: '会员手机号',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '消费金额',
                    field: 'amount_spent',
                    align: 'center',
                    templet: '#amount_spent',
                }
            ]
        ]
        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('user.consume/index')}}",
            page: true,
            cols: cols,
            skin: 'line'
        });


        // 搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            tableIns.reload({
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });

        //清空排行榜数据
        form.on('submit(clean)', function(data) {
            let loading = layer.load();
            $.getJSON("{{:url('user.consume/clean')}}", {}, function(result){
                layer.close(loading);
                if(1 === result.code){
                    layer.msg(result.msg, {
                        icon: 1,
                        time: 1000
                    }, function() {
                        tableIns.reload({
                            url: "{{:url('user.consume/index')}}",
                            page: {
                                curr:localStorage.getItem("member_page_consume_curr")?localStorage.getItem("member_page_consume_curr"):1,
                            },
                            cols: cols,
                            skin: 'line',
                            done: function (res, curr, count) {
                                localStorage.setItem("member_page_consume_curr",curr);//存储页码
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
            return false;
        });
        //导出
        form.on('submit(order-export)', function(data) {
            var loading = layer.load(0, {
                shade: false,
            });
            $.ajax({
                url: "{{:url('user.consume/index')}}"+"?type=export"
                ,data:data.field
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;
                    data = excel.filterExportData(data,{
                        member_id:'member_id',
                        name:'name',
                        phone:'phone',
                        amount_spent:'amount_spent',
                    });
                    data.unshift({
                        member_id: "会员ID",
                        name: "会员昵称",
                        phone: "会员手机号",
                        amount_spent: "消费金额",
                    });
                    var colConf = excel.makeColConfig({
                        'A': 180,
                        'B': 180,
                        'C': 180,
                        'D': 180,
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

        /**
         * 修改消费金额
         */
        $(document).on("click",".bl-Edit-title",function () {
            let id = $(this).attr('data-id');
            let title = $(this).attr('data-name');
            let title_name = '修改消费金额';
            layer.prompt({title: title_name, formType: 3,value:title}, function(text, index){
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.consume/editAmountSpent')}}", {'member_id' : id,'amount_spent':text}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            tableIns.reload({
                                url: "{{:url('user.consume/index')}}",
                                page: {
                                    curr:localStorage.getItem("member_page_consume_curr")?localStorage.getItem("member_page_consume_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("member_page_consume_curr",curr);//存储页码
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
        })
    })
</script>
