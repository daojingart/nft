<script  id="order_list" type="text/template">
    <table id="user-table" lay-filter="user-table"></table>
</script>

<script type="text/html" id="ID"><div class="am-text-middle" rowspan="1"><p>{{d.id}}</p></div></script>
<script type="text/html" id="phone"><div class="am-text-middle" rowspan="1"><p>{{d.phone}}</p></div></script>
<script type="text/html" id="goods_price"><div class="am-text-middle" rowspan="1"><p>￥{{d.goods_price}}元</p></div></script>


<!-- 审核状态  -->
<script type="text/html" id="w_status">
    <div class="am-text-middle" rowspan="1">
        {{#  if(d.sale_status == 10){ }}
        <input lay-filter="putShelf" type="checkbox" value="10" title="已显示" checked data-goods-id="{{d.id}}">
        {{#  } else { }}
        <input lay-filter="putShelf" type="checkbox" value="20"  title="已隐藏" data-goods-id="{{d.id}}">
        {{#  } }}
    </div>
</script>
<!-- 审核状态  -->

<script type="text/html" id="goods-thumb">
    <a href="{{d.goods_thumb}}" title="点击查看大图" target="_blank">
        <img src="{{d.goods_thumb}}" alt="藏品图片">
    </a>
</script>

<!-- 审核状态  -->
<script type="text/html" id="type">
    <div class="am-text-middle" rowspan="1">
        {{#  if(d.goods_status === 1){ }}
        <span class="layui-badge">挂售中</span>
        {{# } else if(d.goods_status === 2){ }}
        <span class="layui-badge layui-bg-blue">交易中</span>
        {{#  } }}
    </div>
</script>
<!-- 审核状态  -->