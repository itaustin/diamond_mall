$(function () {
    var money;
    $("select[name=identity]").change(function () {
        var data = $(this).val();
        if(data === 'province'){
            $("#province").show();
            $("#city").hide();
            $("#region").hide();
            $("#area").hide();
        }else if(data === 'city'){
            $("#province").show();
            $("#city").show();
            $("#region").hide();
            $("#area").hide();
        }else if(data === 'region'){
            $("#province").show();
            $("#city").show();
            $("#region").show();
            $("#area").hide();
        }else if(data === 'area'){
            $("#province").show();
            $("#city").show();
            $("#region").show();
            $("#area").show();
        }
        $.ajax({
            type: "post",
            dataType: "json",
            url: "/?s=/store/passport/get_setting",
            data: {"key" : $(this).val()},
            success : function (data) {
                if(data.code === 1){
                    money = data['data']['order_num'] * data['data']['order_price'];
                    $("input[name=pay_price]").val(money);
                    $(".description").text('该代理费用为：' + money + '人民币' + ',任务系数：' + data['data']['task']);
                }else{
                    $(".description").text('');
                }
            }
        });
    });
    $("#province").change(function () {
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/store/passport/get_region",
            data : {
                id : $(this).val()
            },
            success: function (data) {
                var html = `<option value="0">请选择市区</option>`;
                data.data.forEach(function (value, key) {
                    html += `<option value="`+value.id+`">`+value.name+`</option>`;
                });
                $("#city").empty().append(html);
                $("#region").empty().append(`<option value="0">请选择区域</option>`);
            }
        });
    });
    $("#city").change(function () {
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/store/passport/get_region",
            data : {
                id : $(this).val()
            },
            success: function (data) {
                var html = `<option value="0">请选择区域</option>`;
                data.data.forEach(function (value, key) {
                    html += `<option value="`+value.id+`">`+value.name+`</option>`;
                });
                $("#region").empty().append(html);
            }
        });
    });
    $("#region").change(function () {
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/store/passport/get_custom",
            data : {
                id : $(this).val()
            },
            success: function (data) {
                var html = `<option value="0">请选择小区</option>`;
                data.data.forEach(function (value, key) {
                    html += `<option value="`+value.area_id+`">`+value.name+`</option>`;
                });
                $("#area").empty().append(html);
            }
        });
    });

    // 表单提交
    var $form = $('#login-form');
    $form.submit(function () {
        var $btn_submit = $('#btn-submit');
        var identity = $("#identity option:selected").val();
        var province = $("#province option:selected").val();
        var city = $("#city option:selected").val();
        var region = $("#region option:selected").val();
        var area =  $("#area").val();
        if(identity == 0){
            layer.msg("请选择代理身份", {time: 1500, anim: 1});
            return false;
        }
        if(identity == "province"){
            if (province == 0) {
                layer.msg("请选择省份", {time: 1500, anim: 1});
                return false;
            }
        }
        if(identity == "city"){
            if (province == 0) {
                layer.msg("请选择省份", {time: 1500, anim: 1});
                return false;
            }
            if(city == 0){
                layer.msg("请选择市区", {time: 1500, anim: 1});
                return false;
            }
        }
        if(identity == "region"){
            if (province == 0) {
                layer.msg("请选择省份", {time: 1500, anim: 1});
                return false;
            }
            if(city == 0){
                layer.msg("请选择市区", {time: 1500, anim: 1});
                return false;
            }
            if(region == 0) {
                layer.msg("请选择区域", {time: 1500, anim: 1});
                return false;
            }
        }
        if(identity == "area"){
            if (province == 0) {
                layer.msg("请选择省份", {time: 1500, anim: 1});
                return false;
            }
            if(city == 0){
                layer.msg("请选择市区", {time: 1500, anim: 1});
                return false;
            }
            if(region == 0) {
                layer.msg("请选择区域", {time: 1500, anim: 1});
                return false;
            }
            if(area == ""){
                layer.msg("请输入小区名称", {time: 1500, anim: 1});
                return false;
            }
            if($("#area").length <= 4){
                layer.msg("最低需输入4个字符", {time: 1500, anim: 1});
            }
        }
        layer.confirm('申请后无法修改代理区域,您确定要申请该区域代理?', {
            btn: ['确认','取消'] //按钮
        }, function(){
            $btn_submit.attr("disabled", true);
            $form.ajaxSubmit({
                type: "post",
                dataType: "json",
                // url: '',
                success: function (result) {
                    $btn_submit.attr('disabled', false);
                    if (result.code === 1) {
                        function onBridgeReady(){
                            WeixinJSBridge.invoke(
                                'getBrandWCPayRequest', {
                                    "appId":"wx38d44f6a1bc6904d",     //公众号名称，由商户传入
                                    "timeStamp":result.data.payment.timeStamp,         //时间戳，自1970年以来的秒数
                                    "nonceStr":result.data.payment.nonceStr, //随机串
                                    "package":"prepay_id="+result.data.payment.prepay_id,
                                    "signType":"MD5",         //微信签名方式：
                                    "paySign":result.data.payment.paySign //微信签名
                                },
                                function(res){
                                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                                        // 使用以上方式判断前端返回,微信团队郑重提示：
                                        // res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                        alert("支付成功");
                                        location.href = "/?s=/store/agent.agent/apply";
                                    }else{
                                        layer.msg("支付失败");
                                    }
                                });
                        }
                        if (typeof WeixinJSBridge == "undefined"){
                            if( document.addEventListener ){
                                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                            }else if (document.attachEvent){
                                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                            }
                        }else{
                            onBridgeReady();
                        }
                        // return true;
                    } else if(result.code === 2) {
                        layer.ready(function () {
                            layer.open({
                                type: 1,
                                closeBtn: 1,
                                skin: 'layui-layer-nobg', //没有背景色
                                shadeClose: true,
                                content: `<div style="background: #ffffff;width: 100%;padding:50px;">
                                        <!--<img src="/assets/store/img/zuowey_alipay.png" style="text-align:center;" height="320px"/>-->
                                        <br/>
                                        <h5 style="width:100%;text-align: center;">人工电话：0350-012345678<br/>对公账号名称：交通银行<br/>对公账户账号：6273 61725 71527 635</h5>
                                     </div>`
                            });
                        });
                        return false;
                    } else if(result.code === 3){
                        if(result.msg.province_id !== null && result.msg.region_id !== null && result.msg.city_id !== null && result.msg.area_id !== null){
                            var msg = '您有未支付的代理申请，地区为：<br/>【' + result.msg.province_id + "-" + result.msg.city_id + "-" + result.msg.region_id + "-" + result.msg.area_id + "】<br/>您要继续支付吗？";
                        } else if(result.msg.province_id !== null && result.msg.region_id !== null && result.msg.city_id !== null){
                            var msg = '您有未支付的代理申请，地区为：<br/>【' + result.msg.province_id + "-" + result.msg.city_id + "-" + result.msg.region_id + "】<br/>您要继续支付吗？";
                        } else if(result.msg.province_id !== null && result.msg.region_id !== null){
                            var msg = '您有未支付的代理申请，地区为：<br/>【' + result.msg.province_id + "-" + result.msg.city_id + "】<br/>您要继续支付吗？";
                        } else if(result.msg.province_id !== null){
                            var msg = '您有未支付的代理申请，地区为：<br/>【' + result.msg.province_id + "-" + "】<br/>您要继续支付吗？";
                        }
                        layer.confirm(msg, {
                            btn: ['继续支付','取消'] //按钮
                        }, function(){
                            if(result.data.pay_price > 1000){
                                layer.ready(function () {
                                    layer.open({
                                        type: 1,
                                        closeBtn: 1,
                                        skin: 'layui-layer-nobg', //没有背景色
                                        shadeClose: true,
                                        content: `<div style="background: #ffffff;width: 100%;">
                                        <img src="/assets/store/img/zuowey_alipay.png" style="text-align:center;" height="320px"/>
                                        <br/>
                                        <h5 style="width:100%;text-align: center;">支付后联系1xx83721728审批</h5>
                                     </div>`
                                    });
                                });
                            } else {
                                function onBridgeReady(){
                                    WeixinJSBridge.invoke(
                                        'getBrandWCPayRequest', {
                                            "appId":"wx38d44f6a1bc6904d",     //公众号名称，由商户传入
                                            "timeStamp":result.data.payment.timeStamp,         //时间戳，自1970年以来的秒数
                                            "nonceStr":result.data.payment.nonceStr, //随机串
                                            "package":"prepay_id="+result.data.payment.prepay_id,
                                            "signType":"MD5",         //微信签名方式：
                                            "paySign":result.data.payment.paySign //微信签名
                                        },
                                        function(res){
                                            if(res.err_msg == "get_brand_wcpay_request:ok" ){
                                                // 使用以上方式判断前端返回,微信团队郑重提示：
                                                // res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                                                alert("支付成功");
                                                location.href = "/?s=/store/agent.agent/apply";
                                            }else{
                                                layer.msg("支付失败");
                                            }
                                        });
                                }
                                if (typeof WeixinJSBridge == "undefined"){
                                    if( document.addEventListener ){
                                        document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                                    }else if (document.attachEvent){
                                        document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                                        document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                                    }
                                }else{
                                    onBridgeReady();
                                }
                            }
                        }, function(){

                        });
                        return false;
                    } else if(result.code === 10){
                        $(document.body).html(result.data);
                    }
                    layer.msg(result.msg, {time: 1500, anim: 6});
                }
            });
        }, function(){

        });
        return false;
    });
});