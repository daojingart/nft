<script  id="order_list" type="text/template">
    <table id="user-table" lay-filter="user-table"></table>
</script>

<script type="text/html" id="ID"><div class="am-text-middle" rowspan="1"><p>{{d.id}}</p></div></script>
<script type="text/html" id="phone"><div class="am-text-middle" rowspan="1"><p>{{d.phone}}</p></div></script>
<script type="text/html" id="goods_price"><div class="am-text-middle" rowspan="1"><p>￥{{d.goods_price}}元</p></div></script>


<!-- 审核状态  -->
<script type="text/html" id="w_status">
    <div class="am-text-middle" rowspan="1">
        {{#  if(d.status === 0){ }}
            <span class="layui-badge">待审核</span>
        {{# } else if(d.status === 1){ }}
            <span class="layui-badge layui-bg-blue">审核成功</span>
        {{# } else if(d.status === 2){ }}
            <span class="layui-badge layui-bg-blue">审核拒绝</span>
        {{#  } }}
    </div>
</script>
<!-- 审核状态  -->

<!-- 审核状态  -->
<script type="text/html" id="type">
    <div class="am-text-middle" rowspan="1">
        {{#  if(d.type === 1){ }}
        <span class="layui-badge">支付宝</span>
        {{# } else if(d.type === 2){ }}
        <span class="layui-badge layui-bg-blue">银行卡</span>
        {{#  } }}
    </div>
</script>
<!-- 审核状态  -->