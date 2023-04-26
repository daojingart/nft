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
    <ul class="layui-tab-title store_system">
        <li><a href="{{:url('application.task/index')}}">奖励设置</a></li>
        <li class="layui-this"><a href="{{:url('application.task/awardRecord')}}" >发奖记录</a></li>
        <li><a href="{{:url('application.task/todayTask')}}">拉新配置</a></li>
    </ul>
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" name="name" placeholder="请输入奖励名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="member_name" placeholder="请输入用户昵称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="member_mobile" placeholder="请输入用户手机号" class="layui-input">
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="goods-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
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
    layui.use(['table', 'form', 'jquery','laydate'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let laydate = layui.laydate;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'award_record_id',
                    align: 'center'
                },
                {
                    title: '奖项名称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '会员昵称',
                    field: 'member_name',
                    align: 'center'
                },
                {
                    title: '手机号',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '奖励荣誉值',
                    field: 'honor_num',
                    align: 'center'
                },
                {
                    title: '备注',
                    field: 'remark',
                    align: 'center'
                },
                {
                    title: '领取时间',
                    field: 'create_time',
                    align: 'center'
                },
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('application.task/awardRecord')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("task_record_page_curr",curr);//存储页码
            }
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
    })
</script>
