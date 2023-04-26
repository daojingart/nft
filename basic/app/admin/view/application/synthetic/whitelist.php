
<link rel="stylesheet" href="/assets/admin/css/knowledge.css">
<style>
    .layui-table tbody tr td .layui-table-cell {
        height: 70px !important;
        line-height: 70px !important;
    }
</style>
<body class="pear-container pear-container">
<div class="layui-card">
	<!--        表格上方的数据-->
	<div class="layui-card-body">
		<div class="layui-col-md12 layui-form bl-layui-left">
			<div class="layui-form-item">
				<div class="layui-inline">
					<input type="text" name="name" id="keyword" placeholder="会员手机号" autocomplete="off" class="layui-input">
				</div>
				<div class="layui-inline">
					<button class="layui-btn layui-btn-normal" lay-submit lay-filter="user-query">
						<i class="layui-icon layui-icon-search"></i>
						提交查询
					</button>
				</div>
			</div>
		</div>
		<div class="layui-row layui-col-space12">
			<div class="layui-col-md3">
                <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcel" href="javascript:void(0)">导入会员</a>
                <a href="/assets/user_lable_tpl.xlsx" style="font-size: 12px">下载导入模板</a>			</div>
		</div>

	</div>
    <input type="hidden" id="synthetic_id" value="{{$id}}">
	<!--表格上方的数据-->
	<table id="user-table" lay-filter="user-table"></table>
</div>
</body>

<!-- 图片替换-->
<script>
    layui.use(['table', 'form', 'jquery','toast','dropdown','laydate','excel','upload'], function() {
        let table = layui.table;
        let form = layui.form;
        let toast = layui.toast;
        var laydate = layui.laydate;
        let dropdown = layui.dropdown;
        let excel = layui.excel;
        let upload = layui.upload;
        let $ = layui.jquery;
        let cols = [
            [
                {
                    title: '会员ID',
                    field: 'member_id',
                    align: 'center',
                },
                {
                    title: '手机号',
                    field: 'phone',
                    align: 'center',
                },
                {
                    title: '昵称',
                    field: 'name',
                    align: 'center',
                },
                {
                    title: '添加时间',
                    field: 'create_time',
                    align: 'center',
                },
                {
                    title: '操作',
                    field: 'operate',
                    align: 'center',
                    minWidth: 260,
                }
            ]
        ]

        //渲染加载数据
        let tableIns = table.render({
            elem: '#user-table',
            id:'videoID',
            url: "{{:url('application.synthetic/whitelist')}}",
            page: true,
            cols: cols,
            skin: 'line',
            done: function (res, curr, count) {
                localStorage.setItem("member_whitelist_curr",curr);//存储页码
            }
        });

        //日期范围
        laydate.render({
            elem: '#create_time'
            ,range: '~' //或 range: '~' 来自定义分割字符
            ,type: 'datetime'
        });


        //搜索条件查询操作
        form.on('submit(user-query)', function(data) {
            tableIns.reload({
                where: data.field,
                page: {
                    curr:1,
                },
            })
            return false;
        });

        table.on('tool(user-table)', function(obj) {
            //监听到事件的删除操作
            if (obj.event === 'remove') {
                window.remove(obj);
            }
        });

        window.remove = function(obj) {
            layer.confirm('确认删除吗？', {
                icon: 3,
                title: '提示'
            }, function(index) {
                layer.close(index);
                let loading = layer.load();
                $.getJSON("{{:url('application.synthetic/delwhitelist')}}", {'id' : obj.data['id']}, function(result){
                    layer.close(loading);
                    if(1 === result.code){
                        layer.msg(result.msg, {
                            icon: 1,
                            time: 1000
                        }, function() {
                            location.reload();
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
                        url: "{{:url('application.synthetic/addWhitelist')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr),
                            synthetic_id : $("#synthetic_id").val()
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