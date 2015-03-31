<div class="container-fluid">
    <div class="row clearfix">
        <div class="topbar col-md-12 column">
            <nav class="navbar" role="navigation">
                <div class="header">
                    <div class="back">
                        <img id="back_img" style="width: 30px;height: 30px;margin-left: 5px"
                             src="<?php echo base_url('assets/img/iconfont-daochutijiao.png') ?>" alt="upload"/>
                    </div>
                    <div class="title list-title">
                        <p style="margin-bottom: 0; font-size: 15px">{contestName}</p>

                        <p style="margin-bottom: 0;">评委:{username}</p>
                    </div>
                    <div class="exit">
                        <a href="<?php echo base_url('login/logout') ?>"><img
                                src="<?php echo base_url('assets/img/iconfont-tuichu.png') ?>" alt="注销" id="btn-exit"/></a>
                    </div>
                </div>
            </nav>
            <div id="warning_block">

            </div>
        </div>
        <div class="page col-md-12 column">
            <div class="panel-group" id="panel-268623">

            </div>
        </div>
    </div>
</div>

<script>

    $(function () {
        if (!window.localStorage) {
            alert("not support localstorage!");
            return;
        }

        //请求数据
        $.ajax({
            type: "post",
            url: '<?php echo base_url('ajax/getAllData') ?>',
            data: {},
            dataType: "json",
            success: function (data) {
                console.log(data);
                //第一次登陆
                if(!localStorage.getItem('current_contestId')&&!localStorage.getItem('current_raterId')){
                    initDataToLocalStorage(data);
                    localStorage.setItem("current_contestId", data['contestId']);
                    localStorage.setItem("current_raterId", data['raterId']);
                }else{

                    if(localStorage.getItem('current_raterId')!=data['raterId']){
                        //切换了用户
                        localStorage.setItem("current_contestId", data['contestId']);
                        localStorage.setItem("current_raterId", data['raterId']);

                        if(localStorage.getItem('teamData-rater-'+data['raterId'])){
                            //用户是老用户，使用旧数据
                            refreshPage(data['raterId']);
                        }else{
                            //用户是新用户，直接初始化数据
                            initDataToLocalStorage(data);
                        }
                    }else{
                        //原有用户登录，仅刷新页面
                        refreshPage(data['raterId']);
                    }
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

        function initDataToLocalStorage(data) {
            localStorage.setItem("teamData-rater-"+data['raterId'], JSON.stringify(data['teamData']));
            refreshPage(data['raterId']);
        }

        function updateLocalScore(teamId, scoreDetail) {
            var currentRaterId = localStorage.getItem('current_raterId');
            var localTempTeamData = JSON.parse(localStorage.getItem("teamData-rater-"+currentRaterId));
            for (var i = 0; i < localTempTeamData.length; i++) {
                if (localTempTeamData[i]['id'] == teamId) {
                    var totalScore = 0;
                    for (var j = 0; j < scoreDetail.length; j++) {
                        totalScore += parseInt(scoreDetail[j]['score']);
                        for (var z = 0; z < localTempTeamData[i]['scoreDetail'].length; z++) {
                            if (localTempTeamData[i]['scoreDetail'][z]['rateDetailId'] == scoreDetail[j]['rateDetailId']) {
                                localTempTeamData[i]['scoreDetail'][z]['score'] = scoreDetail[j]['score'];
                                break;
                            }
                        }
                    }
//                    localTempTeamData[i]['rateScore'] = '总评分:' + totalScore;
                    if(totalScore>0){
                        localTempTeamData[i]['rateScore'] = '已评分';
                        localTempTeamData[i]['rateState'] = 'badge rated';
                    }else{
                        localTempTeamData[i]['rateScore'] = '未评分';
                        localTempTeamData[i]['rateState'] = 'badge unrated';
                    }
                    break;
                }
            }
//            console.log(localTempTeamData);

            localStorage.setItem("teamData-rater-"+currentRaterId, JSON.stringify(localTempTeamData));
            refreshPage(currentRaterId);
        }

        function refreshPage(raterId) {
            $('#warning_block').html('');
            var htmlContent = "";
//            console.log("shuju"+raterId);
            var teamData = JSON.parse(localStorage.getItem("teamData-rater-"+raterId));

//            console.log(teamData);
            for (var i = 0; i < teamData.length; i++) {
                var team = teamData[i];
                htmlContent += '<div class="panel panel-default">';
                htmlContent += '<a data-toggle="collapse" data-parent="#panel-268623" href="#panel-element-' + team['id'] + '" class="panel-heading">';
                htmlContent += '<div class="panel-heading">';
                htmlContent += '<div class="panel-number"><span class="badge">' + team['teamDisplayId'] + '</span></div><div class="panel-title"><label>' + team['name'] + '</label></div>';
                htmlContent += '<div class="panel-rate pull-right"><span style="font-size: 15px; margin-top: 5px; margin-right: 10px" class="' + team['rateState'] + '">' + team['rateScore'] + '</span></div></div></a>';
                htmlContent += '<div id="panel-element-' + team['id'] + '" class="panel-collapse collapse"><div class="panel-body">';

                if (team['description'] != '') {
                    htmlContent += '团队描述：' + team['description'] + '<br>';
                }
                if (team['appName'] != '') {
                    htmlContent += '作品名称：' + team['appName'] + '<br>';
                }
                if (team['appDesc'] != '') {
                    htmlContent += '作品描述：' + team['appDesc'] + '<br>';
                }
                htmlContent += '<div><form action="" method="post" id="form-' + team['id'] + '" onsubmit="return checkForm()"><table class="table"><thead><tr><th class="tb-standard">评分标准</th><th class="tb-rate">评分</th></tr></thead>';
                htmlContent += '<tbody>';
                for (var j = 0; j < team['scoreDetail'].length; j++) {
                    var item = team['scoreDetail'][j];
                    htmlContent += '<tr><td class="tb-standard">' + item['ratename']+" (" +item['detail'] + ')</td>';
                    if (item['type'] == '1' && item['rateMax'] == '1') {
                        htmlContent += ' <td class="tb-rate"><div class="input-group-' + team['id'] + ' input-group-sm"><div class="checkbox-input-value">';
                        if (item['score'] == '1') {
                            htmlContent += '<input type="checkbox" maxvalue="' + item['rateMax'] + '" name="' + item['rateDetailId'] + '" class="rate-input-' + team['id'] + '"' + ' checked="checked" value="1" onchange="change(this)"/>';
                        } else {
                            htmlContent += '<input type="checkbox" maxvalue="' + item['rateMax'] + '" name="' + item['rateDetailId'] + '" class="rate-input-' + team['id'] + '"' +' value="0" onchange="change(this)"/>';
                        }
                        htmlContent += '</div></div></td>';

                    } else {
                        htmlContent += '<td class="tb-rate"><div class="input-group-' + team['id'] + ' input-group-sm"><input type="number" min=0 maxvalue="' + item['rateMax'] + '" name="' + item['rateDetailId'] + '" class="rate form-control rate-input-' + team['id'] + '" placeholder="" value="' + item['score'] + '"></div></td>';

                    }
                    htmlContent += '</tr>';
                }
                htmlContent += '</tbody></table>';
                htmlContent += '</form><button  class="btn btn-default btn-save" style="width: 100%" id="' + team['id'] + '">保存</button>';


                htmlContent += '</div></div></div>';
                htmlContent += '</div>';
            }
            $('#panel-268623').html(htmlContent);

            initSaveButton();
        }

        initSaveButton();
        function initSaveButton() {
            $('.btn-save').bind("click", function () {
                //校验合法性
                if(checkForm(this.id)){
                    teamId = this.id;
                    var inputArray = $('.rate-input-' + teamId);
                    var localScoreDetail = new Array();
                    for (var i = 0; i < inputArray.length; i++) {
                        var name = inputArray[i].name;
                        var value = inputArray[i].value;
                        localScoreDetail[i] = {'rateDetailId': name, 'score': value};
                    }
                    var scoreArray = $('#form-' + teamId).serializeJson();
//                console.log(scoreArray);
                    var checkboxArray = $('input.rate-input-' + teamId+':checkbox');
                    if(checkboxArray.length>0){
                        for(var k=0;k<checkboxArray.length;k++){
                            if(checkboxArray[k].checked){
                                scoreArray[checkboxArray[k].name]= "1";
                            }else{
                                scoreArray[checkboxArray[k].name]= "0";
//                            scoreArray.push({checkboxArray[k].name: 0});
                            }
                        }
                    }
                    console.log(scoreArray);
                    updatePostScore(teamId, scoreArray);
                    updateLocalScore(teamId, localScoreDetail);
                }
            });
        }

        function updatePostScore(teamId, scoreArray){
            var current_raterId = localStorage.getItem('current_raterId');
            if(localStorage.getItem('postData-'+current_raterId)){
                //本地存储有数据
                var postData = JSON.parse(localStorage.getItem('postData-'+current_raterId));
                var hasValue = false;
                for(var i=0;i<postData.length;i++){
                    if(postData[i]['teamId']==teamId){
                        postData[i]['rateDetail']= scoreArray;
                        hasValue = true;
                        break;
                    }
                }
                if(!hasValue){
                    //如果没有该条数据则添加数据
                    postData.push({'teamId':teamId ,'rateDetail':scoreArray});
                }
                //本地存储更新下数据
                localStorage.setItem('postData-'+current_raterId,JSON.stringify(postData));
            }else{
                //本地没有存储数据则进行一次存储
                var inputData = new Array();
                inputData.push({'teamId':teamId ,'rateDetail':scoreArray});
                localStorage.setItem('postData-'+current_raterId,JSON.stringify(inputData));
            }
            console.log(JSON.parse(localStorage.getItem('postData-'+current_raterId)));
        }

        $('#back_img').bind("click",function(){
            showNotice("正在进行评分数据上传!");
            postScore();
        });

        function postScore(){
            var current_raterId = localStorage.getItem('current_raterId');
            if(localStorage.getItem('postData-'+current_raterId)){
                var data = localStorage.getItem('postData-'+current_raterId);
                console.log(data);
                $.ajax({
                    type: "post",
                    url: '<?php echo base_url('ajax/setAllData') ?>',
                    data: {ratedata:data},
                    dataType: "json",
                    success: function (data) {
                        switch(data['error_code'])
                        {
                            case 0:
                                showNotice('上传成功');
                                break;
                            case 1:
//                                showNotice('上传失败：未登录');
                                window.location.href='<?php echo base_url('login') ?>';
                                break;
                            case 2:
                                showNotice('上传失败：刷新页面重新上传');
                                break;
                            default :
                                showNotice('未知错误');
                        }
                        console.log(data);
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        window.location.href='<?php echo base_url('login') ?>';
                        alert(errorThrown);
                    }
                });
            }else{
                showNotice("没有要上传的数据");
            }
        }

        $.fn.serializeJson = function () {
            var serializeObj = {};
            var array = this.serializeArray();
            var str = this.serialize();
            $(array).each(function () {
                if (serializeObj[this.name]) {
                    if ($.isArray(serializeObj[this.name])) {
                        serializeObj[this.name].push(this.value);
                    } else {
                        serializeObj[this.name] = [serializeObj[this.name], this.value];
                    }
                } else {
                    serializeObj[this.name] = this.value;
                }
            });
            return serializeObj;
        };

        function showNotice(text){
            var warning = '<div  class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><h4>注意</h4><p id="alert_text">';
            warning+= text + '</p></div>';
            $('#warning_block').html(warning);
        }



        function validate_required(obj) {
            if (obj.value == '') {
                obj.focus();
                showNotice('未打分，请打分!');
                return false
            } else if (parseInt(obj.value) > parseInt(obj.getAttribute("maxvalue")) ) {
                obj.focus();
                obj.value = '';
                showNotice('评分超出范围,当前分数范围为0-' + obj.getAttribute("maxvalue") + '分,请重新为参赛队伍打分。');
                return false
            } else {
                return true
            }

        }

        function checkForm(formId) {
            var inputArray = $('.rate-input-'+formId);
            for(var key=0;key<inputArray.length;key++){
                if (validate_required(inputArray[key]) == false) {
                    return false;
                }
            }
            return true;
        }
    })

    function change(obj) {
        if (obj.checked) {
            obj.value = '1';
        } else {
            obj.value = '0';
        }
    }

</script>