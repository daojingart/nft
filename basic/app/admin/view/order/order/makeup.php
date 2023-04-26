<body class="pear-container pear-container">
<div class="layui-card">
    <div class="layui-card-body" >
        <div class="layui-row layui-col-space12">
            <form class="layui-form" action="">
                <div class="layui-form-item">
                        <a class="layui-btn layui-btn-normal layui-bg-orange" id="importExcelCollection" href="javascript:void(0)">导入分润表单</a>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
<script>
    let table;

    let request_url = '';
    layui.use(['table', 'form', 'jquery','element','excel','laydate','upload'], function() {
        let element = layui.element;
        table = layui.table;
        form = layui.form;
        excel = layui.excel;
        laydate = layui.laydate;
        upload = layui.upload;
        let $ = layui.jquery;

        //执行表格批量导入空投
        uploadInst = upload.render({
            elem: '#importExcelCollection',
            /*method: 'POST',*/
            url: "{{:url('upload/originalUpload')}}",
            accept: 'file', //普通文件
            exts: 'xls|excel|xlsx', //导入表格
            auto: false,  //选择文件后不自动上传
            before: function (obj) {
                layer.load(); //上传loading
            },
            choose: function (obj) {  // 选择文件回调
                var files = obj.pushFile();
                var fileArr = Object.values(files);// 注意这里的数据需要是数组，所以需要转换一下
                //console.debug(fileArr)
                // 用完就清理掉，避免多次选中相同文件时出现问题
                for (var index in files) {
                    if (files.hasOwnProperty(index)) {
                        delete files[index];
                    }
                }
                collectionUploadExcel(fileArr); // 如果只需要最新选择的文件，可以这样写： uploadExcel([files.pop()])
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
        //处理空投藏品的表格
        function collectionUploadExcel(files) {
            var load = layer.load();
            try {
                excel.importExcel(files, {
                    // 读取数据的同时梳理数据
                    fields: {
                        'hy_bill_no' : 'B',
                        'order_sn' : 'C',
                        'pay_method' : 'D',
                    }
                }, function (data) {
                    var arr = new Array();
                    for(i = 2; i < data[0].sheet1.length; i++){
                        var tt = {
                            order_sn : data[0].sheet1[i].order_sn,
                            hy_bill_no : data[0].sheet1[i].hy_bill_no,
                            pay_method : data[0].sheet1[i].pay_method,
                        };
                        arr.push(tt);
                    }
                    $.ajax({
                        async: false,
                        url: "{{:url('order.order/makeup')}}",
                        type: 'post',
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded",
                        data: {
                            data : JSON.stringify(arr)
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
                                layer.close(load); //疯狂模式，关闭所有层
                                //表格导入失败后，重载文件上传
                                layer.alert(data.msg+"请重新上传",{icon : 2});
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


    });

</script>