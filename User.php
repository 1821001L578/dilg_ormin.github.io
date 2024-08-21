<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Books_model', 'books_model');
        $this->load->model('Category_model', 'category_model');
        $this->load->model('Publisher_model', 'publisher_model');
        $this->load->model('Visit_model', 'visit_model');
        $this->load->model('NewsAnnouncements_model', 'news_model');
    }

    public function index()
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['title'] = 'Home';

        $data['news'] = $this->news_model->findAll('news');
        $this->visit_model->insert_visit();
        
        $location = 'Oriental Mindoro';

        $queryString = http_build_query([
          'access_key' => '386f932b0cb9172942eefc1c1a625f2d',
          'query' => $location,
        ]);

        $ch = curl_init(sprintf('%s?%s', 'http://api.weatherstack.com/current', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        $api_result = json_decode($json, true);
        
        $data['weather'] = [
            'weather_icon' => $api_result['current']['weather_icons'][0],
            'temperature' => $api_result['current']['temperature'],
            'description' => $api_result['current']['weather_descriptions'][0],
            'wind_speed' => $api_result['current']['wind_speed'],
            'wind_degree' => $api_result['current']['wind_degree'],
            'wind_dir' => $api_result['current']['wind_dir'],
        ];

        main_template('main', $data);
    }

    public function about_us()
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['title'] = 'About Us';
        main_template('about', $data);
    }

    public function contact_us()
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['title'] = 'Contact Us';
        main_template('contact', $data);
    }

    public function library($page = 1)
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['title'] = 'Library';

        $this->load->library('paginator');
        $optional = array(
            'per_page' => 10,
            'query_string' => true,
            'base_url' => 'user/library',
        );

        $data['books'] = $this->paginator->paginate('books', $optional);
        $pagination_links = $this->paginator->get_links('books', 'bootstrap5');
        $data['pagination'] = $pagination_links;

        $data['scripts'] = 'main/partials/filter.php';

        main_template('books', $data);
    }

    public function book($id)
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['book'] = $this->books_model->get($id);
        $data['title'] = $data['book']->title;
        $data['related_books'] = $this->books_model->related($id, $data['book']->main_category, $data['book']->sub_category);

        main_template('book', $data);
    }

    public function search()
    {
        if(is_logged_in())
        {
            redirect('dashboard');
        }
        
        $data['title'] = 'Search Results';
        $this->load->library('paginator');

        // Get the search query
        $query = $this->input->get('q');

        $main_category = '';
        $sub_category = '';
        $publisher = '';

        if (isset($_GET['main-category'])) {
            $main_category = $_GET['main-category'];
        }

        if (isset($_GET['sub-category'])) {
            $sub_category = $_GET['sub-category'];
        }

        if (isset($_GET['publisher'])) {
            $publisher = $_GET['publisher'];
        }

        $data['page'] = $this->input->get('page');
        $data['search'] = $query;

        // Paginate the search results

        $custom_object = null;
        if ($main_category != '' || $sub_category != '' || $publisher != '') {
            $conditions = [
                'q' => $query,
                'main_category' => $main_category,
                'sub_category' => $sub_category,
                'publisher' => $publisher
            ];

            $custom_object = $this->books_model->get_custom_db_obj($conditions);

            $optional = array(
                'per_page' => 10,
                'query_string' => true,
                'base_url' => 'library/search?q=' . $query . 'main-category=' . $main_category . 'sub-category=' . $sub_category . 'publisher=' . $publisher,
            );
        } else {
            $custom_object = $this->books_model->get_custom_db_obj($query);
            $optional = array(
                'per_page' => 10,
                'query_string' => true,
                'base_url' => 'library/search?q=' . $query,
            );
        }

        $clone = clone $custom_object;
        $data['count'] = $clone->count_all_results();
        $data['results'] = $this->paginator->paginate($custom_object, $optional);

        $data['publishers'] = $this->publisher_model->all();
        $data['main_categories'] = $this->category_model->main_all();
        $data['sub_categories'] = $this->category_model->sub_all();

        // Get pagination links
        $pagination_links = $this->paginator->get_links('books', 'bootstrap5');
        $data['pagination'] = $pagination_links;
        $data['scripts'] = 'main/partials/filter.php';

        main_template('search_results', $data);
    }
}
