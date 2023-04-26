<body class="pear-container pear-container">
<style>

</style>
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="nickname" placeholder="请输入操作人昵称" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline" style="float: left;width: 290px;margin-right: 10px;">
                            <input type="text" name="create_time"  id="create_time" class="layui-input" placeholder="时间范围">
                        </div>
                    </div>
                </div>
                <div class="layui-btn-group">
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="layui-card-body">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>
<!--  图片替换-->
<script type="text/html" id="type_value">
    {{d.type.text}}
</script>
<!-- 图片替换-->
<script>
    layui.use(['table', 'form', 'jquery','laydate'], function() {
        let table = layui.table;
        let laydate = layui.laydate;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: '操作时间',
                    field: 'id',
                    align: 'center',
                },
                {
                    title: '操作账号',
                    field: 'nickname',
                    align: 'center',
                },
                {
                    title: '操作模块',
                    field: 'function',
                    align: 'center'
                },
                {
                    title: '操作类型',
                    field: 'type',
                    align: 'center',
                    templet: '#type_value',
                },
                {
                    title: '操作人时间',
                    field: 'create_time',
                    align: 'center',
                },
                {
                    title: '操作人IP',
                    field: 'login_ip',
                    align: 'center',
                },
                {
                    title: '操作内容',
                    field: 'content',
                    align: 'center',
                }
            ]
        ]

        table.render({
            elem: '#user-table',
            url: "{{:url('store.recordlist/index')}}",
            page: true,
            cols: cols,
            skin: 'line'
        });


        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            table.reload('user-table', {
                where: data.field
            })
            return false;
        });
        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
            ,type: 'datetime'
        });

    })
</script>
