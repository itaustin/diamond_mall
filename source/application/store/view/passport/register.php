<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <title>省市区代理注册</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="renderer" content="webkit"/>
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="icon" type="image/png" href="/assets/common/i/favicon.ico"/>
    <link rel="stylesheet" href="/assets/store/css/login/style.css?v=<?= $version ?>"/>
</head>
<body class="page-login-v3">
<div class="container">
    <div id="wrapper" class="login-body">
        <div class="login-content">
            <div class="brand">
                <img alt="logo" class="brand-img" src="/assets/store/img/login/logo.png?v=<?= $version ?>">
<!--                <h2 class="brand-text">省市区代理注册</h2>-->
            </div>
            <form id="login-form" class="login-form">
                <div class="form-group">
                    <input id="user_name" name="User[user_name]" placeholder="请输入用户名(手机号)" type="number" required>
                    <input id="open_id" name="User[openid]" value="<?= $user_info['openid'] ?>" type="hidden" required>
                </div>
                <div class="form-group">
                    <input id="password" name="User[password]" placeholder="请输入登录密码" type="password" required>
                </div>
                <div class="form-group">
                    <input id="password_confirm" name="User[password_confirm]" placeholder="请再次输入登录密码" type="password" required>
                </div>
                <div class="form-group">
                    <input id="real_name" name="User[real_name]" placeholder="请输入真实姓名" type="text" required>
                </div>
                <div class="form-group">
                    <input id="mobile_code" name="User[code]" placeholder="请输入验证码" type="text" required>
                    <button id="btnSendCode1" style="background:#ff6849;">获取手机验证码</button>
                </div>
                <div class="form-group">
                    <button id="btn-submit" type="submit">
                        下一步
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
<script src="/assets/common/js/jquery.min.js"></script>
<script src="/assets/common/plugins/layer/layer.js?v=<?= $version ?>"></script>
<script src="/assets/common/js/jquery.form.min.js"></script>
<script>
    $(function () {
        var count = 60;
        var InterValObj1;
        var curCount1;
        function sendMessage1() {
            curCount1 = count;
            $("#btnSendCode1").attr("disabled", "true");
            $("#btnSendCode1").text( + curCount1 + "秒再获取");
            InterValObj1 = window.setInterval(SetRemainTime1, 1000);

        }
        function SetRemainTime1() {
            if (curCount1 == 0) {
                window.clearInterval(InterValObj1);
                $("#btnSendCode1").removeAttr("disabled");
                $("#btnSendCode1").text("重新发送");
            } else {
                curCount1--;
                $("#btnSendCode1").text( + curCount1 + "秒再获取");
            }
        }
        $("#btnSendCode1").click(function () {
            var user_name = $.trim($('#user_name').val());
            var pass = $.trim($('#password').val());
            var pass_confirm = $.trim($('#password_confirm').val());
            var real_name = $.trim($('#real_name').val());
            var mobile_code = $.trim($('#mobile_code').val());
            if(user_name == "" || pass == "" || pass_confirm == "" || real_name == ""){
                layer.msg("请将信息填写完整后获取验证码");
                return false;
            }
            $.ajax({
                type : "post",
                dataType : "json",
                data : {
                    mobile_phone : user_name,
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
            return false;
        });

        // 表单提交
        var $form = $('#login-form');
        $form.submit(function () {
            var $btn_submit = $('#btn-submit');
            $btn_submit.attr("disabled", true);
            $form.ajaxSubmit({
                type: "post",
                dataType: "json",
                // url: '',
                success: function (result) {
                    $btn_submit.attr('disabled', false);
                    if (result.code === 1) {
                        layer.msg(result.msg, {time: 1500, anim: 1}, function () {
                            window.location = result.url;
                        });
                        return true;
                    }
                    layer.msg(result.msg, {time: 1500, anim: 6});
                }
            });
            return false;
        });
    });
</script>
</html>
