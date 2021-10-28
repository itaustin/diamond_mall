
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>注册</title>
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
            <span class="aui-center-title">注册</span>
        </div>
        <a href="/?s=/mobile/passport/login" class="aui-navBar-item">
            <i class="icon icon-sys"></i>登录
        </a>
    </header>
    <section class="aui-scrollView">
        <div class="aui-code-box">
            <form action="">
                <p class="aui-code-line">
                    <input type="number" class="aui-code-line-input" name="phone1" value="" id="phone1" autocomplete="off" placeholder="请输入手机号"/>
                </p>
                <p class="aui-code-line">
                    <input type="password" style="display:none;" id="psw" class="aui-code-line-input" placeholder="请输入密码" value="">
                    <input type="button" id="button_psw" class="aui-code-line-input" style="text-align:left;" value="请输入密码">
                </p>
<!--                <p class="aui-code-line">-->
<!--                    <i class="aui-show  operate-eye-open"></i> -->
<!--                    <input type="password" id="psw_ag" class="aui-code-line-input" placeholder="再次验证密码" value="">-->
<!--                </p>-->
                <p class="aui-code-line">
                    邀请码：<input type="text" class="aui-code-line-input erification-right" name="referee_code" value="" id="referee_code"  placeholder="请输入邀请码" />
                </p>
                <div class="aui-flex-links">
                    <input type="number" placeholder="输入验证码" class="aui-code-line-input erification-right" name="code" id="code">
                    <input type="button" id="btnSendCode1" class="feachBtn" style="width: 35%;font-size: 13px;" value="获取验证码">
                </div>

                <div class="aui-code-btn">
                    <button type="button" id="register">注册</button>
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
        $("#psw").css('display','block');
        $("#button_psw").css('display','none');
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
    var phoneReg = /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57]|19[0-9]|16[0-9])[0-9]{8}$/;
    var count = 60;
    var InterValObj1;
    var curCount1;
    function sendMessage1() {
        curCount1 = count;
        var phone = $.trim($('#phone1').val());
        // if (!phoneReg.test(phone)) {
        //     layer.open({
        //         skin : 'msg',
        //         content : "请输入有效的手机号码",
        //         time : 2
        //     });
        //     return false;
        // }
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
    var code = "{{:input('code')}}";
    window.localStorage.setItem('registerCode',code);
    var regCode = window.localStorage.getItem('registerCode');
    $(function () {
        $.get("/?s=/api/user/checkRegisterCoder",{code:regCode,wxapp_id:10001},function (data) {
            if(data.code === 1){
                $("#referee_code").val(data.data);
            }else if(data.code === 0){
                layer.open({
                    skin : 'msg',
                    content : '邀请码无效',
                    time : 2
                });
            }
        });
        $("#btnSendCode1").click(function () {
            var phone = $.trim($('#phone1').val());
            var pass = $.trim($('#psw').val());
            var nickName = $.trim($('#nick_name').val());
            if(phone === null || phone == "" && pass === null || pass == ""){
                layer.open({
                    skin: 'msg',
                    content : "请将信息填写完整后获取验证码",
                    time : 2
                });
                return false;
            }
            $.ajax({
                type : "post",
                dataType : "json",
                data : {
                    mobile_phone : phone,
                    wxapp_id : 10001
                },
                url : "/?s=/api/user/sendMsg",
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
            var phone = $.trim($('#phone1').val());
            var pass = $.trim($('#psw').val());
            console.log(pass);
            var nickName = $.trim($('#nick_name').val());
            var referee_code = $.trim($('#referee_code').val());
            var code = $.trim($('#code').val());
            // if (!phoneReg.test(phone)) {
            //     layer.open({
            //         skin : 'msg',
            //         content : "请输入有效的手机号码",
            //         time : 2
            //     });
            //     return false;
            // }
            if(pass === null || pass == ""){
                layer.open({
                    skin : 'msg',
                    content : "请输入密码",
                    time : 2
                });
                return false;
            }
            // if(nickName === null || nickName == ""){
            //     layer.open({
            //         skin : 'msg',
            //         content : "请输入昵称",
            //         time : 2
            //     });
            //     return false;
            // }
            if(referee_code === null || referee_code == ""){
                layer.open({
                    skin : 'msg',
                    content : '请输入邀请码',
                    time : 2
                });
                return false;
            }
            if(code === null || code == ""){
                layer.open({
                    skin : 'msg',
                    content : '请输入手机验证码',
                    time : 2
                });
                return false;
            }
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user/register",
                data : {
                    mobile_phone : phone,
                    password : pass,
                    nickName : nickName,
                    referee_id : referee_code,
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
                        window.localStorage.setItem('user_id',data.data.user_id);
                        window.localStorage.setItem('token',data.data.token);
                        location.href="/?s=/mobile/mine";
                    }
                }
            });
        });
    });
</script>

</body>
</html>
