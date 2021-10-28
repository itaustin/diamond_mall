<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>用户登录</title>
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" name="viewport"/>
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style"/>
    <meta content="telephone=no" name="format-detection"/>
    <link href="/assets/mobile/css/login.css" rel="stylesheet" type="text/css"/>
    <script>
        var code = "{{:input('code')}}";
    </script>

</head>
<body>
<section class="aui-flexView">
    <header class="aui-navBar aui-navBar-fixed b-line">
        <a href="javascript:;" class="aui-navBar-item" onclick="javascript:history.back(-1);">
            <i class="icon icon-return"></i>
        </a>
        <div class="aui-center">
            <span class="aui-center-title">登录</span>
        </div>
<!--        <a href="/?s=/mobile/passport/register" class="aui-navBar-item">-->
<!--            <i class="icon icon-sys"></i>注册-->
<!--        </a>-->
    </header>
    <section class="aui-scrollView">
        <div class="aui-code-box">
            <form  id="form" onsubmit="return false">
                <p class="aui-code-line">
                    <input type="text" class="aui-code-line-input" value="" name="mobile_phone" id="mobile_phone" autocomplete="off" placeholder="请输入手机号"/>
                </p>
                <p class="aui-code-line aui-code-line-clear">

                    <i class="aui-show  operate-eye-open"></i>
                    <input type="password" class="aui-code-line-input password" name="password" id="password" placeholder="密码" value="">
                </p>
                <div class="aui-flex-links">
                    <!-- <a href="javascript:;">
                         <label class="cell-right">
                             <input type="checkbox" value="1" name="checkbox">
                             <i class="cell-checkbox-icon"></i>记住密码
                         </label>
                     </a> -->
                    <a href="/?s=/mobile/passport/recover">忘记密码?</a>
                </div>
                <div class="aui-code-btn">
                    <button id="login_btu">登录</button>
                </div>
            </form>
        </div>
        <div class="aui-login-line">
            <h2>一键登入</h2>
        </div>
        <div class="aui-login-armor">
            <a href="javascript:;" class="wechat_login aui-tabBar-item">
                <img src="/assets/mobile/images/wechat.png" alt="">
            </a>
        </div>
    </section>
</section>

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script>
    if(isWechatBrowser()){
        layer.open({
            content: '<img src="/assets/mobile/images/tmh_app.jpg" /><br/><p style="text-align:center;">长按/扫码识别关注商城公众号</p>'
            ,skin: 'footer'
        });
    }
    $(".wechat_login").click(function () {
        var is_wechat = isWechatBrowser();
        if(is_wechat){
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/user/checkUser",
                data : {code:code,wxapp_id:10001},
                success:function (data) {
                    if(data.code === 10){
                        location.href=url;
                    }else if(data.code === 0){
                        layer.open({
                            skin : 'msg',
                            content : data.msg,
                            time : 2
                        });
                    }else if(data.code === 1){
                        window.localStorage.setItem('user_id',data.data.user_id);
                        window.localStorage.setItem('token',data.data.token);
                        location.href="/?s=/mobile/mine";
                    }
                }
            });
        }else{
            alert("无法使用微信快捷登录，请使用账号密码登录");
        }
        return false;
    });
    $("#login_btu").click(function () {
        var mobile_phone = $("#mobile_phone").val();
        var password = $("#password").val();
        if(mobile_phone == null || mobile_phone == ""){
            layer.open({
                skin : 'msg',
                content : '请填写手机号',
                time : 2
            });
            return false;
        }
        if(password == null || password == ""){
            layer.open({
                skin : 'msg',
                content : '请填写密码',
                time : 2
            });
            return false;
        }
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/user/passlogin",
            data : {
                username:mobile_phone,password,wxapp_id: 10001
            },
            success: function (data) {
                if(data.code === 1){
                    window.localStorage.setItem('user_id',data.data.user_id);
                    window.localStorage.setItem('token',data.data.token);
                    location.href="/?s=/mobile/mine";
                }else if(data.code === 0){
                    layer.open({
                        skin : 'msg',
                        content : data.msg,
                        time : 2
                    });
                    return false;
                }
            }
        });
    });

    function isWechatBrowser() {
        var agent = navigator.userAgent.toLowerCase();
        if (agent.match(/MicroMessenger/i) == "micromessenger") {
            return true;
        } else {
            return false;
        }
    }

</script>


</body>
</html>
