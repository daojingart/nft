<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<style>
    .layui-form-checked span, .layui-form-checked:hover span {
        background-color: #219fff00 !important
    }
    .layui-form-checked[lay-skin=primary] i {
        border-color: #158efb!important;
        background-color: #ffb80000;
    }
    .layui-form-checkbox[lay-skin=primary]:hover i {
        border-color: #158efb!important;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <ul class="layui-tab-title store_system">
                    <li><a href="{{:url('application.task/index')}}">奖励设置</a></li>
                    <li><a href="{{:url('application.task/awardRecord')}}" >发奖记录</a></li>
                    <li class="layui-this"><a href="{{:url('application.task/todayTask')}}">拉新配置</a></li>
                </ul>
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">排行榜重置</div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="button" class="am-btn am-btn-secondary leaderboard_reset">排行榜重置</button>
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">上榜条件</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-2 am-form-label form-require">上榜条件</label>
                                <div class="am-u-sm-10 layui-form">
                                    {{if isset($values['condition_task'])}}
                                    <input type="radio" name="todaytask[condition_task]" value="1" title="下级消费"   <?= $values['condition_task'] === '1' ? 'checked' : '' ?>>
                                    <input type="radio" name="todaytask[condition_task]" value="2" title="实名认证"  <?= $values['condition_task'] === '2' ? 'checked' : '' ?>>
                                    {{else /}}
                                    <input type="radio" name="todaytask[condition_task]" value="1" title="下级消费"  checked>
                                    <input type="radio" name="todaytask[condition_task]" value="2" title="实名认证" >
                                    {{/if}}
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">排行榜公告</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">排行榜公告名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="todaytask[notice_name]"
                                           value="{{$values.notice_name ?? ''}}">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label">排行榜公告地址 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="todaytask[notice_address]"
                                           value="{{$values.notice_address ?? ''}}">
                                </div>
                            </div>



                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">每日任务设置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">每天签到赠送荣誉值 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="todaytask[sign_honor_num]"
                                           value="{{$values.sign_honor_num ?? ''}}"  required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-form-label form-require">实名认证赠送荣誉值 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="todaytask[honor_num]"
                                           value="{{$values.honor_num ?? ''}}" required>
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

        /**
         * 排行榜数据重置
         */
        $(document).on("click",".leaderboard_reset",function (){
            layer.confirm('确认重置排行榜吗？重置后之前数据无法找回', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('application.task/resetLeaderboardData')}}", function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {

                        });
                    }else{
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });
            });
        })
    });
</script>
