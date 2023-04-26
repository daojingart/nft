
<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-col-md12 layui-form bl-layui-left">
<!--            <div class="layui-form-item">-->
<!--                <div class="layui-inline">-->
<!--                    <input type="text" name="name" id="keyword" placeholder="合成名称" autocomplete="off" class="layui-input">-->
<!--                </div>-->
<!--                <div class="layui-inline">-->
<!--                    <button class="layui-btn layui-btn-normal layui-btn-sm layuiadmin-btn-goods" lay-submit lay-filter="user-query">提交查询</button>-->
<!--                </div>-->
<!--            </div>-->
        </div>
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md3">
                <a class="layui-btn layui-btn-normal"
                   href="<?= url('application.conditions/add',['id'=>$id]) ?>">
                    添加藏品碎片
                </a>
            </div>

        </div>


    </div>

    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
</div>
</body>
<!--  图片替换-->
<script type="text/html" id="head-thumb">
    <img style="border-radius: 50%;width: 60px;height: 60px" src="{{d.goods_thumb}}"
         alt="合成图片">
</script>

<script type="text/html" id="operate">
    <a href="javascript:void(0)" lay-event="{{d.member_id}}" lay-type="2">更多</a>
</script>

<script type="text/html" id="status">
    {{#  if(d.status == '0'){ }}
    <a class="layui-btn layui-btn-danger layui-btn-xs" data-member_id="{{d.memebr_id}}" lay-event="status">禁用</a>
    {{#  } }}

    {{#  if(d.status == 1){ }}
    <a class="layui-btn layui-btn-xs"  data-member_id="{{d.memebr_id}}" lay-event="status">正常</a>

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
                    title: '碎片名称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '需求数量',
                    field: 'count',
                    align: 'center',
                },
                {
                    title: '排序',
                    field: 'sort',
                    align: 'center',
                },
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
            url: "{{:url('application.conditions/index',['id'=>$id])}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("member_page_curr",curr);//存储页码
                dropdown.render({
                    elem: '.blMore'
                    ,trigger: 'hover'
                    ,data: [
                        {
                            title: '贴标签',
                            id: 'pasteLabel',
                        },
                        {
                            title: '调整上级'
                            ,id: 'adjustSuperior'
                        },{
                            title: '充值余额'
                            ,id: 'Balance'
                        }

                    ]
                    ,click: function(obj){
                        switch (obj.id)
                        {
                            case "pasteLabel":
                                window.pasteLabel(id_values);
                                break;
                            case "adjustSuperior":
                                window.adjustSuperior(id_values);
                                break;
                            case "Balance":
                                window.Balance(id_values);
                                break;
                            case "drop":
                                window.drop(id_values);
                                break;

                        }
                    },ready: function(elemPanel, elem){
                        id_values = elem[0].attributes[1].value;
                    }
                });
            }
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

        //发送私信
        window.sendMsg = function(id) {
            layer.open({
                type: 2,
                title: '发送站内信',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','500px'],
                content:"sendMessage?member_id="+id,
            });
        }
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'status') {
                window.statuss(obj);
            }
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });
        //给会员贴标签
        window.pasteLabel = function(id) {
            layer.open({
                type: 2,
                title: '贴标签',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','500px'],
                content:"pasteLabel?member_id="+id,
            });
        }
        window.drop = function(id) {
            layer.open({
                type: 2,
                title: '空投商品',
                shade: 0.1,
                offset: 'auto',
                area: ['600px','500px'],
                content:"drop?member_id="+id,
            });
        }
        window.remove = function(obj) {
            layer.confirm('确认删除吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('common/del')}}", {'id' : obj.data['id'],'dbname':'GoodsSyntheticCount'}, function(result){
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

        window.statuss = function(obj) {
            layer.confirm('确认更改状态吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('common/status')}}", {'id' : obj.data['id'],'dbname':'GoodsSynthetic'}, function(result){
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
        //调整上级
        window.adjustSuperior = function (member_id){
            layer.open({
                type: 2,
                title: '调整上级用户',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"modifySubordinate?member_id="+member_id,
            });
        }

        //调整余额
        window.Balance = function (member_id){
            layer.open({
                type: 2,
                title: '调整余额',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"recharge?member_id="+member_id,
            });
        }


        //充值积分
        window.Score = function (member_id){
            layer.open({
                type: 2,
                title: '调整积分',
                shade: 0.1,
                offset: 'auto',
                area: ['500px','340px'],
                content:"rechargeScore?member_id="+member_id,
            });
        }


        //批量操作数据 执行上架还是下架
        $(document).on("click",".puton",function(){
            var value = table.checkStatus('videoID').data;
            if(value.length<='0'){
                toast.error({title: '错误提示',message: '请您先选择数据在进行对应的数据操作！',position: 'topCenter'});
                return;
            }
            var ids=[];//如果你想获得每个选中行的ID,如下操作
            for(var i=0;i<value.length;i++){
                ids[i] = value[i].id;
            }
            var is_rack = $(this).attr('data-value');
            $.ajax({
                type: "POST",
                url: "{{:url('live.live/editStatus')}}",
                data: {id:ids,is_rack:is_rack},
                success: function(result){
                    if(result.code === 1){
                        toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                        tableIns.reload({
                            page: {
                                curr: localStorage.getItem("member_page_curr")
                            },
                        });
                    }else{
                        toast.error({title: '错误提示',message: '执行错误,请检查勾选状态！',position: 'topCenter'});
                    }
                }
            });

        })

        //批量操作 执行批量删除
        $(document).on("click",".delete",function(){
            var value = table.checkStatus('videoID').data;
            if(value.length<='0'){
                toast.error({title: '错误提示',message: '还未勾选需要删除的课程！',position: 'topCenter'});
                return;
            }
            var ids=[];//如果你想获得每个选中行的ID,如下操作
            for(var i=0;i<value.length;i++){
                ids[i] = value[i].id;
            }
            $.ajax({
                type: "POST",
                url: "{{:url('live.live/batchRemoveAll')}}",
                data: {id:ids},
                success: function(result){
                    if(result.code === 1){
                        toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                        tableIns.reload({
                            page: {
                                curr: localStorage.getItem("member_page_curr")
                            },
                        });
                    }else{
                        toast.error({title: '错误提示',message: '执行错误,请检查勾选状态！',position: 'topCenter'});
                    }
                }
            });
        })
    })

</script>