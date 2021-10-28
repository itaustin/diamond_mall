<div class="page-home row-content am-cf">

    <!-- 商城统计 -->
    <div class="row">
        <div class="am-u-sm-12 am-u-md-12 am-u-lg-12 am-margin-bottom">
            <div class="widget am-cf">
                <div class="widget-head">
                    <div class="widget-title">代理统计</div>
                </div>
                <div class="widget-body am-cf">
                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-3">
                        <div class="widget-card card__blue am-cf">
                            <div class="card-header">关于我的订单</div>
                            <div class="card-body">
                                <div class="card-value"><?= $data['widget-card']['order_total'] ?></div>
                                <div class="card-description">与我有分润关系的订单数量</div>
                                <span class="card-icon iconfont icon-only"></span>
                            </div>
                        </div>
                    </div>

                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-3">
                        <div class="widget-card card__red am-cf">
                            <div class="card-header">分润金额</div>
                            <div class="card-body">
                                <div class="card-value"><?= $data['widget-card']['user_total'] ?></div>
                                <div class="card-description">当前用户总分润金额</div>
                                <span class="card-icon iconfont icon-air"></span>
                            </div>
                        </div>
                    </div>

                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-3">
                        <div class="widget-card card__violet am-cf">
                            <div class="card-header">今日用户分润金额</div>
                            <div class="card-body">
                                <div class="card-value"><?= $data['widget-card']['today_user_total'] ?></div>
                                <div class="card-description">今日获得的分润</div>
                                <span class="card-icon iconfont icon-order"></span>
                            </div>
                        </div>
                    </div>

                    <div class="am-u-sm-12 am-u-md-6 am-u-lg-3">
                        <div class="widget-card card__primary am-cf">
                            <div class="card-header">冻结金额状态</div>
                            <div class="card-body">
                                <div class="card-value">
                                    <?= $data['widget-card']['frozen_money'] ?>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-success thaw">
                                            <span class="am-icon-facebook"></span> 解冻账户
                                        </a>
                                    </div>
                                    <div class="am-btn-group am-btn-group-xs">
                                        <a class="am-btn am-btn-default am-btn-danger freeze">
                                            <span class="am-icon-facebook"></span> 转入金额
                                        </a>
                                    </div>
                                </div>
                                <div class="card-description">
                                    <?php
                                    if($data['widget-card']['frozen_status']){
                                        echo "<span style='color: red;'>冻结中</span>";
                                    }else{
                                        echo "<span style='color: forestgreen;'>已解冻，48小时内未冻结代理自动取消，您将不会得到分润</span>";
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
<script src="/assets/common/js/echarts.min.js"></script>
<script src="/assets/common/js/echarts-walden.js"></script>
<script type="text/javascript">

    $(".thaw").click(function () {
        layer.confirm(`<p>请选择您要转出的账户
<select id="account">
        <option value="">请选择代理账户</option>
    <?php foreach ($apply as $key => $value): ?>
        <option value="<?php echo $value['apply_id']; ?>"><?php echo $value['province'] . $value["city"] . $value["region"] . $value["area_id"] ?></option>
    <?php endforeach; ?>
</select>
</p><br/>确认要解冻吗？解冻后48小时内该账户无金额时，系统将自动取消您在该区域的代理权限。`, {
            btn: ['确认','取消'] //按钮
        }, function(){
            var apply_id = $("#account option:selected").val();
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/store/agent.index/thaw",
                data : {
                    "apply_id" : apply_id
                },
                success : function (result) {
                    if (result.code === 1) {
                        layer.msg(result.msg, {time: 1500, anim: 1}, function () {
                            // window.location = result.url;
                        });
                        return true;
                    }
                    layer.msg(result.msg, {time: 1500, anim: 6});
                }
            });
        }, function(){

        });
    });

    $(".freeze").click(function () {
        layer.confirm(`请选择你要冻结的账户
                <select id="account">
                    <option value="">请选择代理账户</option>
                <?php foreach ($apply as $key => $value): ?>
                    <option value="<?php echo $value['apply_id']; ?>"><?php echo $value['province'] . $value["city"] . $value["region"] . $value["area_id"] ?></option>
                <?php endforeach; ?>
            </select>
<br/>`, {
            btn: ['确认','取消'] //按钮
        }, function(){
            var apply_id = $("#account option:selected").val();
            $.ajax({
                type : "post",
                dataType : "json",
                url : "/?s=/store/agent.index/freeze",
                data : {
                    "apply_id" : apply_id
                },
                success : function (result) {
                    if (result.code === 1) {
                        layer.msg(result.msg, {time: 1500, anim: 1}, function () {
                            // window.location = result.url;
                        });
                        return true;
                    }
                    layer.msg(result.msg, {time: 1500, anim: 6});
                }
            });
        }, function(){

        });
    });

</script>