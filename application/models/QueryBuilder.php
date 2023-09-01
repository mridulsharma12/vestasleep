<?php
defined('BASEPATH') or exit('No direct script access allowed');

class QueryBuilder extends CI_Model
{
    public function get_data($table, $data)
    {
        $this->db->or_where($data);
        $query = $this->db->get($table);

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
        // print_r($this->db->last_query());die;
    }

    public function get_state($table)
    {
        $query = $this->db->get($table);
        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
        // print_r($this->db->last_query());die;
    }
    public function check_email($table, $data)
    {
        $this->db->or_where($data);
        $query = $this->db->get($table);

        return $query->num_rows();

        // print_r($this->db->last_query());die;
    }
    public function select_data($table, $data)
    {
        $this->db->where($data);
        $query = $this->db->get($table);

        if ($query->num_rows()) {
            return $query->result_array();
        } else {
            return false;
        }
    }
    public function get_column($table, $column, $data)
    {
        $this->db->select($column);
        $this->db->where($data);
        $query = $this->db->get($table);
        // return $query->result_array();
        return $query->num_rows();
        // print_r($this->db->last_query());die;
    }

    public function delete_data($table, $data)
    {
        $this->db->where($data);
        return $this->db->delete($table);
    }

    public function insert_data($table, $data)
    {
        $query = $this->db->insert($table, $data);
        return $query;
    }

    public function update($table, $data, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update($table, $data);

        // print_r($this->db->last_query());die;
    }

    public function update_data($table, $data, $where)
    {
        $this->db->where($where);
        return $this->db->update($table, $data);

        // print_r($this->db->last_query());die;
    }



    public function delete($table, $id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($table);
    }
}
