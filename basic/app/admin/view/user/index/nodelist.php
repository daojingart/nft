<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
    .layui-table-cell {
        height: auto;
    }
    .layui-form-checked[lay-skin=primary] i {
        border-color: #1890ff!important;
        background-color: #1890ff00;
    }
</style>

<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</body>

<script>
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: '用户ID',
                    field: 'member_id',
                    align: 'center',
                },
                {
                    title: '会员昵称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '手机号',
                    field: 'phone',
                    align: 'center',
                },
                {
                    title: '注册时间',
                    field: 'create_time',
                    align: 'center',
                },
            ]
        ]

        let tableIns = table.render({
            elem: '#user-table',
            url: "{{:url('user.index/getNodeList',['member_id'=>$member_id])}}", //模拟接口
            page: true,
            cols: cols,
            id:'realId',
            skin:'line',
            done: function (res, curr, count) {
                localStorage.setItem("member_real_page_curr", curr);//存储页码
            }
        });


    })


</script>