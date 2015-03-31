<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-7-28
 * Time: 下午7:46
 */

class Login extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['submitUrl'] = base_url('/login/signin');
        $data['error_tip'] = $this->session->userdata('error_tip');
        if($data['error_tip']==''){
            $data['show_alert'] = false;
        }else{
            $data['show_alert'] = true;
        }
        $this->session->set_userdata('error_tip','');

        $this->load->view('templates/header', $data);
        $this->parser->parse('contest/index', $data);

    }

    public function signin()
    {

        if($this->input->post('act')=='signin'){
            //接受客户端数据
            $name = $this->input->post('name',true);
            $password = $this->input->post('password',true);

            $this->load->model('rater_model');

            if ($user = $this->rater_model->signin($name,$password)){
                //没权限
                if($user['role'] != Rater_model::ROLE_NORMAL) {
                    $this->session->set_userdata('error_tip', '没有权限！');
                    redirect(base_url('/login/index'));
                    die();
                }

                // session记录登陆者信息
                $users = array(
                    'name'  => $user['name'],
                    'username'  => $user['username'],
                    'userid'  => $user['id'],
                    'role'  => $user['role'],
                    'contestAuth' => $user['contestAuth'],
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($users);

                redirect(base_url('/'));

            }else{
                $this->session->set_userdata('error_tip', '用户名或者密码错误！');
            }

        } else {
            $this->session->set_userdata('error_tip', '非法登录！');
        }

        redirect(base_url('/login/index'));

    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login/index'));
    }

}