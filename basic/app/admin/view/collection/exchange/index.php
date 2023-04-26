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
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入藏品名称" class="layui-input">
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="user-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md6 bl-layui-left">
                    <a class="layui-btn layui-btn-normal addGoods" href="javascript:void(0)">新增藏品</a>
                    <a class="layui-btn layui-btn-normal addBlindbox" href="javascript:void(0)">新增盲盒</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>
    </div>

    <div class="layui-card-body">
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>
</body>

<!-- 账号状态 -->
<script type="text/html" id="status">
    {{d.status == '0'?"展示":"隐藏"}}
</script>
<!-- 账号状态 -->

<!--创建时间 -->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!-- 创建时间 -->

<!-- 图片替换 -->
<script type="text/html" id="goods_thumb">
    <a href="{{d.goods_thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.goods_thumb}}" alt="藏品图片">
    </a>
</script>
<!-- 图片替换 -->

<!-- 账号状态 -->
<script type="text/html" id="type">
    {{#  if(d.type === 1){ }}
        <span style="color:blue;">藏品</span>
    {{#  } else { }}
        <span style="color:red;">盲盒</span>
    {{#  } }}
</script>
<!-- 账号状态 -->

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
                    title: '藏品名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '藏品图片',
                    field: 'goods_thumb',
                    align: 'center',
                    templet: '#goods_thumb'
                },
                {
                    title: '剩余库存',
                    field: 'stock_num',
                    align: 'center',
                },
                {
                    title: '兑换消耗数量',
                    field: 'price',
                    align: 'center'
                },
                {
                    title: '兑换类型',
                    field: 'type',
                    align: 'center',
                    templet: '#type'
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center',
                    templet: '#user-createTime',
                    width: 160
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 180
                }
            ]
        ]

        table.render({
            elem: '#user-table',
            url: "{{:url('collection.exchange/index')}}",
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
            }else if(obj.event === 'windChain'){
                window.windChain(obj);
            }
        });
        /**
         * 藏品发行
         * @param obj
         */
        window.windChain = function(obj) {
            layer.confirm('确认要发行吗？发行后无法进行修改', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.goods/putaway')}}", {'goods_id' : obj.data['goods_id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {

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
        //删除
        window.remove = function(obj) {
            layer.confirm('确定要删除吗', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('collection.exchange/remove')}}", {'id' : obj.data['id']}, function(result){
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
        $(document).on("click",".addGoods",function(){
            layer.open({
                type: 2,
                title: '添加藏品',
                shade: 0.1,
                btn:["确定","取消"],
                offset: 'auto',
                area: ['1020px','870px'],
                content:"{{:url('collection.calendar/getGoodsList')}}",
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
                        url: "{{:url('collection.exchange/addGoods')}}",
                        data: {goods_ids:goods_ids,type:1},
                        success: function(result){
                            console.log(result.code);
                            if(result.code === 1){
                                toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                                //添加成功进列表
                                window.location.href = "{{:url('collection.exchange/index')}}";
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
        })

        // 添加盲盒
        $(document).on("click",".addBlindbox",function(){
            layer.open({
                type: 2,
                title: '添加盲盒',
                shade: 0.1,
                btn:["确定","取消"],
                offset: 'auto',
                area: ['1020px','670px'],
                content:"{{:url('collection.exchange/getBlindboxList')}}",
                id: 'LAY_ADDMEMBER' //设定一个id，防止重复弹出
                ,yes: function(index, layero){
                    let iframeWin = window[layero.find('iframe')[0]['name']];
                    let checkStatus = iframeWin.layui.table.checkStatus('goodsId').data;
                    //判断当前选中的数据是否有选中
                    console.log(checkStatus.length);
                    if(checkStatus.length <= 0){
                        toast.error({title: '错误提示',message: '请先勾选需要添加的盲盒,然后在点击确定！',position: 'topCenter'});
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
                        url: "{{:url('collection.exchange/addGoods')}}",
                        data: {goods_ids:goods_ids,type:2},
                        success: function(result){
                            if(result.code === 1){
                                toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                                //添加成功进列表
                                window.location.href = "{{:url('collection.exchange/index')}}";
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
        })
    })
</script>
