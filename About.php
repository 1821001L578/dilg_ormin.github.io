<?php
defined('BASEPATH') or exit('No direct script access allowed');

class About extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function plgrc()
    {
        $data['title'] = "About PLGRC";
        main_template('about/about_plgrc', $data);
    }

    public function pd_message()
    {
        $data['title'] = "Provincial Director's Message";
        main_template('about/pd_message', $data);
    }

    public function index()
    {
        $data['title'] = "About Us";
        main_template('about/index', $data);
    }

    public function organizational_structure()
    {
        $data['title'] = "ORGANIZATIONAL STRUCTURE";
        main_template('about/org_structure', $data);
    }
}
