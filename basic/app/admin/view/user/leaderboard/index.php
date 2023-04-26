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
                    <button type="button" class="layui-btn layui-bg-orange" lay-submit lay-filter="order-export">
                        <i class="layui-icon layui-icon-export"></i>
                        导出数据
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
                    title: '拉新人数',
                    field: 'invitations_number',
                    align: 'center'
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('user.leaderboard/index')}}",
            page: true,
            cols: cols,
            skin: 'line'
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
        form.on('submit(order-export)', function(data) {
            var loading = layer.load(0, {
                shade: false,
            });
            $.ajax({
                url: "{{:url('user.leaderboard/index')}}"+"?type=export"
                ,data:data.field
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;
                    data = excel.filterExportData(data,{
                        member_id:'member_id',
                        name:'name',
                        phone:'phone',
                        invitations_number:'invitations_number',
                    });
                    data.unshift({
                        member_id: "会员ID",
                        name: "会员昵称",
                        phone: "会员手机号",
                        invitations_number: "邀请人数",
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

    })
</script>
