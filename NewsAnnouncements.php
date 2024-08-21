<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NewsAnnouncements extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('NewsAnnouncements_model', 'news_model');
    }
    
    private function _getCategory()
    {
        return $this->uri->segment(1);
    }
    
    /*
    | -------------------------------------------------------------------------
    |   USER PORTAL
    | -------------------------------------------------------------------------
    */
    
    public function news_all()
    {
        $data['title'] = 'News';
        $data['news'] = $this->news_model->findAll('news');

        main_template('news_ann/news_all', $data);
    }

    public function announcements_all()
    {
        $data['title'] = 'Announcements';
        $data['announcements'] = $this->news_model->findAll('announcement');

        main_template('news_ann/announcement_all', $data);
    }

    public function read_more($id)
    {
        $result = $this->news_model->get($id);

        $data['title'] = $result->title;
        if ($result->type == 'news') {
            $data['news'] = $result;
            main_template('news_ann/news', $data);
        } else {
            $data['announcement'] = $result;
            main_template('news_ann/announcement', $data);
        }
    }
    
    /*
    | -------------------------------------------------------------------------
    |   NEWS & ANNOUNCEMENTS SECTION - ADMIN PORTAL
    | -------------------------------------------------------------------------
    */
    
    public function index($category = 'news')
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
        }
        $data['title'] = ucfirst($category);
        $data['category'] = ucfirst($category);
        $data['records'] = $this->news_model->all($category);
        $data['scripts'] = 'admin/partials/script_news.php';
        template('news_ann/index', $data);
    }

    public function create($category = 'news')
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
        }
        $data['title'] = ucfirst($category) . ' - Create';
        $data['category'] = ucfirst($category);
        $data['scripts'] = 'admin/partials/script_summernote.php';
        template('news_ann/create', $data);
    }

    public function store($category = 'news')
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
        }
        $data = [
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'content' => $this->input->post('content'),
            'status' => $this->input->post('status'),
            'type' => $category
        ];
        
        $thumbnail = "";
        if (!empty($_FILES['thumbnail']['name'])) {
            $config['upload_path']   =  FCPATH . '/uploads/news/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 4096;
            $config['max_width']     = 2000;
            $config['max_height']    = 2000;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('thumbnail')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('message', $error);
                 redirect('$category/create');
            } else {
                $post_image            = $this->upload->data();
                $thumbnail = $post_image['file_name'];
            }
        }

        $filesUploaded = [];
        if (!empty($_FILES['attachment'])) {

            $config['upload_path']          = 'uploads/news/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|doc|xslx|gdoc|gsheet|pdf';
            $config['max_size']             = 1024;
            $this->load->library('My_Upload', $config);
            $result = $this->my_upload->do_multi_upload('attachment');

            if (count($result['errors']) > 1) {
                foreach ($result['errors'] as $error) {
                    $this->session->set_flashdata('danger', $error);
                }
                redirect('$category/create');
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
        
        $data['files'] = ltrim(implode('|', $filesUploaded), '|');
        $data['image_content'] = $thumbnail;

        $this->load->database();
        if ($this->db->insert('news', $data)) {
            $this->session->set_flashdata('success', ucfirst($category) . ' has been created');
            redirect($category);
        } else {
            $this->session->set_flashdata('danger', ucfirst($category) . ' could not be created');
            redirect('$category/create');
        }
    }

    public function edit($category = 'news', $id)
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
            $_SESSION['newsann_category'] = $category;
        }
        
        $data['title'] = ucfirst($category) . ' - Edit';
        $data['category'] = ucfirst($category);
        $data['record'] = $this->news_model->get($id);
        $data['scripts'] = ['admin/partials/script_delete_attachment.php',
        'admin/partials/script_summernote.php'];
        template('news_ann/edit', $data);
    }
    
    public function update($category = 'news', $id)
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
            $_SESSION['newsann_category'] = $category;
        }
        $current_attachments = $this->news_model->get($id)->files;
        $current_attachments = explode('|', $current_attachments);
        
        $data = [
            'news_id' => $id,
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'content' => $this->input->post('content'),
            'status' => $this->input->post('status'),
            'type' => $category
        ];
        
        $thumbnail = "";
        if (!empty($_FILES['thumbnail']['name'])) {
            $config['upload_path']   =  FCPATH . '/uploads/news/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']      = 4096;
            $config['max_width']     = 2000;
            $config['max_height']    = 2000;

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('thumbnail')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('message', $error);
                 redirect('$category/create');
            } else {
                $post_image            = $this->upload->data();
                $thumbnail = $post_image['file_name'];
            }
        } else {
            $thumbnail = $this->news_model->get($id)->image_content;
        }

        $filesUploaded = [];
        if (!empty($_FILES['attachment'])) {

            $config['upload_path']          = 'uploads/news/';
            $config['allowed_types']        = 'gif|jpg|png|jpeg|docx|docs|doc|xslx|gdoc|gsheet|pdf';
            $config['max_size']             = 1024;
            $this->load->library('My_Upload', $config);
            $result = $this->my_upload->do_multi_upload('attachment');

            if (count($result['errors']) > 1) {
                foreach ($result['errors'] as $error) {
                    $this->session->set_flashdata('danger', $error);
                }
                redirect('$category/create');
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
        
        $data['files'] = ltrim(implode('|', array_merge($current_attachments, $filesUploaded)), '|');
        $data['image_content'] = $thumbnail; 
        
        if ($this->news_model->update($data)) {
            $this->session->set_flashdata('success', ucfirst($category) . ' has been updated');
            redirect($category);
        } else {
            $this->session->set_flashdata('danger', ucfirst($category) . ' could not be updated');
            redirect('$category/update/' . $id);
        }
    }

    public function destroy($category = 'news', $id)
    {
        if(!$category == $this->_getCategory())
        {
            $category = $this->_getCategory();
        }
        if ($this->news_model->delete($id)) {
            $this->session->set_flashdata('success', ucfirst($category) . ' has been deleted');
        } else {
            $this->session->set_flashdata('danger', ucfirst($category) . ' could not be deleted');
        }

        redirect($category);
    }

    public function upload_image()
    {
        $config['upload_path'] = FCPATH . 'uploads/news/';
        $config['allowed_types'] = 'png|PNG|jpg|JPG|jpeg|JPEG';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $file = '';
        if (!empty($_FILES['file']['name'])) {
            if (!$this->upload->do_upload('file')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('message', $error);
            } else {
                $newsImage = $this->upload->data();
                $file = $newsImage['file_name'];
            }

            echo json_encode(['url' => base_url('uploads/news/' . $file)]);
        }
    }

    public function delete_image()
    {
        $file = basename($this->input->post('src'));
        unlink('uploads/news/' . $file);
    }
    
    public function delete_attachment($id, $is_thumbnail = 'attachment', $attachment)
    {
        $record = $this->news_model->get($id);
        $files = explode('|', $record->files);
    
        $filename = urldecode($attachment);
        
        if($is_thumbnail == 'thumbnail') {
            $this->news_model->update(['news_id' => $id, 'image_content' => '']);
        } else {
            $files = array_filter($files, function($file) use ($filename) {
                return trim($file) !== trim($filename);
            });
    
            $newFilesString = implode('|', $files);
            $this->news_model->update(['news_id' => $id, 'files' => $newFilesString]);
        }
        
        unlink(FCPATH .'/uploads/news/' . $filename);
        
        $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'records' => $this->news_model->get($id)]));
    }
    
    public function attachment_section($id, $category, $file_type)
    {
        // Get the record
        $record = $this->news_model->get($id);
    
        $attachments = "";

        if($file_type == 'thumbnail') {
            $attachments .= '<div class="d-flex align-items-center justify-content-between border border-secondary rounded p-2 mb-2">
                            <span class="me-2">' . $record->image_content . '</span>
                                <button type="button" data-id="' . $record->news_id. '" data-category="'. $category . '" data-link="'. base_url($category.'/delete-attachment/' . $record->news_id . '/'. $file_type . '/' . urlencode($record->image_content)) . '" class="btn btn-danger btn-sm delete-attachment"><i class="bi bi-trash"></i> Delete</button>
                            </div>';
        } else {
            if (!empty($record->files)) {
                $files = explode('|', $record->files);
                foreach ($files as $file) {
                    $attachments .= '<div class="d-flex align-items-center justify-content-between border border-secondary rounded p-2 mb-2">
                        <span class="me-2">' . $file . '</span>
                            <button type="button" data-id="' . $record->news_id. '" data-category="'. $category . '" data-link="'. base_url($category.'/delete-attachment/' . $record->news_id . '/'. $file_type . '/' . urlencode($file)) . '" class="btn btn-danger btn-sm delete-attachment"><i class="bi bi-trash"></i> Delete</button>
                        </div>';
                }
            } else {
                $attachments .= '<p class="text-muted">No attachments found.</p>';
            }
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'records' => $attachments]));
        
    }
}
