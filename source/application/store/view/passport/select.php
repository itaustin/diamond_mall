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
    <link rel="stylesheet" href="/assets/store/css/app.css?v=1.1.38"/>
    <link rel="stylesheet" href="/assets/store/css/login/style.css?v=<?= $version ?>"/>
</head>
<body class="page-login-v3">
<div class="container">
    <div id="wrapper" class="login-body">
        <div class="login-content">
            <div class="brand">
                <img alt="logo" class="brand-img" src="/assets/store/img/login/logo.png?v=<?= $version ?>">
                <h2 class="brand-text">代理身份选择</h2>
            </div>
            <form id="login-form" class="login-form">
                <div class="form-group">
                    <select name="identity" id="identity" required>
                        <option value="0">请选择代理身份</option>
                        <option value="province">省代理</option>
                        <option value="city">市代理</option>
                        <option value="region">区代理</option>
                        <option value="area">小区代理</option>
                    </select>
                    <input type="hidden" name="pay_price">
                </div>
                <div class="form-group">
                    <select name="province_id" id="province" data-am-selected style="display: none;">
                        <option value="0">点击选择</option>
                        <?php if (!empty($province)) : foreach ($province as $key => $value) : ?>
                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <select name="city_id" id="city" data-am-selected style="display: none;">
                        <option value="0">点击选择</option>
                    </select>
                    <select name="region_id" id="region" data-am-selected style="display: none;">
                        <option value="0">点击选择</option>
                    </select>
                </div>
                <div class="form-group">
                    <input name="area_id" style="display: none;" id="area" placeholder="请输入小区名称"/>
                </div>
                <input type="hidden" name="user_id" value="<?= $userinfo['store_user_id'] ?>">
                <div class="form-group">
                    <span class="description"></span>
                </div>
                <div class="form-group">
                    <button id="btn-submit" type="submit">
                        去支付代理费用
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
<script src="https://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script src="/assets/store/js/select.js?version=<?php echo rand(0,9999); ?>"></script>
<script>
    $(function () {
        TimeDown("show", 180039)
    });
    /*
     时间倒计时
     TimeDown.js
     */
    function TimeDown(id, value) {

        //倒计时的总秒数
        var totalSeconds = parseInt(value / 1000);

        //取模（余数）
        var modulo = totalSeconds % (60 * 60 * 24);
        //小时数
        var hours = Math.floor(modulo / (60 * 60));
        modulo = modulo % (60 * 60);
        //分钟
        var minutes = Math.floor(modulo / 60);
        //秒
        var seconds = modulo % 60;

        hours = hours.toString().length == 1 ? '0' + hours : hours;
        minutes = minutes.toString().length == 1 ? '0' + minutes : minutes;
        seconds = seconds.toString().length == 1 ? '0' + seconds : seconds;

        //输出到页面
        document.getElementById(id).innerHTML = hours + ":" + minutes + ":" + seconds;
        //延迟一秒执行自己
        if(hours == "00" && minutes == "00" && parseInt(seconds)-1<0){

        }else{
            setTimeout(function () {
                TimeDown(id, value-1000);
            }, 1000)
        }

    }
</script>
</html>
