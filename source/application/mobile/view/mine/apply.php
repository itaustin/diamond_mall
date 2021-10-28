<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>申请提现</title>
    <link rel="stylesheet" href="/assets/mobile/css/app.css">
    <link rel="stylesheet" href="/assets/mobile/css/apply.css">

</head>
<body>
<div class="container b-f">

    <!-- 头部背景图 -->
    <div class="dealer-bg">
        <img mode="widthFix" style="width:100%;" src="/assets/mobile/images/dealer-bg.png">
    </div>

    <div class="widget-body b-f dis-flex flex-dir-column flex-y-center">
        <form>
            <!-- 提现佣金 -->
            <div class="widget widget__capital m-top20 b-f dis-flex flex-dir-column">
                <div class="capital__item dis-flex flex-x-between flex-y-center">
                    <div class="item__left">可提现佣金：</div>
                    <div class="item__right c-violet">
                        <span class="f-24">￥</span>
                        <span class="f-34 dealer_money"></span>
                    </div>
                </div>
                <div class="capital__item dis-flex flex-y-center">
                    每笔提现订单扣除<span>3</span>元手续费
                </div>
                <div class="capital__item dis-flex flex-y-center">
                    <div class="item__left">提现金额：</div>
                    <div class="item__right flex-box">
                        <input name="money" style="width:206px;height:22.39px;" placeholder="请输入要提取的金额"/>
                    </div>
                </div>
            </div>
            <!-- 最低提现金额 -->
            <div class="capital__lowest m-top20 f-24 col-7 t-r">
                最低提现佣金<span class="min-money"></span>元
            </div>

            <!-- 提现方式 -->
            <div class="widget widget__form m-top20 b-f dis-flex flex-dir-column">
                <div class="form__title f-28">提现方式</div>
                <div class="form__box">
                    <block class="applyItem">

                    </block>
                </div>
            </div>
            <!-- 提交申请 -->
            <div class="form-submit dis-flex flex-x-center">
                <button type="submit">提交申请</button>
            </div>
        </form>
    </div>
</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script src="/assets/mobile/lib/fastclick.js"></script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function() {
        FastClick.attach(document.body);
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/mine/apply.js?v=<?php echo rand(1,999); ?>"></script>
</body>
</html>