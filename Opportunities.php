<?php

    class Opportunities extends CI_Controller {
        
        public function __construct()
        {
            parent::__construct();
            $this->load->model('Opportunities_model', 'opportunities');
        }
        
        /*
        | ----------------------------------------------------------------------
        |   USER PORTAL
        | ----------------------------------------------------------------------
        */
        
        public function bids_main()
        {
            $data['title'] = 'Opportunities - Bids';
            $data['bids'] = $this->opportunities->all('bids');
            main_template('opportunities/bids', $data);
        }
        
        public function career_main()
        {
            $data['title'] = 'Opportunities - Careers';
            $data['careers'] = $this->opportunities->all('careers');
            
            main_template('opportunities/careers', $data);
        }
        
        /*
        | ----------------------------------------------------------------------
        |   ADMIN PORTAL
        | ----------------------------------------------------------------------
        */
        
        private function _category()
        {
            $category = 'bids';
            
            if($category != $this->uri->segment(2))
            {
                $category = $this->uri->segment(2);
            }
            
            return $category;
        }
        
        public function index()
        {
            $data['title'] = ucfirst($this->_category()) . " - Opportunities";
            $data['category'] = ucfirst($this->_category());
            
            $data['records'] = $this->opportunities->all($data['category']);
            
            template('opportunities/index', $data);
        }
        
        public function create()
        {
            $data['title'] = ucfirst($this->_category()) . " - Opportunities";
            $data['category'] = ucfirst($this->_category());
            
            template('opportunities/create', $data);
        }
        
        public function store()
        {
            $category = $this->_category();
            $opportunities = [];
            if($this->_category() == 'bids') {
                $opportunities = [
                    'project' => $this->input->post('project'),
                    'abc' => $this->input->post('abc'),
                    'type' => 'bids',
                ];
            } else {
                $opportunities = [
                    'position_title' => $this->input->post('position'),
                    'type' => 'careers',
                ];
            }
            $opportunities['posting_date'] = $this->input->post('posting-date');
            $opportunities['closing_date'] = $this->input->post('closing-date');
            
            $filesUploaded = [];
            if (!empty($_FILES['attachment'])) {
    
                $config['upload_path']          = 'uploads/opportunities/';
                $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|doc|xslx|gdoc|gsheet|pdf';
                $config['max_size']             = 1024;
                $this->load->library('My_Upload', $config);
                $result = $this->my_upload->do_multi_upload('attachment');
    
                if (count($result['errors']) > 1) {
                    foreach ($result['errors'] as $error) {
                        $this->session->set_flashdata('danger', $error);
                    }
                    redirect('opportunities/'.$category.'/create');
                }
    
                if (count($result['files']) > 1) {
                    foreach($result['files'] as $file)
                    {
                        array_push($filesUploaded, $file);
                    }
                }
    
                if (count($result['files']) == 1) {
                    $filesUploaded = [$result['files'][0]];
                }
            }
        
            $opportunities['files'] = ltrim(implode('|', $filesUploaded), '|');
            $opportunities['status'] = $this->input->post('status');
            
            if($this->opportunities->store($opportunities))
            {
                $this->session->set_flashdata('success', ucfirst($category) . ' has been added.');
                redirect('opportunities/'.$category);
            } else {
                $this->session->set_flashdata('danger', ucfirst($category) . ' was not added.');
                redirect('opportunities/'.$category.'/create');
            }
        }
        
        public function edit($id)
        {
            $data['title'] = ucfirst($this->_category()) . " - Opportunities";
            $data['category'] = ucfirst($this->_category());
            $_SESSION['opp_category'] = $this->_category();
            
            $data['record'] = $this->opportunities->get($id);
            $data['scripts'] = ['admin/partials/script_delete_attachment.php'];
            
            template('opportunities/edit', $data);
        }
        
        public function update($id)
        {
            $category = $this->_category();
            $opportunities = [];
            if($this->_category() == 'bids') {
                $opportunities = [
                    'project' => $this->input->post('project'),
                    'abc' => $this->input->post('abc'),
                    'type' => 'bids',
                ];
            } else {
                $opportunities = [
                    'position_title' => $this->input->post('position'),
                    'type' => 'careers',
                ];
            }
            $opportunities['posting_date'] = $this->input->post('posting-date');
            $opportunities['closing_date'] = $this->input->post('closing-date');
            $current_attachments = $this->opportunities->get($id)->files;
            $current_attachments = explode('|', $current_attachments);
            
            $filesUploaded = [];
            if (!empty($_FILES['attachment'])) {
    
                $config['upload_path']          = 'uploads/opportunities/';
                $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|doc|xslx|gdoc|gsheet|pdf';
                $config['max_size']             = 1024;
                $this->load->library('My_Upload', $config);
                $result = $this->my_upload->do_multi_upload('attachment');
    
                if (count($result['errors']) > 1) {
                    foreach ($result['errors'] as $error) {
                        $this->session->set_flashdata('danger', $error);
                    }
                    redirect('opportunities/'.$category.'/update'.$id);
                }
    
                if (count($result['files']) > 1) {
                    foreach($result['files'] as $file)
                    {
                        array_push($filesUploaded, $file);
                    }
                }
    
                if (count($result['files']) == 1) {
                    $filesUploaded = [$result['files'][0]];
                }
            }
        
            $opportunities['files'] = ltrim(implode('|', array_merge($current_attachments, $filesUploaded)), '|');
            $opportunities['opportunities_id'] = $id;
            $opportunities['status'] = $this->input->post('status');
            
            if($this->opportunities->update($opportunities))
            {
                $this->session->set_flashdata('success', ucfirst($category) . ' has been updated.');
                redirect('opportunities/'.$category);
            } else {
                $this->session->set_flashdata('danger', ucfirst($category) . ' was not updated.');
                redirect('opportunities/'.$category.'/update/'.$id);
            }
        }
        
        public function destroy($id)
        {
            $category = $this->_category();
            if($this->opportunities->delete($id))
            {
                $this->session->set_flashdata('success', ucfirst($category) . ' has been deleted.');
            } else {
                $this->session->set_flashdata('success', ucfirst($category) . ' was not deleted.');   
            }
            
            redirect('opportunities/'.$category);
        }
        
        public function delete_attachment($id, $attachment)
        {
            $record = $this->opportunities->get($id);
            $files = explode('|', $record->files);
        
            $filename = urldecode($attachment);
    
            $files = array_filter($files, function($file) use ($filename) {
                return trim($file) !== trim($filename);
            });
    
            $newFilesString = implode('|', $files);
            $this->opportunities->update(['opportunities_id' => $id, 'files' => $newFilesString]);
            
            unlink(FCPATH .'/uploads/opportunities/' . $filename);
            
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'records' => $this->opportunities->get($id)]));
        }
        
        public function attachment_section($id)
        {
            $category = $_SESSION['opp_category'];
            // Get the record
            $record = $this->opportunities->get($id);
        
            $attachments = "";
    
            if (!empty($record->files)) {
                $files = explode('|', $record->files);
                foreach ($files as $file) {
                    $attachments .= '<div class="d-flex align-items-center justify-content-between border border-secondary rounded p-2 mb-2">
                        <span class="me-2">' . $file . '</span>
                            <button type="button" data-id="' . $record->opportunities_id. '" data-category="'. $category . '" data-link="'. base_url($category.'/delete-attachment/' . $record->opportunities_id . '/' . urlencode($file)) . '" class="btn btn-danger btn-sm delete-attachment"><i class="bi bi-trash"></i> Delete</button>
                        </div>';
                }
            } else {
                $attachments .= '<p class="text-muted">No attachments found.</p>';
            }
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'records' => $attachments]));
            
        }
    }
