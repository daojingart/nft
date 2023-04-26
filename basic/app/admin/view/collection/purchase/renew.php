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
                    <input type="hidden" name="purchase[product_types]" value="4">
                    <input type="hidden" name="purchase[goods_id]" value="{{$model.goods_id}}">
                    <div class="widget-body">
                        <fieldset>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">基本资料</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品名称 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" name="purchase[goods_name]" value="<?= $model['goods_name'] ?>" placeholder="请输入作品名称" required>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">商品图片 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <button type="button" class="upload-file am-btn am-btn-secondary am-radius">
                                            <i class="am-icon-cloud-upload"></i> 选择图片
                                        </button>
                                        <div class="uploader-list am-cf">
                                            <?php if (isset($model['goods_thumb'])):?>
                                                <div class="file-item">
                                                    <img src="<?= $model['goods_thumb'] ?>">
                                                    <input type="hidden" name="purchase[goods_thumb]" value="<?= $model['goods_thumb'] ?>">
                                                    <i class="iconfont icon-shanchu file-item-delete"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="help-block am-margin-top-sm">
                                        <small>尺寸750x750像素以上，大小2M以下 (可拖拽图片调整显示顺序 )</small>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情图片格式</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[is_suffix]" value="10" title="gif动态图片" {{if $model.is_suffix == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_suffix]" value="20" title="GLB3D展示"  {{if $model.is_suffix == 20}} checked {{/if}}>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">详情3D图 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button" class="upload-file1 am-btn am-btn-secondary am-radius">
                                                <i class="am-icon-cloud-upload"></i> 选择图片
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <?php if (isset($model['d_images'])):?>
                                                    <div class="file-item">
                                                        <img src="<?= $model['d_images'] ?>">
                                                        <input type="hidden" name="purchase[d_images]" value="<?= $model['d_images'] ?>">
                                                        <i class="iconfont icon-shanchu file-item-delete"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>尺寸750x750像素以上，大小2M以下 (可拖拽图片调整显示顺序 )</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属作者 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="purchase[writer_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属作者',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($writer)): foreach ($writer as $first): ?>
                                            <option value="<?= $first['id'] ?>" <?= $model['writer_id'] == $first['id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">所属分组 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <select name="purchase[category_id]" required data-am-selected="{searchBox: 1, btnSize: 'sm',  placeholder:'请选择所属分组',maxHeight:'200px'}">
                                        <option value=""></option>
                                        <?php if (isset($category)): foreach ($category as $first): ?>
                                            <option value="<?= $first['category_id'] ?>" <?= $model['category_id'] == $first['category_id'] ? 'selected' : '' ?>><?= $first['name'] ?></option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="am-margin-left-xs">
                                        <a href="<?= url('goods.category/add') ?>">去添加</a>
                                    </small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">藏品类型</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[goods_type]" value="1" title="视频"  lay-filter="goods_type"	{{if $model.goods_type == 1}} checked {{/if}}>
                                    <input type="radio" name="purchase[goods_type]" value="3" title="图片"  lay-filter="goods_type" {{if $model.goods_type == 3}} checked {{/if}}>
                                </div>
                            </div>
                            <!-- 视频 -->
                            <div class="am-form-group  edu-paid-many" id="main-video" style="display: <?= $model['goods_type'] == 1 ? 'block' : 'none' ?>;">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">视频内容</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button"  @click="vExampleAdd" class="upload-file am-btn am-btn-secondary am-radius">
                                                上传视频
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <input type="text" class="tpl-form-input video_link_url" name="purchase[video_link_url]" value="{{$model.video_link_url}}" required>
                                                <input type="file" style="display:none;" ref="vExampleFile" @change="vExampleUpload" />
                                            </div>
                                        </div>
                                        <div class="note_controller" style="display: none;margin-top: 10px;" >
                                            <div class="layui-progress layui-progress-big" lay-showPercent="true" lay-filter="demo">
                                                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>上传文件前,请先前往-->系统管理-->视频存储处配置点播配置；大文件上传比较慢,请耐心等待</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 视频 -->

                            <!-- 音频 -->
                            <div class="am-form-group edu-paid-trainee" id="main-audio" style="display: <?= $model['goods_type'] == 2 ? 'block' : 'none' ?>;">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">音频内容</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <div class="am-form-file">
                                        <div class="am-form-file">
                                            <button type="button"  @click="vExampleAdd" class="upload-file am-btn am-btn-secondary am-radius">
                                                上传音频
                                            </button>
                                            <div class="uploader-list am-cf">
                                                <input type="text" class="tpl-form-input audio_link_url" name="purchase[audio_link_url]" value="{{$model.audio_link_url}}" required>
                                                <input type="file" style="display:none;" ref="vExampleFile" @change="vExampleUpload" />
                                            </div>
                                        </div>
                                        <div class="note_controller" style="display: none;margin-top: 10px;" >
                                            <div class="layui-progress layui-progress-big" lay-showPercent="true" lay-filter="demo">
                                                <div class="layui-progress-bar layui-bg-blue" lay-percent="0%"></div>
                                            </div>
                                        </div>
                                        <div class="help-block am-margin-top-sm">
                                            <small>上传文件前,请先前往-->系统管理-->视频存储处配置点播配置；大文件上传比较慢,请耐心等待</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 音频 -->

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">购买配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品编码 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" class="tpl-form-input" maxlength="6" name="purchase[goods_no]" value="<?= $model['goods_no'] ?>" placeholder="请输入作品编码" required>
                                    <small>作品唯一性</small>
                                </div>
                            </div>
                            <input type="hidden" class="tpl-form-input" name="purchase[buy_num]" value="1" >

                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行库存 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="purchase[stock_num]" value="<?= $model['stock_num'] ?>" placeholder="请输入发行数量" required>
                                    <small>作品发行库存量;使用实际发行的库存数量</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" class="tpl-form-input" name="purchase[original_number]" <?= $model['original_number']?'disabled':'' ?> value="<?= $model['original_number'] ?>" placeholder="请输入发行数量" required>
                                    <small>发行数量一旦确定后不可更改;用于生成作品编号的最后使用规则</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">作品售价 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="purchase[goods_price]" placeholder="请输入作品售价" class="layui-input" value="<?= $model['goods_price'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">预约时间 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" id="start_time" name="time[appointment_start_time]" placeholder="请输入预约开始时间" class="layui-input" value="<?= $model['appointment_start_time'] ?>" style="width:150px;float: left;margin-right: 30px;">
                                    <input type="text" id="end_time" name="time[appointment_end_time]" placeholder="请输入预约结束时间" class="layui-input" value="<?= $model['appointment_end_time'] ?>" style="width:150px;">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">抽签时间 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" id="draw_time" name="purchase[draw_time]" placeholder="请输入抽签时间" class="layui-input" value="<?= $model['draw_time'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">售卖时间 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" id="now_start_time" name="purchase[start_time]" placeholder="请输入售卖时间" class="layui-input" value="<?= $model['start_time'] ?>">
                                    <small>备注：售卖时间需要大于抽签时间</small>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">初始预约人数 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="purchase[init_booking_num]" placeholder="请输入初始虚拟预约人数" class="layui-input" value="<?= $model['init_booking_num'] ?>">
                                </div>
                            </div>
                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">回购配置</div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">回收状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[recovery_status]" value="0" title="否" {{if $model.recovery_status == 0}} checked {{/if}}>
                                    <input type="radio" name="purchase[recovery_status]" value="1" title="是" {{if $model.recovery_status == 1}} checked {{/if}}>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">回收数量 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="purchase[recovery_num]" placeholder="请输入回收数量" class="layui-input" value="<?= $model['recovery_num'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">回收价格 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="purchase[recovery_price]" placeholder="请输入回收价格" class="layui-input" value="<?= $model['recovery_price'] ?>">
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">作品配置</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行方 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="purchase[issue_name]" placeholder="请输入发行方" class="layui-input" value="<?= $model['issue_name'] ?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">发行标签 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="text" name="purchase[issue_tag]" placeholder="请输入发行标签" class="layui-input" value="<?= $model['issue_tag'] ?>">
                                    <small>","隔开 表示多个标签</small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">额外限购次数开关</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[is_limit_number]" value="10" title="关闭" {{if $model.is_limit_number == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_limit_number]" value="20" title="开启"  {{if $model.is_limit_number == 20}} checked {{/if}}>
                                    <small>开启后用户可以使用额外次数购买本藏品,用户有额外次数也可以重复购买本藏品</small>
                                </div>
                            </div>
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">转增时间设置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[is_open_increase]" value="10" title="系统配置" lay-filter="is_open_increase" {{if $model.is_open_increase == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_open_increase]" value="20" title="单独设置" lay-filter="is_open_increase" {{if $model.is_open_increase == 20}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_open_increase]" value="30" title="关闭转增" lay-filter="is_open_increase" {{if $model.is_open_increase == 30}} checked {{/if}}>
                                    <small>根据不同的藏品单独设置转增时间 或者直接关闭转增</small>
                                </div>
                            </div>
                            <!-- 转增 -->
                            <div class="am-form-group individually-paid-trainee" style="display: <?= $model['is_open_increase'] == 20 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设置转增时间</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="purchase[increas_minute]" placeholder="请输入转增时间(分钟)" class="layui-input" value="{{$model.increas_minute}}">
                                    </div>
                                </div>
                            </div>
                            <!-- 转增 -->

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售时间设置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[is_open_consignment]" value="10" title="系统配置" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_open_consignment]" value="20" title="单独设置" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 20}} checked {{/if}}>
                                    <input type="radio" name="purchase[is_open_consignment]" value="30" title="关闭寄售" lay-filter="is_open_consignment" {{if $model.is_open_consignment == 30}} checked {{/if}}>
                                    <small>根据不同的藏品单独设置寄售时间 或者直接关闭寄售</small>
                                </div>
                            </div>
                            <!-- 转增 -->
                            <div class="am-form-group consignment-paid-trainee"  style="display: <?= $model['is_open_consignment'] == 20 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">设置转增时间</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="purchase[consignment_minute]" placeholder="请输入寄售时间(分钟)" class="layui-input" value="{{$model.consignment_minute}}">
                                    </div>
                                </div>
                            </div>
                            <!-- 转增 -->

                            <!-- 限制价格 -->
                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">寄售价格限制配置</label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[limit_consignment_open]" value="10" title="系统配置" lay-filter="limit_consignment_open" {{if $model.limit_consignment_open == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[limit_consignment_open]" value="20" title="单独设置" lay-filter="limit_consignment_open" {{if $model.limit_consignment_open == 20}} checked {{/if}}>
                                    <small>可以针对单个藏品设置最高价和最低价</small>
                                </div>
                            </div>
                            <div class="am-form-group limit_consignment_open_content" style="display: <?= $model['limit_consignment_open'] == 20 ? 'block' : 'none' ?>;">
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最高价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="purchase[top_price_limit]" placeholder="请输入固定的限制最高价格" class="layui-input" value="{{$model.top_price_limit}}">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">最低价</label>
                                    <div class="am-u-sm-9 am-u-end">
                                        <input type="text" name="purchase[minimum_consignment]" placeholder="请输入固定的最低限制价格" class="layui-input" value="{{$model.minimum_consignment}}">
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
                                    <textarea id="container" name="purchase[content]" type="text/plain"><?= $model['content'] ?></textarea>
                                </div>
                            </div>

                            <div class="widget-head am-cf">
                                <div class="widget-title am-fl">其他</div>
                            </div>
                            <div class="am-form-group">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label form-require">排序 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="number" name="purchase[goods_sort]" placeholder="请输入排序值" class="layui-input" value="<?= $model['goods_sort'] ?>">
                                </div>
                            </div>

                            <div class="am-form-group layui-form">
                                <label class="am-u-sm-3 am-u-lg-2 am-form-label">状态 </label>
                                <div class="am-u-sm-9 am-u-end">
                                    <input type="radio" name="purchase[goods_status]" value="10" title="显示" {{if $model.goods_status.value == 10}} checked {{/if}}>
                                    <input type="radio" name="purchase[goods_status]" value="20" title="隐藏" {{if $model.goods_status.value == 20}} checked {{/if}}>
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
<script src="/assets/resource/vue.js"></script>
<script src="/assets/resource/axios.min.js"></script>
<script src="/assets/resource/vod-js-sdk-v6.js"></script>

<script>
    layui.use(['form','element','code','laydate'], function() {
        var form = layui.form;
            element = layui.element;
        let laydate = layui.laydate;

        //  时间
        laydate.render({
            elem: '#start_time' //指定元素
            ,type: 'datetime'
        });
        //  时间
        laydate.render({
            elem: '#end_time' //指定元素
            ,type: 'datetime'
        });
        //  时间
        laydate.render({
            elem: '#draw_time' //指定元素
            ,type: 'datetime'
        });
        //  时间
        laydate.render({
            elem: '#now_start_time' //指定元素
            ,type: 'datetime'
        });


        // 售卖方式
        form.on('radio(goods_type)', function(data){
            var $eduPaidMany = $('.edu-paid-many');
            var $eduPaidTrainee = $('.edu-paid-trainee');

            console.log(data.value);

            switch (data.value)
            {
                case "1":
                    $eduPaidMany.show()&& $eduPaidTrainee.hide();
                    break;
                case "2":
                    $eduPaidMany.hide() && $eduPaidTrainee.show();
                    break;
                case "3":
                    $eduPaidMany.hide() && $eduPaidTrainee.hide();
                    break;
            }
        });

        //是否开启转增
        form.on('radio(is_open_increase)', function(data){
            var $eduPaidMany = $('.individually-paid-trainee');
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
            name: 'purchase[goods_thumb]',
        });

        // 选择图片
        $('.upload-file1').selectImages({
            name: 'purchase[d_images]',
        });

        /**
         * 表单验证提交
         * @type {*}
         */
        $('#my-form').superForm();

        /**
         * 计算签名。
         **/
        function getSignature() {
            return axios.post("{{:url('common/getSignature')}}", JSON.stringify({
                "Action": "GetUgcUploadSign"
            })).then(function (response) {
                return response.data;
            })
        }

        /**
         * 获取资源元信息
         * @param FileId
         * @returns {*}
         */
        function getDescribeMediaInfos(FileId) {
            return axios.post("{{:url('common/getDescribeMediaInfos')}}",{
                'FileId':FileId
            }).then(function (response) {
                console.log(response);
                $(".duration_length").val(response.data);
            })
        }

        // 上传视频
        let app = new Vue({
            el: '#main-video',
            data: {
                uploaderInfos: [],
                vcExampleVideoName: '',
                vcExampleCoverName: '',
                cExampleFileId: '',
                videoName:'',
            },
            created: function () {
                this.tcVod = new TcVod.default({
                    getSignature: getSignature
                })
            },
            methods: {
                /**
                 * 添加视频
                 **/
                vExampleAdd: function () {
                    this.$refs.vExampleFile.click();
                },
                /**
                 * 传视频过程。
                 **/
                vExampleUpload: function () {
                    var self = this;
                    var mediaFile = this.$refs.vExampleFile.files[0];
                    var arr_type = ['video/mp4']; //判断上传的类型是否符合播放的标准
                    if(arr_type.indexOf(mediaFile.type) == -1){
                        layer.alert("请先上传视频文件");return;
                    }
                    element.progress('demo', '0%');
                    var uploader = this.tcVod.upload({
                        mediaFile: mediaFile,
                    });
                    $(".note_controller").show();
                    //视频上传进度
                    uploader.on('media_progress', function (info) {
                        // console.log(info.percent);
                        let lenght = (info.percent)*100;
                        element.progress('demo', lenght + '%');
                    });
                    // console.log(uploader, 'uploader')
                    var uploaderInfo = {
                        videoInfo: uploader.videoInfo,
                        isVideoUploadSuccess: false,
                        isVideoUploadCancel: false,
                        progress: 0,
                        fileId: '',
                        videoUrl: '',
                        cancel: function() {
                            uploaderInfo.isVideoUploadCancel = true;
                            uploader.cancel()
                        },
                    };
                    this.uploaderInfos.push(uploaderInfo);
                    uploader.done().then(function(doneResult) {
                        //上传成功获取上传之后的视频或者音频资源文件
                        getDescribeMediaInfos(doneResult.fileId);
                        uploaderInfo.fileId = doneResult.fileId;
                        var  url_link = doneResult.video.url;  //播放视频音频的链接
                        $(".video_link_url").val(url_link);
                    }).then(function (videoUrl) {
                    })
                },

                setVcExampleVideoName: function () {
                    this.vcExampleVideoName = this.$refs.vcExampleVideo.files[0].name;
                },
                setVcExampleCoverName: function () {
                    this.vcExampleCoverName = this.$refs.vcExampleCover.files[0].name;
                },
            },
        })

        // 上传音频
        let apps = new Vue({
            el: '#main-audio',
            data: {
                uploaderInfos: [],
                vcExampleVideoName: '',
                vcExampleCoverName: '',
                cExampleFileId: '',
                videoName:'',
            },
            created: function () {
                this.tcVod = new TcVod.default({
                    getSignature: getSignature
                })
            },
            methods: {
                /**
                 * 添加视频
                 **/
                vExampleAdd: function () {
                    this.$refs.vExampleFile.click();
                },
                /**
                 * 传视频过程。
                 **/
                vExampleUpload: function () {
                    var self = this;
                    var mediaFile = this.$refs.vExampleFile.files[0];
                    var arr_type = ['audio/mp3','audio/mpeg']; //判断上传的类型是否符合播放的标准
                    if(arr_type.indexOf(mediaFile.type) == -1){
                        layer.alert("请上传mp3文件");return;
                    }
                    element.progress('demo', '0%');
                    var uploader = this.tcVod.upload({
                        mediaFile: mediaFile,
                    });
                    $(".note_controller").show();
                    //视频上传进度
                    uploader.on('media_progress', function (info) {
                        // console.log(info.percent);
                        let lenght = (info.percent)*100;
                        element.progress('demo', lenght + '%');
                    });
                    // console.log(uploader, 'uploader')
                    var uploaderInfo = {
                        videoInfo: uploader.videoInfo,
                        isVideoUploadSuccess: false,
                        isVideoUploadCancel: false,
                        progress: 0,
                        fileId: '',
                        videoUrl: '',
                        cancel: function() {
                            uploaderInfo.isVideoUploadCancel = true;
                            uploader.cancel()
                        },
                    };
                    this.uploaderInfos.push(uploaderInfo);
                    uploader.done().then(function(doneResult) {
                        //上传成功获取上传之后的视频或者音频资源文件
                        let duration = getDescribeMediaInfos(doneResult.fileId);
                        $(".duration_length").val(duration);
                        uploaderInfo.fileId = doneResult.fileId;
                        var  url_link = doneResult.video.url;  //播放视频音频的链接
                        $(".audio_link_url").val(url_link);
                    }).then(function (videoUrl) {
                    })
                },

                setVcExampleVideoName: function () {
                    this.vcExampleVideoName = this.$refs.vcExampleVideo.files[0].name;
                },
                setVcExampleCoverName: function () {
                    this.vcExampleCoverName = this.$refs.vcExampleCover.files[0].name;
                },
            },
        })

    });
</script>
