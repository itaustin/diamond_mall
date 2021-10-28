<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>
        发表评价
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description"
          content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.">

    <link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
    <link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
    <link rel="stylesheet" href="/assets/mobile/css/style.css">
    <script>
        var order_id = "{{:input('order_id')}}";
    </script>

</head>
<body ontouchstart>
<!--主体-->
<!--<header class="wy-header">-->
<!-- <div class="wy-header-icon-back"><span></span></div>-->
<!-- <div class="wy-header-title">发表评价</div>-->
<!--</header>-->
<div class="weui-content clear">

</div>
<div style="clear:both;margin-bottom:30%;"></div>
<div class="com-button evaluate">
    <a href="javascript:void(0);">
        发表评价
    </a>
</div>
<script src="/assets/mobile/lib/jquery-2.1.4.js">
</script>
<script src="/assets/mobile/lib/fastclick.js">
</script>
<script type="text/javascript" src="/assets/mobile/js/jquery.Spinner.js"></script>
<script>
    $(function () {
        FastClick.attach(document.body);
    });
</script>
<script src="/assets/mobile/js/jquery-weui.js"></script>
<script src="/assets/mobile/js/layer.js"></script>
<script src="/assets/mobile/js/comment.js">
</script>
</body>

</html>