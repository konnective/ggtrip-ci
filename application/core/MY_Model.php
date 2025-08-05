<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class MY_Model extends CI_Model
{
	public function add($post, $table)
	{
		$post = array_intersect_key($post, array_flip($this->db->list_fields($table)));

		if ($this->db->insert($table, $post)) {
			$id = $this->db->insert_id();
			return ($id) ? $id : true;
		}else
			return false;
	}

	public function get($table, $select, $where)
	{
		$result = $this->db->select($select)
						->from($table)
						->where($where)
						->get()
						->row_array();

		return ($result !== false) ? $result : false;
	}

	public function getAll($table, $select, $where, $order_by = '', $limit = '', $notIns = '')
	{
		$this->db->select($select)
					->from($table)
					->where($where);

		if(!empty($notIns)):
			foreach ($notIns as $notIn) $this->db->where_not_in($notIn['key'], $notIn['value']);
		endif;

		if ($order_by != '') 
			$this->db->order_by($order_by);
		
		if ($limit != '') 
			$this->db->limit($limit);

		return  $this->db->get()
						->result_array();
	}

	public function check($table, $where, $select)
	{
		$check = $this->db->select($select)
						->from($table)
						->where($where)
						->get()
						->row_array();
		if ($check)
			return $check[$select];
		else
			return false;
	}

	public function update($where, $post, $table)
	{
		if(empty($post['updated_at'])) {
			$post['updated_at'] = date('Y-m-d H:i:s');
		}

		$post = array_intersect_key($post, array_flip($this->db->list_fields($table)));
		$this->db->where($where)->update($table, $post);

		return $this->db->affected_rows();
	}

	public function delete($table, $where)
	{
		$this->db->delete($table, $where);
		return $this->db->affected_rows();
	}

	public function make_datatables()
	{
		$this->make_query();
		if($this->input->post("length") != -1)
		{
			$this->db->limit($this->input->post('length'), $this->input->post('start'));
		}
		$query = $this->db->get();
		return $query->result();
	}  

	public function get_filtered_data(){  
		$this->make_query();
		$query = $this->db->get();  

		return $query->num_rows();
	}

	public function datatable()
	{
        foreach ($this->search_column as $i => $item) 
        {
            if($this->input->post('search') && $this->input->post('search')['value'])
            {
                if($i === 0)
                {
                    $this->db->group_start();
                    $this->db->like($item, $this->input->post('search')['value']);
                }
                else
                {
                    $this->db->or_like($item, $this->input->post('search')['value']);
                }
 
                if(count($this->search_column) - 1 == $i) 
                    $this->db->group_end();
            }
        }

        if($this->input->post('order')) 
        {
            $this->db->order_by($this->order_column[$this->input->post('order')['0']['column']], $this->input->post('order')['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
	}

	public function count($where = [])
	{
        $this->make_query(true);

		if($where) {
			$this->db->where($where);
		}

		return $this->db->get()->num_rows();
	}

	protected function uploadImage($upload, $exts='jpg|jpeg|png', $size=[], $name=null)
    {
        $directories = explode('/', rtrim($this->path, '/'));
        $currentPath = FCPATH;

        foreach ($directories as $directory) {
            $currentPath .= $directory . '/';
            
            // Check if the directory does not exist and create it
            if (!is_dir($currentPath)) {
                mkdir($currentPath, 0777, true);
            }

            // Path to the index.html file in the current directory
            $htmlFilePath = $currentPath . 'index.html';
            
            // Content for the index.html file
            $htmlFileContent = "<!DOCTYPE html><html><head><title>404 Not found</title></head><body><p>Page Not found.</p></body></html>";
            
            // Check if index.html file does not exist and create it
            if (!file_exists($htmlFilePath)) {
                file_put_contents($htmlFilePath, $htmlFileContent);
            }
        }

        $this->load->library('upload');

        $config = [
                'upload_path'      => $this->path,
                'allowed_types'    => $exts,
                'file_name'        => $name ? $name : time(),
                'file_ext_tolower' => TRUE,
                'max_size'         => 15360,
                'overwrite'        => FALSE
            ];

        $config = array_merge($config, $size);

        $this->upload->initialize($config);

        if ($this->upload->do_upload($upload))
            return ['error' => false, 'message' => $this->upload->data("file_name")];
        else
            return ['error' => true, 'message' => $this->upload->display_errors()];
    }

    protected function uploadMultipleFiles($files, $name)
    {
        $images = [];
        $uploadError = false;

        foreach ($files[$name]['name'] as $key => $image) {
            $_FILES['image']['name']= cleanInput($files[$name]['name'][$key]);
            $_FILES['image']['type']= $files[$name]['type'][$key];
            $_FILES['image']['tmp_name']= $files[$name]['tmp_name'][$key];
            $_FILES['image']['error']= $files[$name]['error'][$key];
            $_FILES['image']['size']= $files[$name]['size'][$key];

            $filename = cleanInput($files[$name]['name'][$key]);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = url_title(rtrim($filename, ".$ext"), '-', TRUE).'-'.date('m-d-Y-H-i-s').'-'.uniqid().'.'.$ext;

            $img = $this->uploadImage('image', 'pdf|xls|xlsx|doc|docx|csv|jpeg|jpg|png|gif', [], $filename);

            if($img['error']) {
                $uploadError = $img['message'];
                break;
            }

            $images[] = ! $img['error'] ? $img['message'] : '';
        }

        return ['error' => $uploadError, 'images' => $images];
    }

	public function insert_batch($table, $data)
	{
		$this->db->trans_start();
		$this->db->insert_batch($table, $data);
		$this->db->trans_complete();

		return $this->db->trans_status();
	}
}