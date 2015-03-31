<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:09
 */

class User_rate_model extends CI_Model{
    protected $tableName;

    public function __construct()
    {
        $this->load->database();
        $this->tableName = "user_rate";

    }

    public function get_user_rate_row($raterId,$teamId)
    {
        $this->db->where('raterId', $raterId);
        $this->db->where('teamId', $teamId);
        $query = $this->db->get($this->tableName);
        return $query->row_array();
    }

    public function get_user_rate_row_arr($raterId,$key='')
    {
        $this->db->where('raterId', $raterId);
        $query = $this->db->get($this->tableName);
        $result = $query->result_array();
        if(empty($key)) {
            return $result;
        } else {
            $keyResult = array();
            foreach($result as $v) {
                $keyResult[$v[$key]] = $v;
            }
            return $keyResult;
        }
    }

    public function set_user_rate()
    {
        $data = array(
            "teamId" => $this->input->post("teamId"),
            "contestId" => $this->session->userdata('contestAuth'),
            "raterId" => $this->session->userdata('userid'),
        );

        $rateRuleArr = array();
        foreach($this->input->post() as $k => $v) {
            if(substr($k, 0, 11) == 'rateDetail-') {
                $rateRuleId = intval(substr($k, 11));
                $rateRuleArr[$rateRuleId] = intval($v);
            }
        }

        $data['rateDetail'] = json_encode($rateRuleArr);
        $data['score'] = array_sum($rateRuleArr);
        $data['utime'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        if($this->input->post("isnew") == 1) {
            return $this->db->insert($this->tableName, $data);
        } else {
            $this->db->where("raterId",$data['raterId']);
            $this->db->where("teamId",$data['teamId']);
            return $this->db->update($this->tableName,$data);
        }
    }

    public function set_user_rate_ex($inputData)
    {
        $data = array(
            "teamId" => $inputData["teamId"],
            "contestId" => $this->session->userdata('contestAuth'),
            "raterId" => $this->session->userdata('userid'),
        );

//        $rateRuleArr = array();
//        foreach($inputData['rateDetail'] as $k => $v) {
//            $rateRuleArr[$k] = intval($v);
//        }

        $data['rateDetail'] = json_encode($inputData['rateDetail']);
        $data['score'] = array_sum($inputData['rateDetail']);
        $data['utime'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

        $this->db->where("raterId",$data['raterId']);
        $this->db->where("teamId",$data['teamId']);
        $this->db->update($this->tableName,$data);
        if($this->db->affected_rows() == 0) {
            $this->db->insert($this->tableName, $data);
        }

        return $this->db->affected_rows();
    }

    public function set_user_rate_batch($inputDatas) {
        $result = array();
        $maxCount = 100;
        $count = 0;
        foreach($inputDatas as $v) {
            if($count > $maxCount) {
                break;
            }
            $count++;
            $result[] = $this->set_user_rate_ex($v);
        }

        return $result;
    }

}