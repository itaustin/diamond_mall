<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>申请成为代理商</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="description" content="Write an awesome description for your new site here. You can edit this line in _config.yml. It will appear in your document head meta (for Google search results) and in your feed.xml site description.
">

<link rel="stylesheet" href="/assets/mobile/lib/weui.min.css">
<link rel="stylesheet" href="/assets/mobile/css/jquery-weui.css">
<link rel="stylesheet" href="/assets/mobile/css/style.css">
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        ul,li{
            list-style: none;
        }
        /*审核状态*/
        .line-box{
            display: block;
            width: 65%;
            height: 2px;
            background: #FFF;
            margin: 0 auto;
        }
        .line-box .line-inner{
            display: block;
            width: 25%;
            height: 2px;
            background: #00C6FF;
        }
        .status-box{
            display: flex;
            justify-content: space-around;
            color: #FFF;
            margin-top: -10px;
        }
        .status-box .dot-box{
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            position: relative;
            background: #FFF;
            box-shadow: 0 5px 10px 0 rgba(239,100,56,0.37);
        }
        .status-box .dot-box.on{
            background: #00C6FF;
        }
        .status-box .dot-inner{
            position: absolute;
            left: 7px;
            top: 7px;
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #FFF;
        }
    </style>

</head>
<body ontouchstart style="background:#323542;display:none;">
<!--主体-->
<div class="login-box">
  	<div class="lg-title">请填写申请信息</div>
    <div class="login-form">
    	<form action="#">
        	<div class="login-user-name common-div">
                <span class="eamil-icon common-icon" style="color:#fff;">
                	邀请人
                </span>
                <input type="text" name="dealer" value="" placeholder="" readonly />
            </div>
            <div class="login-user-name common-div">
            	<span class="eamil-icon common-icon" style="color:#fff;">
                	姓名
                </span>
                <input type="email" name="realname" value="" placeholder="请输入真实姓名" />
            </div>
            <div class="login-user-name common-div">
            	<span class="eamil-icon common-icon" style="color:#fff;">
                	手机号
                </span>
                <input type="number" name="phone" value="" placeholder="请输入您的手机号" />
            </div>

            <div class="weui_cell_ft" style="margin:0 auto;text-align: center;margin-bottom:20px;">
                <input class="weui_switch" id="checkbox" type="checkbox" style="margin:0 auto;"><span style="color:#ccc;">我已阅读并了解<span class="dealer">【代理商申请协议】</span></span>
            </div>

            <a href="javascript:;" class="login-btn common-div common-div-btn-apply">申请成为金牌代理</a>
        </form>
    </div>

<!--    <div class="forgets">-->
<!--    	<a href="psd_chage.html">忘记密码？</a>-->
<!--        <a href="regist.html">免费注册</a>-->
<!--    </div>-->
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
    var token = window.localStorage.getItem("token");
    var referee_id = window.localStorage.getItem("referee_id");

    $.ajax({
        type : "get",
        dataType : "json",
        url : "/?s=/api/user.dealer/apply",
        data : {
            referee_id : referee_id,
            token : token,
            wxapp_id : 10001
        },
        beforeSend: function(){
            $("input[name=dealer]").val('loading...');
        },
        success : function (data) {
            if(data.data.is_applying == true){
                $(".login-form").remove();
                $(".lg-title").text("您的申请已经受理，正在进行信息核验，请耐心等待。");
                $(".lg-title").css("font-size","13px");
                $(".login-box").append(`
                <div class="review-box">
                    <span class="line-box">
                        <span class="line-inner"></span>
                    </span>
                    <ul class="status-box">
                        <li>
                           <span class="dot-box">
                               <span class="dot-inner">
                               </span>
                           </span>
                            <p>待审核</p>
                        </li>
                        <li>
                           <span class="dot-box on">
                               <span class="dot-inner">
                               </span>
                           </span>
                            <p>审核中</p>
                        </li>
                        <li>
                           <span class="dot-box">
                               <span class="dot-inner">
                               </span>
                           </span>
                            <p>审核完成</p>
                        </li>
                    </ul>
                </div>
            `);
            }else if(data.data.is_dealer == true){
                location.href="/?s=/mobile/mine/dealer";
            }
            $("body").show();
            $("input[name=dealer]").val(data.data.referee_name+"（请核对）");
            $(".common-div-btn-apply").click(function () {
                var name = $("input[name=realname]").val();
                var mobile = $("input[name=phone]").val();
                var wxapp_id = 10001;
                if(name == ""){
                    $.toast("请输入姓名");
                }else if(mobile == ""){
                    $.toast("请输入手机号");
                }else{
                    var checked = $('#checkbox').is(':checked');
                    if(checked){
                        $.ajax({
                            type : "post",
                            dataType : "json",
                            url : "/?s=/api/user.dealer.apply/submit",
                            data : {
                                name : name,
                                mobile : mobile,
                                wxapp_id : wxapp_id,
                                token : window.localStorage.getItem("token")
                            },
                            beforeSend: function(){
                                layer.open({
                                    type : 2,
                                    shadeClose : false,
                                    content : "申请中..."
                                });
                            },
                            success : function (data) {
                                if(data.code == 1){
                                    $.toast("您的申请已受理，正在进行信息核验，请耐心等待。");
                                }
                            },
                            complete : function () {
                                layer.closeAll();
                            }
                        });
                    }else{
                        $.toast("请先勾选并阅读协议");
                    }
                }
            });
        }
    });
</script>

</body>
</html>
