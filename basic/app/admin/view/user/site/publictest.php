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
        <li><a href="{{:url('user.site/index')}}">运营配置</a></li>
        <li><a href="{{:url('user.site/internal')}}">内测名单</a></li>
        <li class="layui-this"><a href="{{:url('user.site/publictest')}}">公测名单</a></li>
    </ul>
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" name="name" placeholder="请输入奖励名称" class="layui-input">
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
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal" href="<?= url('application.task/addAwardSetting') ?>">
                        新增奖励设置
                    </a>
                </div>
            </div>
            <!--添加按钮-->
        </div>

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
                    field: 'award_setting_id',
                    align: 'center'
                },
                {
                    title: '奖项名称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '邀请人数',
                    field: 'invite_num',
                    align: 'center'
                },
                {
                    title: '赠送荣誉值',
                    field: 'honor_num',
                    align: 'center'
                },
                {
                    title: '创建时间',
                    field: 'create_time',
                    align: 'center'
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 130
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('application.task/index')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("task_setting_page_curr",curr);//存储页码
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

        //监听操作
        table.on('tool(goods-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
            //监听到事件的删除操作
            if (obj.event === 'edit') {
                window.edit(obj);
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
                $.getJSON("{{:url('application.task/delSetting')}}", {'award_setting_id' : obj.data['award_setting_id']}, function(result){
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
