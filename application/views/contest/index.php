<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <nav class="navbar" role="navigation">
                <div class="index-header">
                    <img src="<?php echo base_url('assets/img/cloud.png') ?>" class="img-responsive" alt="Responsive image">
                </div>
            </nav>
            <div class="col-md-12 column notice">
                <p>
                    <strong>百度轻应用hackathon评分系统</strong>
                </p>
            </div>

            <?php if($show_alert) { ?>
            <div class="col-md-12 column alert">
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4>
                        注意
                    </h4> {error_tip}
                </div>
            </div>
            <?php } ?>

            <form class="form-horizontal" role="form" action={submitUrl} method="post" onsubmit="return validate_form(this)">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-12">用户名</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="inputEmail3" name="name" onblur="check(this)"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-12">密码</label>
                    <div class="col-sm-12">
                        <input type="password" class="form-control" id="inputPassword3" name="password" onblur="check(this)"/>
                    </div>
                </div>
                <input type="hidden" name="act" value="signin" />
                <div class="form-group">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-default" id="btn_login" style="width:100%">登录</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function check(obj){
    if(obj.value.length<=0){
        obj.style.borderColor = "#FF0033";
    }else{
        obj.style.borderColor = "#33CC33";
    }
}

function notice(alerttxt){
    $('.alert').empty();
    $('.alert').html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>注意</h4>'+alerttxt+'</div>');
}

function validate_required(field,alerttxt)
{
    with (field)
    {
        if (value==null||value=="")
        {
            notice(alerttxt);
            return false
        }
        else {
            return true
        }
    }
}

function validate_form(thisform)
{
    with (thisform)
    {
        if (validate_required(name,"请输入用户名")==false)
        {
            name.focus();
            return false
        }
        if (validate_required(password,"请输入密码")==false)
        {
            password.focus();
            return false
        }
    }
}
</script>