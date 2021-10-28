
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0">
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <title>我的积分</title>
    <link type="text/css" rel="stylesheet" href="/paimai_assets/css/style.css" />
</head>

<body>
<div class="headerbox">
    <div class="header">
        <div class="headerL">
            <a onclick="javascript:history.back(-1)" class="goback"><img src="/paimai_assets/images/goback.png" /></a>
        </div>
        <div class="headerC">
            <p>积分</p>
        </div>
        <div class="headerR"></div>
    </div>
</div>
<div class="clear"></div>
<div class="hbox"></div>
<div class="jfheader">
<!--    <div class="guize">积分使用规则</div>-->
    <div class="jfnum">0.00</div>
    <div class="jfsub">小积分，有大用，多领一些屯起来！</div>
<!--    <div class="jfgl">-->
<!--        <ul>-->
<!--            <li>-->
<!--                <a href="javascript:void()">全部</a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="javascript:void()">收入</a>-->
<!--            </li>-->
<!--            <li>-->
<!--                <a href="javascript:void()">支出</a>-->
<!--            </li>-->
<!--        </ul>-->
<!--    </div>-->
</div>
<div class="clear"></div>
<div class="kbox"></div>
<div class="jfbox">

</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js"></script>
<script>
    $(function (){
        var token = window.localStorage.getItem("token");
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/mobile/mine/points",
            data : {
                token : token
            },
            success: function (data) {
                console.log(data);
                $(".jfnum").html(data.url.points + "<span>个</span>");
                let html = '';
                data.data.forEach(function (item, key) {
                    html += `
                        <div class="jfbox1">
                            <div class="jfbox1_R">
                                <div class="jfbox1_R1">
                                    <div class="v1">委托代卖费：<span style="color:red">￥${item.consignment_money}</span></div>
                                    <div class="v2">${item.create_time}</div>
                                </div>
                                <div class="jfbox1_R2">
                                    <div class="v3">委托代卖送积分</div>
                                    <div class="v4"><span style="color:red;font-size:12px;">+${item.points}积分</span></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    `;
                });
                if(html === ''){
                    $(".jfbox").html(`
                        <p style="text-align:center;font-size:14px;">暂无积分，快去赚取吧。</p>
                    `);
                }else{
                    $(".jfbox").html(html);
                }
            }
        });
    });
</script>
</body>
</html>
