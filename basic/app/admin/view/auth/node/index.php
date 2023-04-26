<body class="pear-container pear-container">
<blockquote class="layui-elem-quote">
    <p>注意:无特殊情况请勿随意修改</p>
</blockquote>
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="power-table" lay-filter="power-table"></table>
        </div>
    </div>
</body>
<script type="text/html" id="power-toolbar">
    <button class="layui-btn layui-btn-normal" lay-event="add">
        <i class="layui-icon layui-icon-add-1"></i>
        新增顶级节点
    </button>
    <div class="layui-btn-group">
        <button type="button" class="layui-btn layui-btn-primary" lay-event="expandAll"><i class="layui-icon layui-icon-spread-left"></i>展开</button>
        <button type="button" class="layui-btn layui-btn-primary" lay-event="foldAll"><i class="layui-icon layui-icon-shrink-right"></i>折叠</button>
    </div>
</script>
<script type="text/html" id="icon">
    <i class="iconfont sidebar-nav-link-logo {{d.menu_icon}}"></i>
</script>
<script>
    layui.use(['table','form','jquery','treetable'],function () {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let treetable = layui.treetable;
        window.render = function(){
            treetable.render({
                treeColIndex: 1,
                treeSpid: 0,
                treeIdName: 'id',
                treePidName: 'p_id',
                skin:'line',
                method:'post',
                treeDefaultClose: true,
                toolbar:'#power-toolbar',
                defaultToolbar:false, //不显示右侧图标
                elem: '#power-table',
                // url: "../power.json",
                url: "{{:url('auth.node/index')}}", //访问数据信息
                page: false,
                cols: [
                    [
                        {field: 'id',minWidth: 70,  title: '权限ID'},
                        {field: 'node_name', minWidth: 150, title: '权限名称'},
                        {field: 'controller_name', minWidth: 100, title: '节点控制器'},
                        {field: 'action_name', minWidth: 100, title: '节点方法'},
                        {field: 'menu_icon', title: '图标',templet:'#icon'},
                        {field: 'sort', title: '排序'},
                        {field: 'operate', width: 200, align: 'center',title:'操作'}
                    ]
                ]
            });
        }
        render();

        //操作按钮的JS事件
        table.on('tool(power-table)',function(obj){
            if (obj.event === 'remove') {
                window.remove(obj);
            } else if (obj.event === 'edit') {
                window.edit(obj);
            }else if(obj.event === 'addChild'){ //添加子节点
                window.add(obj);
            }
        })

        //表格上方的按钮事件
        table.on('toolbar(power-table)', function(obj){
            if(obj.event === 'add'){
                window.add(obj);
            }else if(obj.event === 'expandAll'){
                treetable.expandAll("#power-table");
            } else if(obj.event === 'foldAll'){
                treetable.foldAll("#power-table");
            }
        });

        //添加节点
        window.add = function(obj){
            //顶级节点
            let p_id = 0;
            let title = '添加顶级节点';
            if(obj.event != 'add'){
                p_id = obj.data.id;
                title = '添加'+obj.data.node_name+'子节点';
            }
            layer.open({
                type: 2,
                title: title,
                shade: 0.1,
                offset: 'auto',
                area: ['750px', '630px'],
                content:"{{:url('auth.node/add')}}?id="+p_id
            });
        }

        //编辑节点
        window.edit = function(obj){
            layer.open({
                type: 2,
                title: '编辑节点信息',
                shade: 0.1,
                area: ['750px', '630px'],
                content:"{{:url('auth.node/renew')}}?id="+obj.data.id
            });
        }
        //移除节点 软删除
        window.remove = function(obj){
            layer.confirm('确认删除该权限节点吗?删除父级节点会连带子节点同步删除?', {icon: 3, title:'提示'}, function(index){
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('auth.node/remove')}}", {'id' : obj.data['id']}, function(result){
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
