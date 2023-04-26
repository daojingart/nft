<style>
    .layui-btn-group{
        display: inline-block;
        vertical-align: middle;
        font-size: 0;
        margin-top: -5px;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <label><input type="text" name="name" placeholder="请输入会员昵称" class="layui-input"></label>
                    </div>
                    <div class="layui-input-inline">
                        <label>
                            <select name="amount_type" id="">
                                <option value="">请选择收支类型</option>
                                <option value="1">收入</option>
                                <option value="2">支出</option>
                            </select>
                        </label>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="float: left;width: 290px;margin-right: 10px;">
                            <input size="12" type="text" name="create_time"  id="test" class="layui-input" placeholder="时间范围">
                        </div>
                    </div>
                    <div class="layui-btn-group">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                    <div class="layui-btn-group">
                        <button class="layui-btn  layui-btn-danger" lay-submit lay-filter="order-export">
                            <i class="layui-icon layui-icon-export"></i>
                            导出荣誉值流水
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="layui-card-body">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>
<script>
    layui.use(['table', 'form', 'jquery','laydate','excel'], function() {
        let table = layui.table;
        let form = layui.form;
        let laydate = layui.laydate;
        excel = layui.excel;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                    totalRowText:'合计',
                },
                {
                    title: '用户昵称',
                    field: 'nickname',
                    align: 'center',
                },
                {
                    title: '变动数量',
                    field: 'amount',
                    align: 'center',
                    totalRow:true
                },
                {
                    title: '流水类型',
                    field: 'remark',
                    align: 'center',
                },
                {
                    title: '收支类型',
                    field: 'amount_type',
                    align: 'center',
                },
                {
                    title: '发生时间',
                    field: 'create_time',
                    align: 'center',
                }
            ]
        ]
        table.render({
            elem: '#user-table',
            url: "{{:url('finance.finance/glory')}}",
            page: true,
            cols: cols,
            skin: 'line',
            totalRow:true,
            limits:[10,20,50,100,200],
        });
        form.on('submit(order-export)', function(data) {
            var ress = data.field;
            var recodePage = $(".layui-laypage-skip .layui-input").val();
            var recodeLimit = $(".layui-laypage-limits").find("option:selected").val();
            ress['page'] = recodePage;
            ress['limit'] = 10000;

            $.ajax({
                url: "{{:url('finance.finance/glory')}}"
                ,data:ress
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;
                    console.log(data);
                    data = excel.filterExportData(data,{
                        id:'id',
                        nickname:'nickname',
                        amount:'amount',
                        remark:'remark',
                        amount_type:'amount_type',
                        create_time:'create_time',
                    });
                    // console.log(data);return;
                    data.unshift({
                        id:'id',
                        nickname:'用户昵称',
                        amount:'变动数量',
                        remark:'流水类型',
                        amount_type:'收支类型',
                        create_time:'发生时间',
                    });
                    var colConf = excel.makeColConfig({
                        'A': 80,
                        'B': 150,
                        'C': 100,
                        'D': 100,
                        // 'E': 180,
                        'F': 180,
                        'G': 100,
                        // 'N': 650,
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


        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            table.reload('user-table', {
                where: data.field
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

        //日期范围
        laydate.render({
            elem: '#test'
            ,range: '~' //或 range: '~' 来自定义分割字符
            ,type: 'datetime'
        });


    })
</script>