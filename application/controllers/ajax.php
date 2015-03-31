<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:18
 */

class Ajax extends CI_Controller {

    /***
     * error code:
     * 0 成功
     * 1 未登录
     * 2 db_error
     */


    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')){
            $result = array(
                "error_code" => 1,
                "msg" => "未登录",
            );
            echo json_encode($result);
            exit();
        }

//        $users = array(
//            'name'  => "张三",
//            'username'  => 'aa',
//            'userid'  => 3,
//            'role'  => 0,
//            'contestAuth'  => 1,
//            'logged_in' => TRUE
//        );
//        $this->session->set_userdata($users);
    }

    public function getAllData() {
        //验证,权限，时间戳对比

        $this->load->model('team_model');
        $this->load->model('contest_model');
        $this->load->model('rate_model');
        $this->load->model('user_rate_model');

        //比赛数据
        $contestId = $this->session->userdata('contestAuth');
        $contestInfo = $this->contest_model->get_contest($contestId);
//        var_dump($contestInfo);

        //该比赛所有团队数据
        $teamData = $this->team_model->get_team_by_contest($contestId);
//        var_dump($teamData);

        //评分细则数据
        $rateRuleIdArr = explode(",",$contestInfo["rateRule"]);
        $rateRuleData = $this->rate_model->get_rate_in($rateRuleIdArr);
//        var_dump($rateRuleData);

        //评分数据
        $raterId = $this->session->userdata('userid');
        $rateData = $this->user_rate_model->get_user_rate_row_arr($raterId,'teamId');
//        var_dump($rateData);

        foreach($teamData as &$teamInfo) {
            //team获得评分细节数据
            $teamId = $teamInfo['id'];
            $rateDetailData = empty($rateData[$teamId]['rateDetail'])?array():json_decode($rateData[$teamId]['rateDetail']);
            $rateDetailArr = array();
            foreach($rateRuleData as $rateInfo) {
                $rateDetailArr[] = array(
                    "ratename" => $rateInfo['name'],
                    "detail" => $rateInfo['detail'],
                    "score" => isset($rateDetailData->$rateInfo['id'])?$rateDetailData->$rateInfo['id']:0,
                    "rateDetailId" => $rateInfo['id'],
                    "type" => $rateInfo['score'],  //0代表普通评分项输入框   1代表加分项
                    "rateMax" => intval($rateInfo['score']) //当type为1时且rateMax=1时，显示为checkbox, 其余情况均为input
                );
            }
            $teamInfo['scoreDetail'] = $rateDetailArr;

            if(empty($rateData[$teamId])) {
                $teamInfo['rateState'] = "badge unrated";
                $teamInfo['rateScore'] = "未评分";
            }else {
                $teamInfo['rateState'] = "badge rated";
                $teamInfo['rateScore'] = "已评分";
            }
        }

        $result = array(
            'contestId' => $contestId,
            'raterId' => $raterId,
            'teamData' => $teamData,
            'error_code' => (empty($contestId) || empty($raterId) || empty($teamData))? 2:0
        );

        if($this->input->get('debug') == 1) {
            var_dump($result);
        }else {
            $this->_outputJson($result);
        }
    }

    public function setAllData() {


//        $inputDatas =array(
//            'a' => array(
//                'teamId' => 3,
//                'rateDetail' => array(
//                    '2' => 9,
//                    '3' => 8,
//                ),
//            ),
//            'b' => array(
//                'teamId' => 4,
//                'rateDetail' => array(
//                    '2' => 9,
//                    '3' => 8,
//                ),
//            ),
//        );
//        object_array

        $inputDatas = json_decode($this->input->post("ratedata"),true);
        $this->load->model('user_rate_model');
        $dbResult = $this->user_rate_model->set_user_rate_batch($inputDatas);
        $result = array(
            'error_code' => empty($dbResult)?2:0,
        );

        $this->_outputJson($result);
    }

    private function _outputJson($data) {
        $jdata = json_encode($data);
        $this->output->set_content_type('application/json')
            ->set_output($jdata);
    }
}