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
    <div class="layui-card-body">
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('collection.calendar/add') ?>">新增发售日历</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>

<!-- 账号状态 -->
<script type="text/html" id="disabled">
    {{d.disabled == '0'?"展示":"隐藏"}}
</script>
<!-- 账号状态 -->

<!--创建时间 -->
<script type="text/html" id="create_time">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!-- 创建时间 -->

<script>
    layui.use(['table', 'form', 'jquery','toast'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let toast = layui.toast;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center'
                },
                {
                    title: '排序值',
                    field: 'sort',
                    align: 'center'
                },
                {
                    title: '状态',
                    field: 'disabled',
                    align: 'center',
                    templet: '#disabled'
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center',
                    templet: '#create_time',
                    width: 160
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 130
                }
            ]
        ]

        table.render({
            elem: '#user-table',
            url: "{{:url('collection.calendar/index')}}",
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
            if (obj.event === 'goods') {
                window.toGoods(obj);
            }
        });

        //删除
        window.remove = function(obj) {
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.calendar/remove')}}", {'id' : obj.data['id']}, function(result){
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

        // 添加藏品
        window.toGoods = function(obj) {
            layer.open({
                type: 2,
                title: '设置藏品',
                shade: 0.1,
                btn:["确定","取消"],
                offset: 'auto',
                area: ['1020px','770px'],
                content:"{{:url('collection.calendar/getReserveGoodsList')}}?calendar_id="+obj.data['id'],
                id: 'LAY_ADDMEMBER' //设定一个id，防止重复弹出
                ,yes: function(index, layero){
                    let iframeWin = window[layero.find('iframe')[0]['name']];
                    let checkStatus = iframeWin.layui.table.checkStatus('goodsId').data;
                    //判断当前选中的数据是否有选中
                    console.log(checkStatus.length);
                    if(checkStatus.length <= 0){
                        toast.error({title: '错误提示',message: '请先勾选需要添加的藏品,然后在点击确定！',position: 'topCenter'});
                        return;
                    }
                    //循环 获取选中的会员ID 然后保存到数据库
                    var goods_ids = [];//如果你想获得每个选中行的ID,如下操作
                    for(var i = 0;i < checkStatus.length;i++){
                        goods_ids[i] = checkStatus[i].goods_id;
                    }
                    //发送添加的信息增加开通的会员人数  查询snake_member_learn 表  course_type  30的
                    $.ajax({
                        type: "POST",
                        url: "{{:url('collection.calendar/addGoods')}}",
                        data: {goods_ids:goods_ids,calendar_id: obj.data['id']},
                        success: function(result){
                            if(result.code === 1){
                                toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                                //添加成功进列表
                                window.location.href = "{{:url('collection.calendar/index')}}";
                            }else{
                                toast.error({title: '错误提示',message: '执行错误,请检查勾选状态！',position: 'topCenter'});
                            }
                        }
                    });
                }
                ,btn2: function(index, layero){

                    //按钮【按钮二】的回调
                    // console.log("点击取消获取信息");
                    //return false 开启该代码可禁止点击该按钮关闭

                }
            });
        }

    })
</script>
