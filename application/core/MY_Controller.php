<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class MY_Controller extends CI_Controller
{
    public $path = 'uploads/';

	protected function uploadImage($upload, $exts='jpg|jpeg|png', $size=[], $name=null)
    {
        create_directories($this->path);

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
            return ['error' => true, 'message' => strip_tags($this->upload->display_errors())];
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
}