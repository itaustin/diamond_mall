<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>添加地址</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">

    <link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
    <link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
    <link rel="stylesheet" href="/assets/mobile/css/style.css">

</head>
<body ontouchstart>
<!--主体-->
<!--<header class="wy-header">-->
<!--  <div class="wy-header-icon-back"><span></span></div>-->
<!--  <div class="wy-header-title">编辑地址</div>-->
<!--</header>-->
<div class="weui-content">
    <div class="weui-cells weui-cells_form wy-address-edit">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label wy-lab">收货人</label></div>
            <div class="weui-cell__bd"><input class="weui-input" type="text" name="name" placeholder="请输入收货人姓名"></div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label wy-lab">手机号</label></div>
            <div class="weui-cell__bd"><input class="weui-input" type="number" name="phone" pattern="[0-9]*"
                                              placeholder="请输入收货人联系电话"></div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label for="name" class="weui-label wy-lab">所在地区</label></div>
            <div class="weui-cell__bd"><input class="weui-input" id="address" name="region" type="text" value=""
                                              readonly="" data-code="420106" data-codes="420000,420100,420106"
                                              placeholder="请选择省、市、区"></div>
        </div>
        <div class="weui-cell weui-cell_select weui-cell_select-after custom_area" style="display: none;">
            <div class="weui-cell__hd">
                <label for="" class="weui-label">区域</label>
            </div>
            <div class="weui-cell__bd">
                <select class="weui-select" name="custom_area">
                    <option value="0">请选择区域</option>
                </select>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label wy-lab">详细地址</label></div>
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="detail" placeholder=""></textarea>
            </div>
        </div>
        <!--    <div class="weui-cell weui-cell_switch">-->
        <!--      <div class="weui-cell__bd">设为默认地址</div>-->
        <!--      <div class="weui-cell__ft"><input class="weui-switch" type="checkbox"></div>-->
        <!--    </div>-->
    </div>
    <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">添加</a>
    </div>

</div>

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
    });
</script>

<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/city-picker.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script>
    $(function () {
        var last_area = '';
        $("#address").cityPicker({
            title: "选择区域",
            onChange: function (picker, values, displayValues) {
                console.log(values, displayValues);
                last_area = displayValues;
            }
        });
        $(document).delegate(".toolbar-inner a","click",function () {
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/store/passport/get_custom",
                data : {
                    "region" : last_area[0] + "," + last_area[1] + "," + last_area[2]
                },
                success: function (result){
                    if(result.data.length > 0){
                        var html = `<option value="0">请选择区域</option>`;
                        result.data.forEach(function (value, key) {
                            html += `
                                <option value="${value.area_id}">${value.name}</option>
                            `;
                        });
                        $("select[name=custom_area]").empty().append(html);
                        $(".custom_area").hide();
                    }else{
                        $("select[name=custom_area]").empty().append(`<option value="0">请选择区域</option>`);
                        $(".custom_area").hide();
                    }
                }
            });
        });
        var token = window.localStorage.getItem("token");
        if (!token) {
            $.toast("未登录，系统即将引导您登陆");
            setTimeout(function () {
                location.href = "/?s=/mobile/mine";
            }, 1000);
            return false;
        }
        $("#showTooltips").click(function () {
            var obj = getUpdateData();
            if (obj) {
                $.ajax({
                    type: "post",
                    dataType: "json",
                    url: "/?s=/api/address/add",
                    data: obj,
                    beforeSend: function () {
                        layer.open({
                            type: 2,
                            shadeClose: false,
                        });
                    },
                    success: function (data) {
                        if (data.code == -1) {
                            $.toast("未登录，系统即将引导您登陆");
                            setTimeout(function () {
                                location.href = "/?s=/mobile/mine";
                            }, 1000);
                            return false;
                        } else if (data.code == 1) {
                            $.toast("添加成功");
                            setTimeout(function () {
                                history.back();
                            }, 500);
                        } else if(data.code == 3){
                            $.toast(data.msg);
                        } else {
                            $.toast("添加失败");
                        }
                    },
                    complete: function () {
                        layer.closeAll();
                    }
                });
            }
        });

        function getUpdateData() {
            var name = $("input[name=name]").val();
            var phone = $("input[name=phone]").val();
            var region = $("input[name=region]").val();
            var custom_area = $("select[name=custom_area] option:selected").val();
            var detail = $("textarea[name=detail]").val();
            if (name == "") {
                $.toast("收货姓名不能为空");
                return false;
            }
            if (phone == "") {
                $.toast("手机号不能为空");
                return false;
            }
            if (region == "") {
                $.toast("省、市、区不能为空");
                return false;
            } else {
                var reg = / /g;
                region = region.replace(reg, ',');
            }
            // if ($("select[name=custom_area] option").length > 1){
            //     if(custom_area == 0){
            //         $.toast("请选择区域");
            //         return false;
            //     }
            // }
            if (detail == "") {
                $.toast("详细地址不能为空");
                return false;
            }
            if ($("select[name=custom_area] option").length > 1){
                return obj = {
                    name: name,
                    phone: phone,
                    detail: detail,
                    region: region,
                    area_id: custom_area,
                    wxapp_id: 10001,
                    token: token
                };
            }else{
                return obj = {
                    name: name,
                    phone: phone,
                    detail: detail,
                    region: region,
                    wxapp_id: 10001,
                    token: token
                };
            }
        }
    });
</script>
</body>
</html>
