<style>
    .layui-form-checked[lay-skin=primary] i {
        border-color: #1890ff!important;
        background-color: #1890ff00;
    }

</style>
<body class="pear-container pear-container">
<blockquote class="layui-elem-quote">
    <p>注意:1>自动标签功能;系统会每隔1个小时自动维护标签内的会员;凡是不在持有标签内的藏品的会员将自动移除，新增持有标签藏品的会员</p>
    <p>注意:2>请勿将非持有藏品的会员加入本标签,系统会自动剔除</p>
    <p>注意:3>快照功能解释：快照功能是在某个时间段内持有藏品的用户自动列出放到标签里面</p>
</blockquote>
<div class="layui-row layui-col-space10">
    <div class="layui-col-xs6 layui-col-md6">
        <div class="layui-card top-panel">
            <div class="layui-card-header">会员总数量</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value1">
                        {{$member_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="layui-col-xs6 layui-col-md6">
        <div class="layui-card top-panel">
            <div class="layui-card-header">总藏品数量</div>
            <div class="layui-card-body">
                <div class="layui-row layui-col-space5">
                    <div class="layui-col-xs12 layui-col-md12 top-panel-number" style="color: #28333E;" id="value4">
                        {{$goods_count}}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="layui-card" style="margin-top: 10px">
    <div class="layui-card-body">
        <div class="layui-row">
            <div class="layui-col-md3 layui-card-body layui-form bl-layui-left">
                {{if $label_type==2}}
                <a class="layui-btn layui-btn-normal synchronize" href="JavaScript:void(0)">同步标签数据</a>
                {{/if}}
<!--                {{if $label_type==1}}-->
<!--                <a class="layui-btn layui-btn-normal timeSnapshot" href="JavaScript:void(0)">时间快照</a>-->
<!--                {{/if}}-->
            </div>
            <div class="layui-col-md9 layui-card-body layui-form ">
                <input type="hidden" id="label_id" name="label_id" value="{{$label_id}}">
                <div class="layui-form-item bl-layui-right" style="float: right;">
                    <div class="layui-inline">
                        <input type="text" name="name" id="keyword" placeholder="请输入会员昵称" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <input type="text" name="phone" id="keyword" placeholder="请输入会员手机号" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
                            <i class="layui-icon layui-icon-search"></i>
                            提交查询
                        </button>
                        <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcel" href="javascript:void(0)">导入会员</a>
                        <a href="/assets/user_lable_tpl.xlsx" style="font-size: 12px">下载导入模板</a>
                    </div>
                </div>
            </div>

        </div>

        <table id="user-table" lay-filter="user-table"></table>
    </div>
</div>

</body>

<!--  账号状态 -->
<script type="text/html" id="user-enable">
    {{d.status == '1'?"启用":"禁用"}}
</script>
<!--   账号状态     -->

<!--   创建时间     -->
<script type="text/html" id="user-createTime">
    {{layui.util.toDateString(d.create_time, 'yyyy-MM-dd HH:mm:ss')}}
</script>
<!--    创建时间    -->

<script>
    layui.use(['table', 'form', 'jquery','excel','upload'], function() {
        let table = layui.table;
        let form = layui.form;
        let upload = layui.upload;
        let excel = layui.excel;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: 'ID',
                    field: 'id',
                    align: 'center',
                    type: 'checkbox'
                },
                {
                    title: '会员昵称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '会员手机号',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '优先购次数',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '优先购时间',
                    field: 'phone',
                    align: 'center'
                },
                {
                    title: '加入时间',
                    field: 'create_time',
                    align: 'center'
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    width: 130
                }
            ]
        ]

        let tableIns = table.render({
            elem: '#user-table',
            url: "{{:url('user.label/member')}}",
            page: true,
            cols: cols,
            skin: 'line',
            where: {
                'label_id': $("#label_id").val()
            }
        });


        //搜索条件查询操作
        form.on('submit(user-query)', function (data) {
            table.reload('user-table', {
                where: data.field
            })
            return false;
        });

        //监听操作
        table.on('tool(user-table)', function (obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });
        //删除
        window.remove = function (obj) {
            layer.confirm('确认删除这个会员吗', {
                icon: 3,
                title: '提示'
            }, function (index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('user.label/memberRemove')}}", {
                    'id': obj.data['id'],
                    'label_id': $("#label_id").val()
                }, function (result) {
                    layer.close(loading);
                    if (1 === result.code) {
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function () {
                            obj.del();
                        });
                    } else {
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                });

            });
        }

        /**
         * 同步会员标签数据
         */
        $(document).on("click", ".synchronize", function () {
            layer.confirm('确认执行自动打标签功能吗？系统会对持有这个标签藏品的用户自动进行标签整理', {
                icon: 3,
                title: '提示'
            }, function (index) {
                let loading = layer.load();
                $.getJSON("{{:url('user.label/automaticMember')}}", {'label_id': $("#label_id").val()}, function (result) {
                    layer.close(loading);
                    if (1 === result.code) {
                        tableIns.reload({
                            url: "{{:url('user.label/member')}}",
                            page: {
                                curr: 1,
                            },
                            cols: cols,
                            skin: 'line'
                        });
                    } else {
                        layer.msg(result.msg, {
                            icon: 2,
                            time: 1000
                        });
                    }
                })
            })
        })

        /**
         * 时间快照获取会员
         */
        $(document).on("click",".timeSnapshot",function(){
            let label_id = $("#label_id").val();
            layer.open({
                type: 2,
                title: '时间快照',
                shade: 0.1,
                offset: 'auto',
                area: ['80%','90%'],
                content:"{{:url('collection.blindbox/addgoods')}}?label_id="+label_id,
            });
        })

        //执行实例
        uploadInst = upload.render({
            elem: '#importExcel',
            /*method: 'POST',*/
            url: "{{:url('upload/originalUpload')}}",
            accept: 'file', //普通文件
            exts: 'xls|excel|xlsx', //导入表格
            auto: false,  //选择文件后不自动上传
            before: function (obj) {
                layer.load(); //上传loading
            },
            choose: function (obj) {  // 选择文件回调
                layer.load();
                var files = obj.pushFile();
                var fileArr = Object.values(files);// 注意这里的数据需要是数组，所以需要转换一下
                //console.debug(fileArr)
                // 用完就清理掉，避免多次选中相同文件时出现问题
                for (var index in files) {
                    if (files.hasOwnProperty(index)) {
                        delete files[index];
                    }
                }
                uploadExcel(fileArr); // 如果只需要最新选择的文件，可以这样写： uploadExcel([files.pop()])
            },
            error : function(){
                setTimeout(function () {
                    layer.msg("上传失败！", {
                        icon : 1
                    });
                    //关闭所有弹出层
                    layer.closeAll(); //疯狂模式，关闭所有层
                },1000);
            }

        });

        function uploadExcel(files) {
            try {
                excel.importExcel(files, {
                    // 读取数据的同时梳理数据
                    fields: {
                        'phone' : 'A',
                    }
                }, function (data) {
                    // console.log(data);return;
                    var arr = new Array();
                    for(i = 1; i < data[0].Sheet1.length; i++){
                        var tt = {
                            phone : data[0].Sheet1[i].phone,
                        };
                        arr.push(tt);
                    }
                    $.ajax({
                        async: false,
                        url: "{{:url('user.label/importMember')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr),
                            label_id : $("#label_id").val()
                        },
                        success: function (data) {
                            if(data.code ==1){
                                layer.msg(data.msg);
                                setTimeout(function () {
                                    layer.closeAll(); //疯狂模式，关闭所有层
                                },1000);
                                //表格导入成功后，重载表格
                                location.reload();
                            }else{
                                //表格导入失败后，重载文件上传
                                layer.alert(data.error+"请重新上传",{icon : 2});
                            }
                        },
                        error: function (msg) {
                            layer.msg('请联系管理员!!!');
                        }
                    });
                });
            } catch (e) {
                layer.alert(e.message);
            }
        }


    })
</script>
