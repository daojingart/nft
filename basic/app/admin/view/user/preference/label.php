<link rel="stylesheet" href="/assets/admin/layui/css/layui.css" />
<script src="/assets/admin/jquery.min.js"></script>
<script src="/assets/admin/layui/layui.js"></script>
<script src="/assets/admin/watermark.js"></script>
<script src="/assets/admin/pear.js"></script>
<script src="/assets/admin/jquery.form.min.js"></script>
<script src="/assets/admin/modules/webuploader.html5only.js"></script>
<script src="/assets/admin/modules/art-template.js"></script>
<script src="/assets/admin/app.js"></script>
<script src="/assets/admin/modules/file.library.js"></script>
<style>
    .layui-table-view .layui-form-radio {
        padding-top: 10px;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-row">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>

</body>
<!--        账号状态-->
<script type="text/html" id="user-enable">
    {{d.status == '1'?"启用":"禁用"}}
</script>
<!--        账号状态-->
<!--        创建时间-->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!--        创建时间-->
<script>
    var index = parent.layer.getFrameIndex(window.name);
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {type: 'radio', fixed: 'left'},
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                },
                {
                    title: '标签名称',
                    field: 'label_title',
                    align: 'center',
                },
                {
                    title: '标签类型',
                    field: 'label_type',
                    align: 'center'
                },
                {
                    title: '标签条件',
                    field: 'condition',
                    align: 'center',
                },
                {
                    title: '标签人数',
                    field: 'count',
                    align: 'center',
                },
            ]
        ]

        table.render({
            elem: '#user-table',
            url: "{{:url('user.label/index')}}",
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

        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        table.on('radio(user-table)', function(obj){ //test 是 table 标签对应的 lay-filter 属性
            var label_id = obj.data.id; //选中行的相关数据
            var label_title = obj.data.label_title; //选中行的相关数据
            parent.$('#label_id').val(label_id);
            parent.$('#label_title').html(label_title);
            parent.$('#label_none').hide();
            parent.$('#label_show').show();
            parent.layer.close(index);

        });

        //删除
        window.remove = function(obj) {
            layer.confirm('确定要删除该标签', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.label/remove')}}", {'id' : obj.data['id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            obj.del();
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
