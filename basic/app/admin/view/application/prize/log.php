
<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <ul class="layui-tab-title store_system">
        <li><a href="{{:url('application.prize/index')}}">奖品管理</a></li>
        <li><a href="{{:url('application.prize/prize')}}" >奖品设置</a></li>
        <li class="layui-this"><a href="{{:url('application.prize/log')}}" >中奖记录</a></li>
    </ul>
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-col-md12 layui-form bl-layui-left">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <input type="text" name="name" id="keyword" placeholder="奖品名称" autocomplete="off" class="layui-input">
                </div>

                <div class="layui-inline">
                    <input type="text" name="phone" id="keyword" placeholder="中奖人手机号" autocomplete="off" class="layui-input">
                </div>

                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                    <button type="button" class="layui-btn layui-bg-orange" lay-submit lay-filter="order-export">
                        <i class="layui-icon layui-icon-export"></i>
                        导出信息
                    </button>
                </div>

            </div>
        </div>
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md3">
                <a class="layui-btn layui-btn-normal" href="<?= url('application.prize/add') ?>">添加奖品</a>
            </div>
        </div>

    </div>


    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
</div>
</body>
<!--  图片替换-->
<script type="text/html" id="head-thumb">
    <img style="border-radius: 50%;width: 60px;height: 60px" src="{{d.prize_thumb}}"
         alt="奖品图片">
</script>

<script type="text/html" id="operate">
    <a href="javascript:void(0)" lay-event="{{d.member_id}}" lay-type="2">更多</a>
</script>

<script type="text/html" id="type">
    {{#  if(d.prize_type == 1){ }}
    红包
    {{#  } }}

    {{#  if(d.prize_type == 2){ }}
    藏品
    {{#  } }}

    {{#  if(d.prize_type == 3){ }}
    产品
    {{#  } }}

    {{#  if(d.prize_type == 4){ }}
    空奖
    {{#  } }}


</script>
<!-- 图片替换-->
<script>
    layui.use(['table', 'form', 'jquery','toast','dropdown','laydate','excel'], function() {
        let table = layui.table;
        let form = layui.form;
        let toast = layui.toast;
        let excel = layui.excel;
        var laydate = layui.laydate;
        let dropdown = layui.dropdown;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                },
                {
                    title: '中奖人名称',
                    field: 'user_name',
                    align: 'center',
                },
                {
                    title: '中奖人手机号',
                    field: 'user_phone',
                    align: 'center',
                },
                {
                    title: '奖品名称',
                    field: 'prize_name',
                    align: 'center',
                },

                {field: 'image',width:120,align: 'center', title: '奖品图片',templet: '#head-thumb'},
                {field: 'type',width:120,align: 'center', title: '类型',templet: '#type'},
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    minWidth: 260,
                }
            ]
        ]

        //渲染加载数据
        let tableIns = table.render({
            elem: '#user-table',
            id:'videoID',
            url: "{{:url('application.prize/log')}}",
            page: true,
            cols: cols,
            skin: 'line',
        });
        form.on('submit(order-export)', function(data) {
            var loading = layer.load(0, {
                shade: false,
            });
            $.ajax({
                url: "export"
                ,data:data.field
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;

                    data = excel.filterExportData(data,{
                        prize_id:'prize_id',
                        prize_name:'prize_name',
                        user_name:'user_name',
                        user_phone:'user_phone',
                        type_text:'type_text',
                        update_time:'update_time',
                    });
                    data.unshift({
                        prize_id:'中奖id',
                        prize_name:'奖品名称',
                        user_name:'用户昵称',
                        user_phone:'用户手机号',
                        type_text:'奖品类型',
                        update_time:'更新时间',
                    });
                    var colConf = excel.makeColConfig({
                        'A': 180,
                        'B': 180,
                        'C': 180,
                        'D': 180,
                        'E': 180,
                        'F': 180,
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

        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
            ,type: 'datetime'
        });
        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            tableIns.reload({
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });

        table.on('tool(user-table)', function(obj) {
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });
        window.remove = function(obj) {
            layer.confirm('确认删除吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('common/del')}}", {'id' : obj.data['id'],'dbname':'prizeLog'}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            location.reload();
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


    })

</script>