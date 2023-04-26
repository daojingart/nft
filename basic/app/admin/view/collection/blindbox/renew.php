<link rel="stylesheet" href="/assets/admin/css/amui/bladmin.css" />
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.config.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/admin/modules/ueditorplus/ueditor.all.js"></script>
<style>
    .am-form .am-form-file .upload-file1{
        font-size: 1.24rem;
        padding: 0.6em 1em;
    }
</style>
<div class="am-cf">
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12">
            <div class="widget am-cf">
                <form id="my-form" class="am-form tpl-form-line-form" method="post">
                    <input type="hidden" name="blindbox[product_types]" value="3">
                    <input type="hidden" name="blindbox[goods_id]" value="{{$model.goods_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本资料</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">盲盒名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="blindbox[goods_name]" value="<?= $model['goods_name'] ?>" placeholder="请输入盲盒名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属作者 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="blindbox[writer_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属作者',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($writer)): foreach ($writer as $first): ?>
                                            <option value="<?= $first['id'] ?>" <?= $model['writer_id'] == $first['id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分类 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="blindbox[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属分类',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($category)): foreach ($category as $first): ?>
                                            <option value="<?= $first['category_id'] ?>" <?= $model['category_id'] == $first['category_id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="am-margin-left-xs">
                                        <a  href="<?= url('collection.category/add') ?>">去添加</a>
                                    </small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">展示图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                            <i class="am-icon-cloud-upload"></i> 选择图片
                                        </button>
                                        <div class="uploader-list am-cf">
                                            <?php if (isset($model['goods_thumb'])):?>
                                                <div class="file-item">
                                                    <img src="<?= $model['goods_thumb'] ?>">
                                                    <input type="hidden" name="blindbox[goods_thumb]" value="<?= $model['goods_thumb'] ?>">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="help-block am-margin-top-sm">
                                        <small>尺寸750x750像素以上，大小2M以下</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情图片格式</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[is_suffix]" value="10" title="gif动态图片" {{if $model.is_suffix == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[is_suffix]" value="20" title="GLB3D展示"  {{if $model.is_suffix == 20}} checked {{/if}}>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">轮播图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file1 am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <?php if (isset($model['d_images'])): foreach ($model['d_images'] as $item):?>
                                                    <div class="file-item">
                                                        <img src="<?= $item ?>">
                                                        <input type="hidden" name="blindbox[d_images][]" value="<?= $item ?>">
                                                        <i class="iconfont icon-shanchu file-item-delete"></i>
                                                    </div>
                                                <?php endforeach; endif; ?>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸750x750像素以上，大小2M以下</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">购买配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品编码 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="blindbox[goods_no]" value="<?= $model['goods_no'] ?>" placeholder="请输入作品编码" required>
                                    <small>作品唯一性</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">限购数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="blindbox[buy_num]" value="<?= $model['buy_num'] ?>" placeholder="请输入限购数量" required>
                                    <small>0是不限制</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行库存 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="blindbox[stock_num]" value="<?= $model['stock_num'] ?>" placeholder="请输入发行数量" required>
                                    <small>作品发行库存量;使用实际发行的库存数量</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="blindbox[original_number]" <?= $model['original_number']?'disabled':'' ?> value="<?= $model['original_number'] ?>" placeholder="请输入发行数量" required>
                                    <small>发行数量一旦确定后不可更改;用于生成作品编号的最后使用规则</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">开售时间 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" id="start_time" name="blindbox[start_time]" placeholder="请输入开售时间" class="layui-input" value="<?= $model['start_time'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品售价 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="blindbox[goods_price]" placeholder="请输入作品售价" class="layui-input" value="<?= $model['goods_price'] ?>">
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">盲盒配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行方 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="blindbox[issue_name]" placeholder="请输入发行方" class="layui-input" value="<?= $model['issue_name'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售时间设置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[is_open_consignment]" value="10" title="系统配置" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[is_open_consignment]" value="20" title="单独设置" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 20}} checked {{/if}}>
                                    <input type="radio" name="blindbox[is_open_consignment]" value="30" title="关闭寄售" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 30}} checked {{/if}}>
                                    <small>根据不同的藏品单独设置寄售时间 或者直接关闭寄售</small>
                                </div>
                            </div>
                            <!-- 转增 -->
                            <div class="am-form-group consignment-paid-trainee"  style="display: <?= $model['is_open_consignment'] == 20 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设置转增时间</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="blindbox[consignment_minute]" placeholder="请输入寄售时间(分钟)" class="layui-input" value="{{$model.consignment_minute}}">
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行标签 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="blindbox[issue_tag]" placeholder="请输入发行标签" class="layui-input" value="<?= $model['issue_tag'] ?>">
                                    <small>","隔开 表示多个标签</small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">开盒方式</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[open_box_type]" value="10" title="概率开盒" {{if $model.open_box_type == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[open_box_type]" value="20" title="随机开盒" {{if $model.open_box_type == 20}} checked {{/if}}>
                                    <small>概率开盒按照设置概率系统计算开盒藏品,随机开盒不按照概率,系统随机抽取</small>
                                </div>
                            </div>

                            <!-- 限制价格 -->
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售价格限制配置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[limit_consignment_open]" value="10" title="系统配置" lay-filter="limit_consignment_open" {{if $model.limit_consignment_open == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[limit_consignment_open]" value="20" title="单独设置" lay-filter="limit_consignment_open" {{if $model.limit_consignment_open == 20}} checked {{/if}}>
                                    <small>可以针对单个藏品设置最高价和最低价</small>
                                </div>
                            </div>
                            <div class="am-form-group limit_consignment_open_content" style="display: <?= $model['limit_consignment_open'] == 20 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最高价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="blindbox[top_price_limit]" placeholder="请输入固定的限制最高价格" class="layui-input" value="{{$model.top_price_limit}}">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最低价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="blindbox[minimum_consignment]" placeholder="请输入固定的最低限制价格" class="layui-input" value="{{$model.minimum_consignment}}">
                                    </div>
                                </div>
                            </div>
                            <!-- 限制价格 -->

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">作品详情</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">作品详情 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <textarea id="container" name="blindbox[content]" type="text/plain"><?= $model['content'] ?></textarea>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="blindbox[goods_sort]" placeholder="请输入排序值" class="layui-input" value="<?= $model['goods_sort'] ?>">
                                </div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[goods_status]" value="10" title="显示" {{if $model.goods_status.value == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[goods_status]" value="20" title="隐藏" {{if $model.goods_status.value == 20}} checked {{/if}}>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">额外限购次数开关</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="blindbox[is_limit_number]" value="10" title="关闭" {{if $model.is_limit_number == 10}} checked {{/if}}>
                                    <input type="radio" name="blindbox[is_limit_number]" value="20" title="开启"  {{if $model.is_limit_number == 20}} checked {{/if}}>
                                    <small>开启后用户可以使用额外次数购买本藏品,用户有额外次数也可以重复购买本藏品</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3 am-margin-top-lg">
                                    <button type="submit" class="j-submit am-btn am-btn-secondary">确认提交</button>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 图片文件列表模板 -->
{{include file="layouts/_template/tpl_file_link_item" /}}
<!-- 文件库弹窗 -->
{{include file="layouts/_template/file_library" /}}

<script src="/assets/admin/amazeui.min.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.config.js"></script>
<script src="/assets/admin/modules/umeditor/umeditor.min.js"></script>

<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
        var element = layui.element;
        let laydate = layui.laydate;

        //  时间
        laydate.render({
            elem: '#start_time' //指定元素
            ,type: 'datetime'
            // ,format: 'HH:mm'
        });
        //是否开启寄售
        form.on('radio(is_open_consignment)', function(data){
            var $eduPaidMany = $('.consignment-paid-trainee');
            switch (data.value)
            {
                case "20":
                    $eduPaidMany.show();
                    break;
                case "10":
                    $eduPaidMany.hide()
                    break;
                case "30":
                    $eduPaidMany.hide()
                    break;
            }
        });

        //价格限制
        form.on('radio(limit_consignment_open)', function(data){
            var $eduPaidMany = $('.limit_consignment_open_content');
            switch (data.value)
            {
                case "10":
                    $eduPaidMany.hide()
                    break;
                case "20":
                    $eduPaidMany.show();
                    break;
            }
        });

        layui.code();
    });

    $(function () {

        // 富文本编辑器
        UE.getEditor('container');

        // 选择图片
        $('.upload-file').selectImages({
            name: 'blindbox[goods_thumb]',
        });

        // 选择图片
        $('.upload-file1').selectImages({
            name: 'blindbox[d_images][]'
            , multiple: true
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

    });
</script>
