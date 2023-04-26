<script  id="order" type="text/template">
<link rel="stylesheet" href="/assets/admin/css/member.css">
    <!--        表格上方的数据-->
    <div class="layui-card-body">
        <div class="layui-row layui-col-space12">
            <div class="layui-col-md3 layui-float-left">
            </div>
            <div class="layui-col-md9">
                <form class="layui-form layui-float-right" action="">
                    <input type="hidden" name="course_id" value="{{id}}">
                    <div class="layui-form-item">
                        <label class="layui-form-label">订单编号</label>
                        <div class="layui-input-inline">
                            <label>
                                <input type="text" name="order_sn" placeholder="" class="layui-input">
                            </label>
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
            <div class="layui-btn-group">
            </div>
        </div>
    </div>
    <!--表格上方的数据-->
    <table id="user-table" lay-filter="user-table"></table>
    <script type="text/javascript">
        layui.use(['table', 'form', 'jquery','toast'], function() {
            let table = layui.table;
            let form = layui.form;
            let toast = layui.toast;
            let $ = layui.jquery;
            let cols = [
                [
                    {
                        title: '订单编号',
                        field: 'order_no',
                        align: 'center',
                    },
                    {
                        title: '产品标题',
                        field: 'goods_name',
                        align: 'center',
                    },
                    {
                        title: '产品价格',
                        field: 'pay_price',
                        align: 'center',
                    },
                    {
                        title: '下单时间',
                        field: 'create_time',
                        align: 'center',
                    }
                ]
            ]
            //渲染加载数据
            let tableIns = table.render({
                elem: '#user-table',
                id:'MemberID',
                url: "{{:url('member.member/getOpenList')}}",
                page: {
                    curr:localStorage.getItem("open_page_curr")?localStorage.getItem("open_page_curr"):1,
                },
                cols: cols,
                skin: 'line',
                where: {
                    'member_id':{{id}}
                },
            })

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
</script>

