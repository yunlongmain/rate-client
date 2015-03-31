<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:18
 */

class Team extends Base {

    public function __construct()
    {
        parent::__construct();
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

    public function index() //团队列表页
    {
        $this->load->model('contest_model');
//        $this->load->model('team_model');
//        $this->load->model('rate_model');
//        $this->load->model('user_rate_model');


        //比赛数据
        $contestId = $this->session->userdata('contestAuth');
        $contestInfo = $this->contest_model->get_contest($contestId);

        $data['username'] = $this->session->userdata('name');
        $data['contestName']= $contestInfo['name'];

        $this->load->view('templates/header');
        $this->parser->parse('contest/teamlist',$data);
    }
}