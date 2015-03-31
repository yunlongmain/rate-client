<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:09
 */


class Rater_model extends CI_Model{
    protected $tableName;

    const ROLE_NORMAL = 0;
    const ROLE_ADMIN = 1;
    const ROLE_SUPER_ADMIN = 2;

    public function __construct()
    {
        $this->load->database();
        $this->tableName = "rater";
    }

    public function get_rater($id = false)
    {
        if($id === false)
        {
            $query = $this->db->get('rater');
            return $query->result_array();
        }

        $query = $this->db->get_where('rater',array('id'=>$id));
        return $query->row_array();
    }

    public function set_rater()
    {
        $data = array(
            'name' => $this->input->post('name'),
            'username' => $this->input->post('username'),
            'password' => md5($this->input->post('password').$this->config->item('encryption_key')),//加密
            'role' => $this->input->post('role'),
            'contestAuth' => $this->input->post('contestAuth'),
            'description' => $this->input->post('description'),
        );

        $id = $this->input->post('id');
        if(empty($id)) {
            return $this->db->insert($this->tableName, $data);
        } else {
            $this->db->where("id",$id);
            return $this->db->update($this->tableName,$data);
        }
    }

    public function del_rater($id) {
        $this->db->where('id',$id);
        return $this->db->delete($this->tableName);
    }

    public function signin($username,$password)
    {
        $query = $this->db->get_where($this->tableName,array('username' => $username,'password' => $password));//md5($password.$this->config->item('encryption_key'))

        return $query->row_array();
    }

} 