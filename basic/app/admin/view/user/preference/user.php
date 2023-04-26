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
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md12  layui-float-left">
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="name" placeholder="请输入会员昵称" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="phone" placeholder="请输入手机号" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="p_id" placeholder="推荐人id" class="layui-input">
                            </label>
                        </div>
                        <div class="layui-input-inline">
                            <select name="shiming">
                                <option value="">实名认证</option>
                                <option value="">全部</option>
                                <option value="0">未实名</option>
                                <option value="2">实名</option>
                            </select>
                        </div>

                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <select name="status">
                                <option value="">会员状态</option>
                                <option value="">全部</option>
                                <option value="0">禁用</option>
                                <option value="1">正常</option>
                            </select>
                        </div>
                        <div class="layui-inline">
                            <div class="layui-input-inline" style="float: left;width: 290px;margin-right: 10px;">
                                <input size="12" type="text" name="create_time"  id="create_time" class="layui-input" placeholder="请输入注册时间范围">
                            </div>
                        </div>
                        <div class="layui-btn-group">
                            <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                                <i class="layui-icon layui-icon-search"></i>
                                提交查询
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md3">
                <a class="layui-btn layui-btn-normal"
                   href="<?= url('user.index/add') ?>">
                    添加会员
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
    <img style="border-radius: 50%;width: 60px;height: 60px" src="{{d.avatarUrl}}"
         alt="商品图片">
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
    var index = parent.layer.getFrameIndex(window.name);
    layui.use(['table', 'form', 'jquery','toast','dropdown','laydate'], function() {
        let table = layui.table;
        let form = layui.form;
        let toast = layui.toast;
        var laydate = layui.laydate;
        let dropdown = layui.dropdown;
        let $ = layui.jquery;
        let cols = [
            [
                {type: 'radio', fixed: 'left'},

                {
                    title: '用户ID',
                    field: 'member_id',
                    align: 'center',
                },
                {
                    title: '用户ID',
                    field: 'member_id',
                    align: 'center',
                },
                {
                    title: '头像',
                    field: 'member_id',
                    align: 'center',
                    templet: '#head-thumb',
                },
                {
                    title: '昵称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '渠道',
                    field: 'from_type',
                    align: 'center',
                },
                {
                    title: '手机号',
                    field: 'phone',
                    align: 'center',
                },
                {
                    title: '邀请码',
                    field: 'code',
                    align: 'center',
                },
                {
                    title: '账户余额',
                    field: 'account',
                    align: 'center',
                },

                {field: 'status',width:120,align: 'center', title: '状态',templet: '#status'},
                {
                    title: '上级用户',
                    field: 'p_name',
                    align: 'center',
                },
                {
                    title: '注册时间',
                    field: 'create_time',
                    align: 'center',
                },
                {
                    title: '备注',
                    field: 'note',
                    align: 'center',
                },
            ]
        ]

        //渲染加载数据
        let tableIns = table.render({
            elem: '#user-table',
            id:'videoID',
            url: "{{:url('user.index/index')}}",
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

        table.on('radio(user-table)', function(obj){ //test 是 table 标签对应的 lay-filter 属性
            var member_id = obj.data.member_id; //选中行的相关数据
            var member_name = obj.data.name; //选中行的相关数据
            parent.$('#member_id').val(member_id);
            parent.$('#member_name').val(member_name);
            parent.layer.close(index);

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

        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'status') {
                window.remove(obj);
            }
        });

    })

</script>