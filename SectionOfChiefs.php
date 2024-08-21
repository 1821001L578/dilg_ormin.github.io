<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SectionOfChiefs extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SectionOfChiefs_model', 'soc');
    }

    public function cds()
    {
        $data['title'] = 'Section Chiefs - CDS';
        $data['section'] = 'CDS';
        
        $data['contents'] = $this->soc->all('CDS');
        
        template('section_of_chiefs/index', $data);
    }
    
    public function fas()
    {
        $data['title'] = 'Section Chiefs - FAS';
        $data['section'] = 'FAS';
        $data['contents'] = $this->soc->all('FAS');
        
        template('section_of_chiefs/index', $data);
    }
    
    public function mes()
    {
        $data['title'] = 'Section Chiefs - MES';
        $data['section'] = 'MES';
        $data['contents'] = $this->soc->all('MES');
        
        template('section_of_chiefs/index', $data);
    }
    
    public function pdmu()
    {
        $data['title'] = 'Section Chiefs - PDMU';
        $data['section'] = 'PDMU';
        $data['contents'] = $this->soc->all('PDMU');
        
        template('section_of_chiefs/index', $data);
    }
    
    public function create()
    {
        $data['title'] = 'Section Chiefs - Create';
        $data['section'] = $_SESSION['section'];
        $data['scripts'] = 'admin/partials/script_soc.php';
        
        template('section_of_chiefs/create', $data);
    }
    
    public function store()
    {
        $data = [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'content' => $_POST['content'],
            'status' => $this->input->post('status'),
            'section' =>$_SESSION['section']
        ];
        
        $config['upload_path']          = 'uploads/soc/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|pdf|xslx';
        $config['max_size']             = 10000;
        $this->load->library('My_Upload', $config);
        $result = $this->my_upload->do_multi_upload('attachment');

        if (count($result['errors']) > 1) {
            foreach ($result['errors'] as $error) {
                $this->session->set_flashdata('danger', $error);
            }
            redirect('section-chiefs/create');
        }

        if (count($result['files']) > 1) {
            $data['files'] = implode('|', $result['files']);
        }

        if (count($result['files']) == 1) {
            $data['files'] = $result['files'][0];
        }
        
        if($this->soc->store($data))
        {
            $this->session->set_flashdata('success', 'Content has been added!');
            redirect('section-chiefs/'.strtolower($_SESSION['section']));
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong!');
        }
    }
    
    public function edit($id)
    {
        $data['title'] = 'Section Chiefs - Edit';
        $data['section'] = $_SESSION['section'];
        
        $data['content'] = $this->soc->get($id);
        $data['scripts'] = 'admin/partials/script_soc.php';
        
        template('section_of_chiefs/edit', $data);
    }
    
    public function update($id)
    {
        $data = [
            'soc_id' => $id,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'content' => $_POST['content'],
            'status' => $this->input->post('status'),
            'section' =>$_SESSION['section']
        ];
        
        $old_file = explode('|', $this->soc->get($id)->files);
        
        $filesUploaded = [];
        if (!empty($_FILES['attachment'])) {
        
            $config['upload_path']          = 'uploads/soc/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|pdf|xslx';
            $config['max_size']             = 10000;
            $this->load->library('My_Upload', $config);
            $result = $this->my_upload->do_multi_upload('attachment');
    
            if (count($result['errors']) > 1) {
                foreach ($result['errors'] as $error) {
                    $this->session->set_flashdata('danger', $error);
                }
                redirect('section-chiefs/update/'.$id);
            }
    
            if (count($result['files']) > 1) {
                foreach($result['files'] as $file)
                {
                    array_push($filesUploaded, $file);
                }
            }
    
            if (count($result['files']) == 1) {
                $data['files'] = $result['files'][0];
            }
        }
        
        $data['files'] = ltrim(implode('|', array_merge($old_file, $filesUploaded)), '|');
        
        if($this->soc->update($data))
        {
            $this->session->set_flashdata('success', 'Content has been updated!');
            redirect('section-chiefs/'.strtolower($_SESSION['section']));
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong!');
        }
    }
    
    public function destroy($id)
    {
        if($this->soc->destroy($id))
        {
            $this->session->set_flashdata('success', 'Content has been deleted!');
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong!');
        }
        
        redirect('section-chiefs/'.strtolower($_SESSION['section']));
    }
}
