<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
    }

    public function save_tests()
    {
        $data = $this->input->post('response');
        $new_array = array();
        foreach ($data as $d) {
            $new_array[] = array(
                'item_id' => $d['itemId'],
                'item_name' => $d['itemName'],
                'min_price' => $d['minPrice'],
                'object_id' => $d['objectID'],
                'popular' => $d['popular']
            );
        }
        $this->db->insert_batch('tests', $new_array);
    }

    public function getItemList()
    {
        $this->db->select('*,FORMAT(min_price,0) as price');
        $query = $this->db->get('tests');
        $data = $query->result_array();
        echo json_encode($data);
    }

    public function add_user_items($id)
    {
        $request_body = file_get_contents('php://input');
        $item_ids_object = json_decode($request_body);
        $item_ids = explode(',', $item_ids_object->item_ids);
        $insert_data = array();
        $user_id = $id;
        $this->db->where('user_id', $user_id);
        $this->db->set('status', 1);
        $this->db->update('user_selected_tests');
        foreach ($item_ids as $item_id) {
            $insert_data[] = array('user_id' =>  $user_id, 'test_id' => $item_id, 'created_by' => $user_id, 'created_at' => date('Y-m-d H:i:s'));
        }
        $this->db->insert_batch('user_selected_tests', $insert_data);
        echo json_encode('Items Inserted Successfully');
    }

    public function get_user_selected_item_list($id)
    {
        $this->db->select('user_selected_tests.id as select_id,FORMAT(tests.min_price,0) as price,tests.item_name,tests.min_price');
        $this->db->where('user_id', $id);
        $this->db->where('status', 0);
        $this->db->join('tests', 'tests.id=user_selected_tests.test_id');
        $query = $this->db->get('user_selected_tests');
        $data = $query->result_array();
        echo json_encode($data);
    }

    public function get_user_purchased_item_list($id)
    {
        $this->db->select('user_selected_tests.id as select_id,FORMAT(tests.min_price,0) as price,tests.item_name,tests.min_price');
        $this->db->where('user_id', $id);
        $this->db->where('status', 2);
        $this->db->join('tests', 'tests.id=user_selected_tests.test_id');
        $query = $this->db->get('user_selected_tests');
        $data = $query->result_array();
        echo json_encode($data);
    }

    public function delete_user_selected_item_list($id)
    {
        $this->db->where('user_id', $id);
        $this->db->where('status', 0);
        $this->db->delete('user_selected_tests');
    }

    public function make_purchase($id)
    {
        $this->db->where('user_id', $id);
        $this->db->where('status', 0);
        $this->db->set('status', 2);
        $this->db->update('user_selected_tests');
    }

    public function delete_selected_item()
    {
        $request_body = file_get_contents('php://input');
        $select_id_object = json_decode($request_body);
        $this->db->where('id', $select_id_object->id);
        $this->db->delete('user_selected_tests');
        echo json_encode('Items Removed Successfully');
    }

    public function loginUser()
    {
        $request_body = file_get_contents('php://input');
        $userdata = json_decode($request_body);
        $this->db->where('email', $userdata->email);
        $this->db->where('password', $userdata->password);
        $query = $this->db->get('user');
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(false);
        }
    }
}
