$(function () {
    //http://zuoweyshop.zuowey.com/index.php?s=/api/user.dealer/center&wxapp_id=10001&token=9f6212df31e55e552166a283304e1505
    var token = window.localStorage.getItem("token");
    var user_id = window.localStorage.getItem("user_id");
    $.get("/?s=/api/user.dealer/center",{
        wxapp_id : 10001,
        token : token
    },function (data) {
        if(data.code === -1){
            history.back();
            return false;
        }
        if(data.data.is_dealer === false){
            $.get("/?s=/mobile/mine/check_dealer_status",{user_id:user_id},function (data) {
                if(data.status == true){
                    location.href="/?s=/mobile/dealer/apply";
                }else{
                    alert("您未满足申请条件");
                    history.back();
                    return false;
                }
            });
        }else{
            var dealer_user = data.data.dealer.user;
            $(".avatarUrl").attr("src",data.data.dealer.user.avatarUrl);
            $(".user-nickName").text(dealer_user.nickName);
            $(".user-referee").text(data.data.dealer.referee ? "推荐会员：" + data.data.dealer.referee.nickName : "推荐会员：" + "平台");
            $(".withdraw_money").text("可提现 " + data.data.dealer.money + " 元");
            $(".freeze_money").text("待提现 " + data.data.dealer.freeze_money + " 元");
            $(".total_money").text(data.data.dealer.total_money);
            $("body").fadeIn();
            var token = localStorage.getItem("token");
            $.ajax({
                type : "get",
                dataType : "json",
                url : "/?s=/api/user.index/checkGrade",
                data : {
                    wxapp_id : 10001,
                    token : token
                },
                success: function (level) {
                    var text = dealer_user.nickName;
                    var x = "";
                    if(level.first !== ""){
                        for (var i = 1; i <= level.first;i++){
                            x += "☆";
                        }
                    }
                    $(".user-nickName").text(text + "  " + x);
                    $(".first").text("会员等级："+data.first);
                    $(".second").text("会员等级："+data.second);
                    $(".people").text("☆☆☆☆☆有效客户：" + data.peopleCount + "人");
                }
            });
        }
    });
});