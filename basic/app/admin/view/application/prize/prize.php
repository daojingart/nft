<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <ul class="layui-tab-title store_system">
                    <li><a href="{{:url('application.prize/index')}}">奖品管理</a></li>
                    <li  class="layui-this" ><a href="{{:url('application.prize/prize')}}" >奖品设置</a></li>
                    <li><a href="{{:url('application.prize/log')}}" >中奖记录</a></li>
                </ul>
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">规则详情</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">规则 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea type="text"  name="prize[rule]" >{{$values.rule ?? ''}}</textarea>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">抽奖消耗设置</div>
                            </div>

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">请选择抽奖消耗藏品 </label>
                                <div class="am-u-sm-8 am-u-end" id="goods_id_data">
                                    {{volist name="goods_check_data" id="vos"}}
                                    <select name="prize[goods_id][]" id="">
                                        {{volist name="goods" id="vo"}}
                                        <option value="{{$vos.goods_id}}">{{$vos.goods_name}}</option>
                                        {{/volist}}
                                    </select>
                                    {{/volist}}
                                </div>
                                <div class="am-u-sm-1 am-u-end">
                                    <i class="delete layui-icon layui-icon-close" id="goods_id_push"  style="font-size: 25px; color: #1E9FFF;"></i>
                                    <i class="layui-icon layui-icon-addition" id="goods_id_push"  style="font-size: 25px; color: #1E9FFF;margin-left: auto;"></i>
                                </div>
                            </div>




                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">抽奖开关</div>
                            </div>


                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-form-label form-require">开关</label>
                                <div class="am-u-sm-9 layui-form">
                                    <input type="radio" name="prize[status]" value="0" title="关闭" {{if $values.status==0}} checked {{/if}} >
                                    <input type="radio" name="prize[status]" value="1" title="开启" {{if $values.status==1}} checked {{/if}}  >
                                </div>
                            </div>


                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">邀请赠送抽奖次数</div>
                            </div>



                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-form-label form-require">赠送抽奖次数</label>
                                <div class="am-u-sm-9 layui-form">
                                    <input type="number" name="prize[prize_count]" value="{{$values.prize_count ?? ''}}" title="" >
                                </div>
                            </div>



                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">实名认证赠送抽奖次数</div>
                            </div>



                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-form-label form-require">赠送抽奖次数</label>
                                <div class="am-u-sm-9 layui-form">
                                    <input type="number" name="prize[authentication_prize_count]" value="{{$values.authentication_prize_count ?? ''}}" title="" >
                                </div>
                            </div>




                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">提交保存
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/assets/admin/amazeui.min.js"></script>
<script>
    $(function () {
        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();


        $(".delete").on("click", function() {
            $("#goods_id_data").empty()
        })
        $(document).on("click","#goods_id_push",function (){
            var html='  <select name="prize[goods_id][]" id="">{{volist name="goods" id="vo"}}<option {{if $values.goods_id==$vo.goods_id}} selected  {{/if}} value="{{$vo.goods_id}}">{{$vo.goods_name}}</option>{{/volist}}</select>';
            $("#goods_id_data").append(html)
        })
    });
</script>
