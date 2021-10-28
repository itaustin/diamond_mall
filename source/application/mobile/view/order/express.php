
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>查看物流</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>

    <link href="/assets/mobile/css/express.css" rel="stylesheet" type="text/css"/>

</head>
<body>
<section class="aui-flexView">
    <section class="aui-scrollView">
        <div class="aui-flex aui-flex-lag">
            <div class="aui-aircraft-img">
                <img src="/assets/mobile/images/icon-fj.png" alt="">
            </div>
            <div class="aui-flex-box">
                <h2>
                    物流公司：<em class="express_name"></em>
                </h2>
                <h2>
                    物流单号：<em class="express_no"></em>
                </h2>
            </div>
            <div class="aui-text-copy">复制
            </div>
        </div>
        <div class="divHeight"></div>
        <div class="aui-flex aui-flex-lag">
            <div class="aui-flex-box">
                <h2 style="color:#333">订单跟踪</h2>
            </div>
        </div>
        <div class="aui-timeLine b-line">
            <ul class="aui-timeLine-content">

            </ul>
        </div>
    </section>
</section>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
</body>
<script>
    $(function () {
        var token = window.localStorage.getItem("token");
        var order_id = "{{:input('order_id')}}";
        if(order_id == null || order_id == ""){
            layer.open({
                type : 0,
                content : "非法操作",
                shadeClose: false
            });
        }else{
            $.ajax({
                type : "get",
                url : "/?s=/api/user.order/express",
                data : {
                    order_id : order_id,
                    wxapp_id : 10001,
                    token : token
                },
                beforeSend: function(){
                    layer.open({
                        type : 2,
                        shadeClose : false,
                        content : "快递信息获取中..."
                    });
                },
                success : function (data) {
                    if(data.code == 0){
                        layer.open({
                            type : 0,
                            shadeClose : false,
                            content : data.msg
                        });
                    }else if(data.code == -1){
                        layer.open({
                            type : 0,
                            shadeClose : false,
                            content : "您未登录，即将引导您登陆"
                        });
                        setTimeout(function () {
                            location.href="/?s=/mobile/mine";
                        },1000);
                        return false;
                    }
                    var express = ``;
                    $(".express_name").text(data.data.express.express_name);
                    $(".express_no").text(data.data.express.express_no);
                    data.data.express.list.forEach(item => {
                        //var city = item.areaName ? `【${item.areaName}】` : "";
                        express += `
                        <li class="aui-timeLine-content-item">
                            <em class="aui-timeLine-content-icon"></em>
                            <p>${item.context}</p>
                            <p style="margin-top: 10px;">${item.time}</p>
                        </li>
                    `;
                    });
                    $(".aui-timeLine-content").html(express);
                },
                complete : function () {
                    layer.closeAll();
                }
            });
        }

    });
</script>
</html>
