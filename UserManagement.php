<?php

class UserManagement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserManagement_model', 'usermanagement');
    }
    
    public function index()
    {
        $data['title'] = 'User Management';
        $data['users'] = $this->usermanagement->all();
        
        template('user_management/index', $data);
    }
    
    public function create()
    {
        $data['title'] = 'User Management - Add User';
        $data['scripts'] = 'admin/partials/script_profile.php';
        
        template('user_management/create', $data);
    }
    
    public function store()
    {
        $config['upload_path'] = 'uploads/profile';
        $config['allowed_types'] = 'png|PNG|jpg|JPG|jpeg|JPEG';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $profile = 'profile.png';

        if (!empty($_FILES['profile']['name'])) {
            if (!$this->upload->do_upload('profile')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('message', $error);
            } else {
                $profile_image = $this->upload->data();
                $profile = $profile_image['file_name'];
            }
        }
        
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('position', 'Position', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');

        if ($this->form_validation->run()) {
            $data = array(
                'lastname' => $this->input->post('lastname'),
                'firstname' => $this->input->post('firstname'),
                'middlename' => $this->input->post('middlename'),
                'position' => $this->input->post('position'),
                'section' => $this->input->post('section'),
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'profile' => $profile,
                'role' => 'soc-admin'
            );
            
            if ($this->usermanagement->store($data)) {
                $this->session->set_flashdata('success', 'User has been added.');
                redirect('user-management');
            } else {
                $this->session->set_flashdata('danger', 'Something went wrong. Try again.');
                $this->create();
            }
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong. Try again.');
            redirect('user-management');
        }
    }
    
    public function edit ($user_id)
    {
        $data['title'] = 'User Management - Edit User';
        $data['user'] = $this->usermanagement->get($user_id);
        
        $data['scripts'] = 'admin/partials/script_profile.php';
        
        template('user_management/edit', $data);
    }
    
    public function update ($user_id)
    {
        $config['upload_path'] = 'uploads/profile/';
        $config['allowed_types'] = 'png|PNG|jpg|JPG|jpeg|JPEG';

        $this->load->library('Upload', $config);
        $this->upload->initialize($config);

        $old_file = $this->usermanagement->get($user_id)->profile;

        if (!empty($_FILES['profile']['name'])) {
            if (!$this->upload->do_upload('profile')) {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('message', $error);
            } else {
                $profile = $this->upload->data();

                if ($old_file != $profile['file_name'] && $old_file != "profile.png") {
                    unlink('uploads/profile/' . $old_file);
                }
                
                $old_file = $profile['file_name'];
            }
        }
        
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('position', 'Position', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');
        
        $password = ($this->input->post('password') == "") ? $this->usermanagement->get($user_id)->password : $this->input->post('password');


        if ($this->form_validation->run()) {
            $data = array(
                'admin_id' => $user_id,                                                                                        
                'lastname' => $this->input->post('lastname'),
                'firstname' => $this->input->post('firstname'),
                'middlename' => $this->input->post('middlename'),
                'position' => $this->input->post('position'),
                'section' => $this->input->post('section'),
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'password' => passwordhash($password),
                'profile' => $old_file,
                'role' => 'soc-admin',
                'is_hashed' => 1
            );
            
            if ($this->usermanagement->update($data)) {
                $this->session->set_flashdata('success', 'User details has been updated.');
                redirect('user-management');
            } else {
                $this->session->set_flashdata('danger', 'Something went wrong. Try again.');
                $this->create();
            }
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong. Try again.');
            redirect('user-management');
        }
    }
    
    public function destroy ($user_id)
    {
        if($this->usermanagement->destroy($user_id))
        {
            $this->session->set_flashdata('success', 'User has been deleted.');
        } else {
            $this->session->set_flashdata('danger', 'Something went wrong. Try again.');
        }
        
        redirect('user-management');
    }
}
