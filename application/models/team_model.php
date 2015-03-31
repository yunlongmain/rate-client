<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:09
 */


class Team_model extends CI_Model{
    protected $tableName;

    public function __construct()
    {
        $this->load->database();
        $this->tableName = "team";

    }

    public function get_team($id = false)
    {
        if($id === false)
        {
            $query = $this->db->get($this->tableName);
            return $query->result_array();
        }

        $query = $this->db->get_where($this->tableName,array('id'=>$id));
        return $query->row_array();
    }

    public function get_team_by_contest($id = false)
    {
        $query = $this->db->get_where($this->tableName,array('contestId'=>$id));
        return $query->result_array();
    }

} 