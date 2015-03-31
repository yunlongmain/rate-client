<?php
/**
 * Created by PhpStorm.
 * User: yunlong
 * Date: 14-4-8
 * Time: 下午8:09
 */


class Contest_model extends CI_Model{
    protected $tableName;

    public function __construct()
    {
        $this->load->database();
        $this->tableName = "contest";

    }

    public function get_contest($id = false)
    {
        if($id === false)
        {
            $query = $this->db->get('contest');
            return $query->result_array();
        }

        $query = $this->db->get_where('contest',array('id'=>$id));
        return $query->row_array();
    }

} 