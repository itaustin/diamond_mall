<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>地址管理</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">

<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">

</head>
<body ontouchstart>
<!--主体-->
<!--<header class="wy-header">-->
<!--  <div class="wy-header-icon-back"><span></span></div>-->
<!--  <div class="wy-header-title">地址管理</div>-->
<!--</header>-->
<div class="weui-content">
  <div class="weui-panel address-box">

  </div>
  <div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" href="/?s=/mobile/address/address_add" id="showTooltips">添加收货地址</a>
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
<script>
    $(function () {
        var from = "{{:input('from')}}";
        if(from !== null || from !== ""){
            $("#showTooltips").attr("href","/?s=/mobile/address/address_add&from=" + from);
        }
        var token = window.localStorage.getItem("token");
        $.ajax({
            type : "post",
            dataType : "json",
            url : "/?s=/api/address/lists",
            data : {
                wxapp_id : 10001,
                token : token
            },
            success : function (data) {
                if(data.code == -1){
                    $.toast("未登录，系统即将引导您登陆");
                    setTimeout(function () {
                        location.href="/?s=/mobile/mine";
                    },1000);
                    return false;
                }
                var addressHtml = ``;
                data.data.list.forEach(item => {
                    var ischecked = item.address_id == data.data.default_id ? "checked" : "notcheck";
                    addressHtml += `
                        <div class="weui-panel__bd">
                          <div class="weui-media-box weui-media-box_text address-list-box">
                            <a href="/?s=/mobile/address/address_edit/aid/${item.address_id}" class="address-edit"></a>
                            <h4 class="weui-media-box__title"><span>${item.name}</span><span>${item.phone}</span></h4>
                            <p class="weui-media-box__desc address-txt">${item.region.province}-${item.region.city}-${item.region.region}${item.detail}</p>
                          </div>
                          <div class="weui-cell weui-cell_switch">
                              <div class="weui-cell__bd">选择</div>
                              <div class="weui-cell__ft"><input class="weui-switch" ${ischecked} name="checkthis" data-address-id="${item.address_id}" type="radio"></div>
                            </div>
                        </div>
                    `;
                });
                $(".address-box").html(addressHtml);
            }
        });
        $(document.body).delegate("input[type=radio]","click",function () {
            var address_id = $(this).data("address-id");
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/api/address/setDefault",
                data : {
                    address_id : address_id,
                    wxapp_id : 10001,
                    token : token
                },
                beforeSend: function(){
                    layer.open({
                        type : 2,
                        shadeClose: false,
                    });
                },
                success : function (data) {
                    if(data.code == -1){
                        $.toast("未登录，系统即将引导您登陆");
                        setTimeout(function () {
                            location.href="/?s=/mobile/mine";
                        },1000);
                        return false;
                    }else if(data.code == 1){
                        $.toast("设置成功");
                        setTimeout(function () {
                            if(from == "checkout"){
                                history.back();
                            }
                        },1000);
                    }else if(data.code == 0){
                        $.toast("已经是默认地址");
                        setTimeout(function () {
                            if(from == "checkout"){
                                history.back();
                            }
                        },1000);
                    }
                },
                complete: function () {
                    layer.closeAll();
                }
            });
        });
    });
</script>
</body>
</html>
