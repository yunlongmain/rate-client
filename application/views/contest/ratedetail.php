<div class="container">
    <div class="row clearfix">
        <div class="col-md-12 column">
            <nav class="navbar" role="navigation">
                <div class="header">
                    <div class="back"><a href="<?php echo base_url('team') ?>"><img style="width: 30px;height: 30px" src="<?php echo base_url('assets/img/iconfont-back.png') ?>" alt="back"/></a></div>
                    <div class="title">{name}的评分详情</div>
                    <div class="exit"></div>

                </div>
            </nav>
        </div>
        <div class="col-md-12 column">
            <div class="jumbotron">
                <h3 style="margin-top: -20px; margin-left: -20px">{name}团队</h3>
                <?php if(isset($appName)&&$appName!=='') { ?>
                    <p style="font-size: 18px">作品名: {appName}</p>
                <?php } ?>
                <?php if(isset($appDesc)&&$appName!=='') { ?>
                    <p style="font-size: 18px">作品简介: {appDesc}</p>
                <?php } ?>
            </div>
        </div>
        <div class="col-md-12 column">
            <form action="{submitUrl}" method="post" onsubmit="return checkForm()">
                <table class="table">
                    <thead>
                    <tr>
                        <th class="tb-standard">
                            评分标准
                            </th>
                        <th class="tb-rate">
                            评分
                            </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($score as $item) {?>
                    <tr>
                        <td class="tb-standard">
                            <?php echo $item['detail'] ?>
                        </td>
                        <?php if($item['type']=='1'&&$item['rateMax']=='1') { ?>
                            <td class="tb-rate">
                                <div class="input-group input-group-sm">
                                    <?php if($item['score']=='1') { ?>
                                        <input type="checkbox" maxvalue="<?php echo $item['rateMax']; ?>" name="<?php echo $item['rateDetailId']; ?>" checked="checked" value="1" onchange="change(this)"/>
                                    <?php }else{ ?>
                                        <input type="checkbox" maxvalue="<?php echo $item['rateMax']; ?>" name="<?php echo $item['rateDetailId']; ?>" value="0" onchange="change(this)"/>
                                    <?php } ?>
                                </div>
                            </td>
                        <?php }else{ ?>
                            <td class="tb-rate">
                                <div class="input-group input-group-sm">
                                    <input type="number" maxvalue="<?php echo $item['rateMax']; ?>" name="<?php echo $item['rateDetailId']; ?>" class="rate form-control" placeholder="" value="<?php echo $item['score'] ?>">
                                </div>
                            </td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <input type="hidden" name="act" value="submitrate" />
                <input type="hidden" name="teamId" value={teamId} />
                <input type="hidden" name="isnew" value={isnew} />
                <button type="submit" class="btn btn-default" style="width: 100%" >提交</button>
            </form>
        </div>
    </div>
</div>
<script>

    function change(obj){
         if(obj.checked){
             obj.value='1';
         }else{
             obj.value='0';
         }
    }

    function validate_required(obj)
    {
            if(obj.value==''){
                obj.focus();
                alert('未打分，请打分!');
                return false
            }else if(obj.value>obj.getAttribute("maxvalue")){
                obj.focus();
                obj.value='';
                alert('评分超出范围,当前分数范围为0-'+obj.getAttribute("maxvalue")+'分,请重新为参赛队伍打分。');
                return false
            }else{
                return true
            }

    }

    function checkForm(){
        var inputArray = $('.input-group input');
        for(var key in inputArray){
            if(validate_required(inputArray[key])==false){
                 return false;
            }
        }
    }
</script>

