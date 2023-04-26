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
        <blockquote class="layui-elem-quote">
            <p>注意:1>持仓排行榜只展示前300名会员</p>
        </blockquote>
        <div class="layui-row layui-col-space12">
            <form class="layui-form table_serch_input" action="">
                <input type="hidden" name="types" value="1">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <select name="goods_id" id="">
                            <option value="">选择藏品</option>
                            {{volist name="goods_list" id="vo"}}
                            <option value="{{$vo.goods_id}}"  {{if $one_goods_id == $vo['goods_id']}} selected {{/if}}>{{$vo.goods_name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="goods-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                    <button type="button" class="layui-btn layui-bg-orange" lay-submit lay-filter="order-export">
                        <i class="layui-icon layui-icon-export"></i>
                        导出数据
                    </button>
                </div>
            </form>
            <div class="layui-form">
                <div class="layui-input-inline">
                    <select name="goods_id" id="switch_goods">
                        <option value="">选择藏品</option>
                        {{volist name="goods_list" id="vo"}}
                        <option value="{{$vo.goods_id}}"  {{if $goods_id == $vo['goods_id']}} selected {{/if}}>{{$vo.goods_name}}</option>
                        {{/volist}}
                    </select>
                </div>
                <button class="layui-btn  layui-btn-normal" id="submitGoods">
                    <i class="layui-icon layui-icon-search"></i>
                    设定查询
                </button>
            </div>
        </div>
    </div>

    <div class="layui-card-body">
        <table id="goods-table" lay-filter="goods-table"></table>
    </div>
</div>
</body>


<script>
    layui.use(['table', 'form', 'jquery','laydate','excel'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        excel = layui.excel;
        let laydate = layui.laydate;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'member_id',
                    align: 'center'
                },
                {
                    title: '藏品名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '持仓会员昵称',
                    field: 'nickname',
                    align: 'center'
                },
                {
                    title: '持仓会员手机号',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '持仓数量',
                    field: 'goods_number',
                    align: 'center'
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('collection.position/index')}}",
            page: false,
            cols: cols,
            skin: 'line',
            where: {
                'goods_id':{{$one_goods_id}}
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
        form.on('submit(order-export)', function(data) {
            var loading = layer.load(0, {
                shade: false,
            });
            var ress = data.field;
            var recodePage = $(".layui-laypage-skip .layui-input").val();
            var recodeLimit = $(".layui-laypage-limits").find("option:selected").val();
            ress['page'] = recodePage;
            ress['limit'] = 10000;
            $.ajax({
                url: "{{:url('collection.position/index')}}"+"?type=export"
                ,data:ress
                ,dataType: 'json'
                ,success(res) {
                    var data = res.data;
                    data = excel.filterExportData(data,{
                        id:'id',
                        goods_name:'goods_name',
                        nickname:'nickname',
                        phone:'phone',
                        goods_number:'goods_number',
                    });
                    data.unshift({
                        id: "编号ID",
                        goods_name: "藏品名称",
                        nickname: "会员昵称",
                        phone: "会员手机号",
                        goods_number: "藏品数量",
                    });
                    var colConf = excel.makeColConfig({
                        'A': 180,
                        'B': 180,
                        'C': 180,
                        'D': 180,
                        'E': 180,
                    },100);
                    var time=new Date();
                    var date = time.getFullYear() + '-' + (time.getMonth() + 1) + '-' + time.getDate() + ' ' + time.getHours()
                        + ':' + time.getMinutes() + ':' + time.getSeconds();
                    let ecel_name = date+".xlsx";
                    excel.exportExcel({
                        sheet1: data
                    },ecel_name,'xlsx',{
                        extend: {
                            '!cols': colConf
                        }
                    });
                    layer.close(loading);
                }
                ,error() {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
            return false;
        });

        /**
         * 排行榜持仓藏品设定
         */
        $(document).on("click","#submitGoods",function(){
            var goods_id = $("#switch_goods").val();
            $.ajax({
                url: "{{:url('collection.position/setGoods')}}"
                ,data:{'goods_id':goods_id}
                ,dataType: 'json'
                ,success(res) {
                    if(res.code == 1){
                        layer.msg(res.msg);
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }else{
                        layer.msg(res.msg);
                    }
                }
                ,error() {
                    layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
                }
            });
        })

    })
</script>
