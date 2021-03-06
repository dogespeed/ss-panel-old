{include file='admin/main.tpl'}

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            用户编辑 #{$user->id}
            <small>Edit User</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div id="msg-success" class="alert alert-success alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="ok-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> 成功!</h4>

                    <p id="msg-success-p"></p>
                </div>
                <div id="msg-error" class="alert alert-warning alert-dismissable" style="display: none;">
                    <button type="button" class="close" id="error-close" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> 出错了!</h4>

                    <p id="msg-error-p"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="form-horizontal">
                            <div class="row">
                                <fieldset class="col-sm-6">
                                    <legend>帐号信息</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">邮箱</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="email" type="email" value="{$user->email}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">密码</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="pass" value="" placeholder="不修改时留空">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Telegram_ID</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="telegram_id" value="{$user->telegram_id}" placeholder="未绑定">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">是否管理员</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="is_admin">
                                                <option value="0" {if $user->is_admin==0}selected="selected"{/if}>
                                                    否
                                                </option>
                                                <option value="1" {if $user->is_admin==1}selected="selected"{/if}>
                                                    是
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">用户类型</label>

                                        <div class="col-sm-9">
                                            <select class="form-control" id="user_type">
                                                <option value="1" {if $user->user_type==1}selected="selected"{/if}>
                                                    1
                                                </option>
                                                <option value="2" {if $user->user_type==2}selected="selected"{/if}>
                                                    2
                                                </option>
                                                <option value="3" {if $user->user_type==3}selected="selected"{/if}>
                                                    3
                                                </option>
                                                <option value="4" {if $user->user_type==4}selected="selected"{/if}>
                                                    4
                                                </option>
                                                <option value="5" {if $user->user_type==5}selected="selected"{/if}>
                                                    5
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Shadowsocks状态</label>

                                        <div class="col-sm-9"><select class="form-control" id="enable">
                                                <option value="1" {if $user->enable==1}selected="selected"{/if}>
                                                    正常
                                                </option>
                                                <option value="0" {if $user->enable==0}selected="selected"{/if}>
                                                    禁用
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">面板登录</label>

                                        <div class="col-sm-9"><select class="form-control" id="allow_login">
                                                <option value="1" {if $user->allow_login==1}selected="selected"{/if}>
                                                    正常
                                                </option>
                                                <option value="0" {if $user->allow_login==0}selected="selected"{/if}>
                                                    禁用
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">AnyConnect 状态</label>

                                        <div class="col-sm-9"><select class="form-control" id="ac_enable">
                                                <option value="1" {if $user->ac_enable==1}selected="selected"{/if}>
                                                    开通
                                                </option>
                                                <option value="0" {if $user->ac_enable==0}selected="selected"{/if}>
                                                    禁用
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                </fieldset>
                                <fieldset class="col-sm-6">
                                    <legend>ShadowSocks连接信息</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">连接端口</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="port" type="number" value="{$user->port}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">连接密码</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="passwd" value="{$user->passwd}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">自定义加密</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="method" value="{$user->method}">
                                        </div>
                                    </div>
                                </fieldset>
                                {if $user->ac_enable}
                                <fieldset class="col-sm-6">
                                    <legend>AnyConnect连接信息</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">用户名</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ac_user_name" value="{$user->ac_user_name}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">密码</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ac_passwd" value="{$user->ac_passwd}">
                                        </div>
                                    </div>
                                </fieldset>
                                {else}
                                <input type="hidden" id="ac_user_name" value="{$user->ac_user_name}">
                                <input type="hidden" id="ac_passwd" value="{$user->ac_passwd}">
                                {/if}
                            </div>
                            <div class="row">
                                <fieldset class="col-sm-6">
                                    <legend>流量</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">总流量</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">
                                                <input class="form-control" id="transfer_enable" type="number"
                                                       value="{$user->enableTrafficInGB()}">

                                                <div class="input-group-addon">GiB</div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">已用流量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="traffic_usage" type="text"
                                                   value="{$user->usedTraffic()}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="col-sm-6">
                                    <legend>邀请</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">可用邀请数量</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="invite_num" type="number"
                                                   value="{$user->invite_num}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">邀请人ID</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="ref_by" type="number"
                                                   value="{$user->ref_by}" readonly>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="col-sm-6">
                                    <legend>捐赠</legend>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">捐赠金额</label>

                                        <div class="col-sm-9">
                                            <input class="form-control" id="donate_amount" type="number"
                                                   value="{$user->donate_amount}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">是否钦点</label>

                                        <div class="col-sm-9"><select class="form-control" id="is_protected">
                                                <option value="1" {if $user->is_protected==1}selected="selected"{/if}>
                                                    是
                                                </option>
                                                <option value="0" {if $user->is_protected==0}selected="selected"{/if}>
                                                    否
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="note" class="col-sm-3 control-label">备注</label>

                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="note" rows="3">{$user->note}</textarea>
                                        </div>
                                    </div>

                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" id="submit" name="action" value="add" class="btn btn-primary">修改</button>
                    </div>
                </div>
            </div>
            <!-- /.box -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
    $(document).ready(function () {
        function submit() {
            $.ajax({
                type: "PUT",
                url: "/admin/user/{$user->id}",
                dataType: "json",
                data: {
                    email: $("#email").val(),
                    pass: $("#pass").val(),
                    port: $("#port").val(),
                    passwd: $("#passwd").val(),
                    transfer_enable: $("#transfer_enable").val(),
                    invite_num: $("#invite_num").val(),
                    method: $("#method").val(),
                    enable: $("#enable").val(),
                    is_admin: $("#is_admin").val(),
                    user_type: $("#user_type").val(),
                    ac_enable: $("#ac_enable").val(),
                    ac_user_name: $("#ac_user_name").val(),
                    ac_passwd: $("#ac_passwd").val(),
                    ref_by: $("#ref_by").val(),
                    telegram_id: $("#telegram_id").val(),
                    donate_amount: $("#donate_amount").val(),
                    is_protected: $("#is_protected").val(),
                    allow_login: $("#allow_login").val(),
                    note: $("#note").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#msg-error").hide(100);
                        $("#msg-success").show(100);
                        $("#msg-success-p").html(data.msg);
                        window.setTimeout("location.href='/admin/user'", 2000);
                    } else {
                        $("#msg-error").hide(10);
                        $("#msg-error").show(100);
                        $("#msg-error-p").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#msg-error").hide(10);
                    $("#msg-error").show(100);
                    $("#msg-error-p").html("发生错误：" + jqXHR.status);
                }
            });
        }

        $("html").keydown(function (event) {
            if (event.keyCode == 13) {
                login();
            }
        });
        $("#submit").click(function () {
            submit();
        });
        $("#ok-close").click(function () {
            $("#msg-success").hide(100);
        });
        $("#error-close").click(function () {
            $("#msg-error").hide(100);
        });
    })
</script>


{include file='admin/footer.tpl'}
