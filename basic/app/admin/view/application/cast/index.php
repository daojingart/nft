
<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <!--    表格上方的数据    -->
    <div class="layui-card-body">
        <div class="layui-col-md12 layui-form bl-layui-left">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <input type="text" name="name" id="keyword" placeholder="合成名称" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-inline">
                    <input type="text" name="time" id="create_time" placeholder="选择时间" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </div>
        </div>
        <div class="layui-row layui-col-space12"></div>
    </div>

    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
</div>
</body>
<!--  图片替换-->
<script type="text/html" id="head-thumb">
    <img style="border-radius: 50%;width: 60px;height: 60px" src="{{d.avatarUrl}}" alt="用户头像">
</script>

<script type="text/html" id="operate">
    <a href="javascript:void(0)" lay-event="{{d.member_id}}" lay-type="2">更多</a>
</script>

<script type="text/html" id="status">
    {{#  if(d.status == 10){ }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" >失败</a>
    {{# } else if(d.status == 20){ }}
         <a class="layui-btn layui-btn-xs" >成功</a>
    {{#  } }}

</script>
<!-- 图片替换-->
<script>
    layui.use(['table', 'form', 'jquery','toast','dropdown','laydate'], function() {
        let table = layui.table;
        let form = layui.form;
        let toast = layui.toast;
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
                    title: '用户头像',
                    field: 'avatarUrl',
                    align: 'center',
                    templet: '#head-thumb',
                },
                {
                    title: '用户手机号',
                    field: 'phone',
                    align: 'center',
                },
                {
                    title: '合成藏品名称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '合成状态',
                    field: 'status',
                    align: 'center',
                    templet: '#status'
                },
                {
                    title: '合成时间',
                    field: 'create_time',
                    align: 'center',
                }
            ]
        ]

        //渲染加载数据
        let tableIns = table.render({
            elem: '#user-table',
            id:'videoID',
            url: "{{:url('application.cast/index')}}",
            page: {
                curr:localStorage.getItem("member_page_curr")?localStorage.getItem("member_page_curr"):1,
            },
            cols: cols,
            skin: 'line',
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
    })

</script>