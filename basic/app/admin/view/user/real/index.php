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
        <div class="layui-row layui-form layui-col-space12">
            <div class="layui-form-item">
                <div class="layui-form-item ">
                    <div class="layui-inline">
                        <input type="text" name="phone" id="code" placeholder="请输入会员手机号" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="nickName" id="code" placeholder="请输入用户昵称" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <select name="status" >
                            <option value="">全部</option>
                            <option value="1">审核中</option>
                            <option value="2">已通过</option>
                            <option value="3">已拒绝</option>
                        </select>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                    </div>
                </div>
                <div class="layui-row layui-col-space12">
                    <div class="layui-btn-group">
                        <button type="button" class="layui-btn layui-btn-primary puton" data-value="10">通过</button>
                        <button type="button" class="layui-btn layui-btn-primary puton" data-value="20">驳回</button>
                    </div>
                </div>
            </div>
        </div>
        <table id="user-table" lay-filter="user-table"></table>
    </div>
</body>

<!-- 身份证正面 -->
<script type="text/html" id="header-thumb1">
    <a href="{{d.just}}" title="点击查看大图" target="_blank">
        <img src="{{d.just}}" width="50" height="50" alt="身份证正面">
    </a>
</script>

<!-- 身份证反面  -->
<script type="text/html" id="header-thumb2">
    <a href="{{d.back}}" title="点击查看大图" target="_blank">
        <img src="{{d.back}}" width="50" height="50" alt="身份证反面">
    </a>
</script>

<!--  审核状态   -->
<script type="text/html" id="status">{{d.real_status_text}}</script >

<script type="text/html" id="operation">
    {{#  if(d.real_status == '1'){ }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" data-user_id="{{d.member_id}}" lay-event="reject">驳回</a>
        <a class="layui-btn layui-btn-xs"  data-user_id="{{d.member_id}}" lay-event="pass">通过</a>
    {{#  } }}
</script>

<script>
    layui.use(['table', 'form', 'jquery'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'Id',
                    field: 'id',
                    type:'checkbox'
                },
                {
                    title: '用户ID',
                    field: 'member_id'
                },
                {
                    title: '手机号',
                    field: 'phone'
                },
                {
                    title: '昵称',
                    field: 'nickName'
                },
                {
                    title: '姓名',
                    field: 'name'
                },
                {
                    title: '身份证号码',
                    field: 'card'
                },
                {
                    title: '申请时间',
                    field: 'create_time',
                    width: 160
                },
                {
                    title: '状态',
                    field: 'real_status_text'
                },
                {
                    title: '操作',
                    field: 'remark',
                    templet: '#operation',
                    width:130
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#user-table',
            url: "{{:url('user.real/index')}}", //模拟接口
            page: true,
            cols: cols,
            id:'realId',
            skin:'line',
            done: function (res, curr, count) {
                localStorage.setItem("member_real_page_curr", curr);//存储页码
            }
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

        //监听操作
        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'pass') {
                window.pass(obj);
            }
            if (obj.event === 'reject') {
                window.reject(obj);
            }
        });

        // 通过审核
        window.reject = function (data) {
            var id = data.data.member_id
            layer.confirm('确认要驳回吗？',function(index){
                //发异步把用户状态进行更改
                $.post('{{:Url("user.real/audit")}}',{id:id,status:3},function(data){
                    if(data.code == 1){
                        layer.msg(data.msg,{icon:1});
                        //设置两秒刷新
                        tableIns.reload({
                            url: "{{:url('user.real/index')}}", //模拟接口
                            page: {
                                curr:localStorage.getItem("member_real_page_curr")?localStorage.getItem("member_real_page_curr"):1,
                            },
                            cols: cols,
                            skin: 'line',
                            done: function (res, curr, count) {
                                localStorage.setItem("member_real_page_curr",curr);//存储页码
                            }
                        });
                    }else{
                        layer.msg(data.msg,{icon:5,time:1000});
                    }
                })
            });
        }

        // 拒绝审核
        window.pass = function (data) {
            var id = data.data.member_id
            layer.confirm('确认要通过吗？',function(index){
                //发异步把用户状态进行更改
                $.post('{{:Url("user.real/audit")}}',{id:id,status:2},function(data){
                    if(data.code == 1){
                        layer.msg(data.msg,{icon:1});
                        //设置两秒刷新
                        tableIns.reload({
                            url: "{{:url('user.real/index')}}", //模拟接口
                            page: {
                                curr:localStorage.getItem("member_real_page_curr")?localStorage.getItem("member_real_page_curr"):1,
                            },
                            cols: cols,
                            skin: 'line',
                            done: function (res, curr, count) {
                                localStorage.setItem("member_real_page_curr",curr);//存储页码
                            }
                        });
                    }else{
                        layer.msg(data.msg,{icon:5,time:1000});
                    }
                })
            });
        }

        //批量操作数据 执行上架还是下架
        $(document).on("click",".puton",function(){
            var value = table.checkStatus('realId').data;
            if(value.length<='0'){
                layer.alert('请您先选择数据在进行对应的数据操作',{title:'错误提示'});
                return;
            }
            var ids=[];//如果你想获得每个选中行的ID,如下操作
            for(var i=0;i<value.length;i++){
                ids[i] = value[i].id;
            }
            var is_rack = $(this).attr('data-value');
            //如果确认执行拒绝按钮 需要进行二次确认 二次确认后方可驳回这个申请
            var tip_title = '';
            if(is_rack ==10){
                tip_title = "确认执行批量通过程序吗？";
            }else{
                tip_title = "确认执行批量驳回程序吗";
            }
            layer.confirm(tip_title,function(index){
                $.ajax({
                    type: "POST",
                    url: "{{:url('user.real/batchAudit')}}",
                    data: {id:ids,is_rack:is_rack},
                    success: function(result){
                        if(result.code === 1){
                            layer.msg('执行成功');
                            tableIns.reload({
                                url: "{{:url('user.real/index')}}", //模拟接口
                                page: {
                                    curr:localStorage.getItem("member_real_page_curr")?localStorage.getItem("member_real_page_curr"):1,
                                },
                                cols: cols,
                                skin: 'line',
                                done: function (res, curr, count) {
                                    localStorage.setItem("member_real_page_curr",curr);//存储页码
                                }
                            });
                        }else{
                            layer.msg('执行失败！');
                        }
                    }
                });
            })

        })

    })


</script>