
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>修改个人资料</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link href="/assets/mobile/css/login.css" rel="stylesheet" type="text/css"/>
    <link href="/assets/mobile/css/paymentDialog.css" rel="stylesheet" type="text/css"/>

</head>
<body>

<section class="aui-flexView">
    <header class="aui-navBar aui-navBar-fixed b-line">
        <a href="javascript:;" class="aui-navBar-item" onclick="javascript:history.back(-1);">
            <i class="icon icon-return"></i>
        </a>
        <div class="aui-center">
            <span class="aui-center-title">修改个人资料</span>
        </div>
    </header>
    <section class="aui-scrollView">
        <div class="aui-code-box">
            <form action="">
                <p style="text-align:left;">支付宝真实姓名：</p>
                <p class="aui-code-line">
                    <input type="text" class="aui-code-line-input" name="alipay_name" value="" id="alipay_name" autocomplete="off" placeholder="请输入支付宝真实姓名"/>
                </p>
                <p style="text-align:left;">支付宝账号：</p>
                <p class="aui-code-line">
                    <input type="text" class="aui-code-line-input" name="alipay_account" value="" id="alipay_account" autocomplete="off" placeholder="请输入支付宝账号"/>
                </p>
                <p style="text-align:left;">手机号：</p>
                <p class="aui-code-line">
                    <input type="text" class="aui-code-line-input" name="mobile_phone" value="" id="mobile_phone" autocomplete="off" placeholder="请输入手机号"/>
                </p>
                <p style="text-align:left;">昵称：</p>
                <p class="aui-code-line">
                    <input type="text" class="aui-code-line-input" name="nickName" value="" id="nickName" autocomplete="off" placeholder="请输入昵称" />
                </p>
                <div class="aui-flex-links">
                    <input type="number" placeholder="输入验证码" class="aui-code-line-input erification-right" name="code" id="code">
                    <input type="button" id="btnSendCode1" class="feachBtn" style="width: 35%;font-size: 13px;" value="获取验证码">
                </div>
                <div class="aui-code-btn">
                    <button type="button" id="register">修改</button>
                </div>
            </form>
        </div>

    </section>
</section>

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/paymentDialog.js"></script>
<script>
    /*实例化*/
    var paymentDialog = new paymentDialog(function (ret,err) {
        /* this 指向 paymentDialog */
        $("#psw").val(ret.password);
        layer.open({
            skin : 'msg',
            content : '密码输入成功',
            time : 2
        });
        paymentDialog.close();
    });

    /*打开弹框*/
    $('#button_psw').click(function () {
        paymentDialog.open({
            money: 30
        });
    });
</script>
<script>
    var token = window.localStorage.getItem('token');
    if(token === null || token == ""){
        window.location.href = "/?s=/mobile/passport/login";
    }
    var phoneReg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57]|19[0-9]|16[0-9])[0-9]{8}$/;
    var count = 60;
    var InterValObj1;
    var curCount1;
    function sendMessage1() {
        curCount1 = count;
        $("#btnSendCode1").attr("disabled", "true");
        $("#btnSendCode1").val( + curCount1 + "秒再获取");
        InterValObj1 = window.setInterval(SetRemainTime1, 1000);

    }
    function SetRemainTime1() {
        if (curCount1 == 0) {
            window.clearInterval(InterValObj1);
            $("#btnSendCode1").removeAttr("disabled");
            $("#btnSendCode1").val("重新发送");
        } else {
            curCount1--;
            $("#btnSendCode1").val( + curCount1 + "秒再获取");
        }
    }
    $(function () {
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/user/detail",
            data : {
                token : window.localStorage.getItem('token'),
                wxapp_id : 10001
            },
            success : function (data) {
                var data = data.data.userInfo;
                $("#alipay_account").val(data.alipay_account);
                $("#alipay_name").val(data.alipay_name);
                $("#mobile_phone").val(data.mobile_phone);
                $("#nickName").val(data.nickName);
            }

        });
        $("#btnSendCode1").click(function () {
            $.ajax({
                type : "post",
                dataType : "json",
                data : {
                    token : window.localStorage.getItem('token'),
                    user_id : window.localStorage.getItem('user_id'),
                    wxapp_id : 10001
                },
                url : "/?s=/api/user/tokenSendMsg",
                success : function (data) {
                    if(data.code === 0){
                        layer.open({
                            skin : 'msg',
                            content : data.msg,
                            time : 2
                        });
                        return false;
                    }else if(data.code === 1){
                        sendMessage1();
                        layer.open({
                            skin : 'msg',
                            content : "短信下发成功，请注意查收",
                            time : 2
                        });
                    }
                }
            });
        });
        $("#register").click(function (m) {
            var alipay_account = $("#alipay_account").val();
            var alipay_name = $("#alipay_name").val();
            var mobile_phone = $("#mobile_phone").val();
            var nickName = $("#nickName").val();
            var code = $("#code").val();
            if(alipay_account === null || alipay_account == ""){
                layer.open({
                    skin : 'msg',
                    content : "请输入支付宝账号",
                    time : 2
                });
                return false;
            }
            if(code == ""){
                layer.open({
                    skin : 'msg',
                    content : "请输入手机验证码",
                    time : 2
                });
                return false;
            }
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user/changeAliPay",
                data : {
                    alipay_account : alipay_account,
                    alipay_name : alipay_name,
                    mobile_phone : mobile_phone,
                    nickName : nickName,
                    token : window.localStorage.getItem('token'),
                    user_id : window.localStorage.getItem('user_id'),
                    code : code,
                    wxapp_id : 10001
                },
                success: function (data) {
                    if(data.code === 0){
                        layer.open({
                            skin : 'msg',
                            content : data.msg,
                            time : 3
                        });
                        return false;
                    }else if(data.code === 1){
                        layer.open({
                            skin : 'msg',
                            content : data.msg,
                            time : 2
                        });
                        setTimeout(function () {
                            history.back();
                        },2000);
                    }
                }
            });
        });
    });
</script>

</body>
</html>
