<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>售后管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
    ">

    <link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
    <link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
    <link rel="stylesheet" href="/assets/mobile/css/style.css">
    <link rel="stylesheet" href="/assets/mobile/css/dropload.css">

</head>
<body ontouchstart>
<!--    <header class="wy-header" style="position:fixed; top:0; left:0; right:0; z-index:200;">-->
<!--      <div class="wy-header-icon-back"><span></span></div>-->
<!--      <div class="wy-header-title">订单管理</div>-->
<!--    </header>-->
    <div class='weui-content'>
      <div class="weui-tab">
        <div class="weui-navbar" style="position:fixed;left:0; right:0; height:44px; background:#fff;">
          <a class="weui-navbar__item proinfo-tab-tit font-14 weui-bar__item--on" οnclick="return false;" href="#all">全部</a>
          <a class="weui-navbar__item proinfo-tab-tit font-14 refund" οnclick="return false;" href="#refund">待处理</a>
        </div>
        <div class="weui-tab__bd proinfo-tab-con" style="">
          <div id="all" class="weui-tab__bd-item weui-tab__bd-item--active"></div>
          <div id="refund" class="weui-tab__bd-item"></div>
        </div>
      </div>
  </div>

<script src="/assets/mobile/lib/jquery-2.1.4.js"></script> 
<script src="/assets/mobile/lib/fastclick.js"></script> 
<script>
  $(function() {
    FastClick.attach(document.body);
  });
</script> 
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/dropload.min.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/order_lists.js"></script>
<script>

    let urls = {
        all: '/pages/order/index?type=all',
        payment: '/pages/order/index?type=payment',
        received: '/pages/order/index?type=received',
        refund: '/pages/order/refund/index',
    };

      $(document).on("click", ".ords-btn-dele", function() {
        $.confirm("您确定要删除此订单吗?", "确认删除?", function() {
          $.toast("订单已经删除!");
        }, function() {
          //取消操作
        });
      });
	  $(document).on("click", ".receipt", function() {
        $.alert("五星好评送蓝豆哦，赶快去评价吧！", "收货完成！");
      });

</script>
</body>
</html>
