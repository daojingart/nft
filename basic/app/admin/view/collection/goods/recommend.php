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
                <input type="hidden" name="types" value="1">
                <div class="layui-form-item">
                    <div class="layui-input-inline">
                        <input type="text" id="goods_name" name="goods_name" placeholder="请输入藏品名称" class="layui-input">
                    </div>
                    <div class="layui-input-inline">
                        <select name="writer_id" id="">
                            <option value="">请选择作家</option>
                            {{volist name="writer" id="vo"}}
                                <option value="{{$vo.id}}">{{$vo.name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="category_id" id="">
                            <option value="">请选择分组</option>
                            {{volist name="category" id="vo"}}
                                <option value="{{$vo.category_id}}">{{$vo.name}}</option>
                            {{/volist}}
                        </select>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="create_time"  id="create_time" class="layui-input" placeholder="创作时间范围">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <div class="layui-input-inline">
                            <input type="text" name="start_time"  id="start_time" class="layui-input" placeholder="开售时间范围">
                        </div>
                    </div>
                    <button class="layui-btn  layui-btn-normal" lay-submit lay-filter="goods-query">
                        <i class="layui-icon layui-icon-search"></i>
                        提交查询
                    </button>
                </div>
            </form>
        </div>
        <div class="layui-row">
            <!--添加按钮-->
            <div class="layui-card-body">
                <div class="layui-col-md3 bl-layui-left">
                    <a class="layui-btn layui-btn-normal addGoods" href="javascript:void(0)">新增推荐</a>
                </div>
            </div>
            <!--添加按钮-->
        </div>
    </div>

    <div class="layui-card-body">
        <table id="goods-table" lay-filter="goods-table"></table>
    </div>
</div>
</body>

<!-- 图片替换 -->
<script type="text/html" id="goods-thumb">
    <a href="{{d.goods_thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.goods_thumb}}" alt="藏品图片">
    </a>
</script>
<!-- 图片替换 -->

<!-- 商品状态 -->
<script type="text/html" id="goods_status">
    {{#  if(d.goods_status === '显示'){ }}
        <input lay-filter="putShelf" type="checkbox" value="20" title="已显示" checked data-goods-id="{{d.goods_id}}">
    {{#  } else { }}
        <input lay-filter="putShelf" type="checkbox" value="10"  title="已隐藏" data-goods-id="{{d.goods_id}}">
    {{#  } }}
</script>
<!-- 商品状态 -->

<!-- 商品状态 -->
<script type="text/html" id="is_sold">
    {{#  if(d.is_sold > 0){ }}
    <input lay-filter="soldStatus" type="checkbox" value="20" title="售卖中" checked data-goods-id="{{d.goods_id}}">
    {{#  } else { }}
    <input lay-filter="soldStatus" type="checkbox" value="10"  title="已售罄" data-goods-id="{{d.goods_id}}">
    {{#  } }}
</script>
<!-- 商品状态 -->

<script>
    layui.use(['table', 'form', 'jquery','laydate','toast'], function() {
        let table = layui.table;
        let form = layui.form;
        let $ = layui.jquery;
        let laydate = layui.laydate;
        let toast = layui.toast;

        let cols = [
            [
                {
                    title: 'ID',
                    field: 'goods_id',
                    align: 'center'
                },
                {
                    title: '作品图片',
                    field: 'goods_thumb',
                    align: 'center',
                    templet: '#goods-thumb',
                    width: 120,
                },
                {
                    title: '作品名称',
                    field: 'goods_name',
                    align: 'center'
                },
                {
                    title: '分类名称',
                    field: 'goods_category_name',
                    align: 'center'
                },
                {
                    title: '作者名称',
                    field: 'writer_name',
                    align: 'center'
                },
                {
                    title: '售卖价格',
                    field: 'goods_price',
                    align: 'center',
                },
                {
                    title: '限购数量',
                    field: 'buy_num',
                    align: 'center',
                },
                {
                    title: '排序',
                    field: 'goods_sort',
                    align: 'center',
                },
                {
                    title: '发行数量',
                    field: 'original_number',
                    align: 'center',
                },
                {
                    title: '已售数量',
                    field: 'sales_actual',
                    align: 'center',
                },
                {
                    title: '剩余数量',
                    field: 'is_sold_number',
                    align: 'center',
                },
                {
                    title: '开售时间',
                    field: 'start_time',
                    align: 'center',
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#goods-table',
            url: "{{:url('collection.goods/recommend')}}",
            page: true,
            cols: cols,
            skin: 'line',
            where: {
                'types':1
            },
            done: function (res, curr, count) {
                localStorage.setItem("goods_page_curr",curr);//存储页码
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
            }else if(obj.event === 'windChain'){
                window.windChain(obj);
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
                $.getJSON("{{:url('collection.goods/removeRecommend')}}", {'goods_id' : obj.data['goods_id']}, function(result){
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


        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

        //日期范围
        laydate.render({
            elem: '#start_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
        });

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
                        url: "{{:url('collection.goods/editRecommend')}}",
                        data: {goods_ids:goods_ids},
                        success: function(result){
                            console.log(result.code);
                            if(result.code === 1){
                                toast.success({title: '成功提示',message: result.msg,position: 'topCenter'});
                                //添加成功进列表
                                window.location.href = "{{:url('collection.goods/recommend')}}";
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
