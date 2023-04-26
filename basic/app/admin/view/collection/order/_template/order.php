<script  id="order_list" type="text/template">
    <table id="user-table" lay-filter="user-table"></table>
</script>


<script type="text/html" id="ID"><div class="am-text-middle" rowspan="1"><p>{{d.id}}</p></div></script>
<script type="text/html" id="phone"><div class="am-text-middle" rowspan="1"><p>{{d.phone}}</p></div></script>
<script type="text/html" id="goods_price"><div class="am-text-middle" rowspan="1"><p>￥{{d.goods_price}}元</p></div></script>

<!-- 图片替换 -->
<script type="text/html" id="goods-thumb">
    <a href="{{d.goods_thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.goods_thumb}}" alt="藏品图片">
    </a>
</script>
<!-- 图片替换 -->

<!--藏品编号 -->
<script type="text/html" id="collection_number">
    <div style="cursor:pointer;" class="bl-table-name-title bl-Edit-title" data-id="{{d.id}}" data-name="{{d.collection_number}}">
        {{d.collection_number}} <i class="layui-icon layui-icon-survey" style="font-size: 18px !important;"></i>
    </div>
</script>
<!-- 藏品编号 -->

<!-- 商品状态  -->
<script type="text/html" id="status">
    <div class="am-text-middle" rowspan="1">
        <p><span class="layui-badge {{d.cast_status == '0'? 'layui-bg-blue' :'' }}">
                    {{#  if(d.cast_status === 1){ }}铸造中
                    {{# } else if(d.cast_status === 2){ }}铸造成功
                    {{# } else if(d.cast_status === 3){ }}铸造失败
                    {{#  } }}
            </span></p>
    </div>
</script>
<!-- 商品状态  -->

<script type="text/html" id="goods_no"><div class="am-text-middle" rowspan="1"><p>{{d.goods_no}}</p></div></script>
<script type="text/html" id="hash_url"><div class="am-text-middle" rowspan="1"><p>{{d.hash_url}}</p></div></script>

<!--挂售时间 -->
<script type="text/html" id="list_time">
    <div class="am-text-middle" rowspan="1"><p>{{d.list_time?d.list_time:'--'}}</p></div>
</script>
<!-- 挂售时间 -->

<!-- 藏品来源  -->
<script type="text/html" id="source_type">
    <div class="am-text-middle" rowspan="1">
        {{# if(d.source_type === 1){ }}<p><span class="layui-badge layui-bg-blue">抢购</span></p>
        {{# } else if(d.source_type === 2){ }}<p><span class="layui-badge layui-bg-blue">预约</span></p>
        {{# } else if(d.source_type === 3){ }}<p><span class="layui-badge layui-bg-blue">盲盒</span></p>
        {{# } else if(d.source_type === 4){ }}<p><span class="layui-badge layui-bg-blue">空投</span></p>
        {{# } else if(d.source_type === 5){ }}<p><span class="layui-badge layui-bg-blue">合成</span></p>
        {{# } else if(d.source_type === 6){ }}<p><span class="layui-badge layui-bg-blue">兑换码</span></p>
        {{# } else if(d.source_type === 7){ }}<p><span class="layui-badge layui-bg-blue">空投卷</span></p>
        {{# } else if(d.source_type === 8){ }}<p><span class="layui-badge layui-bg-blue">荣誉值</span></p>
        {{#  } }}
    </div>
</script>
<!-- 藏品来源  -->

<!-- 操作按钮  -->
<script type="text/html" id="operate">
    <!-- 铸造失败可以重新发起铸造 -->
    {{#  if(d.cast_status === 3){ }}
        <div style="margin-top: 5px">
            <a type="button" class="layui-btn layui-btn-sm layui-btn-primary layui-border-blue" id="toCast" href="javascript:void(0)" data-id="{{d.id}}">重新铸造</a>
        </div>
    {{#  } }}
</script>
<!-- 操作按钮 -->