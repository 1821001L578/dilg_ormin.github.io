<?php

class QRCode extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('QRCode_model', 'qrcode_model');
    }
    
    public function scan_qr($qr_code)
    {
        $qr_code = str_replace('%20', ' ', $qr_code);
        $data = $this->qrcode_model->getData($qr_code);
        
        if ($data) {
            $response = [
                'success' => true,
                'data' => [
                    'book_cover' => base_url('uploads/books/').$data->file,
                    'book_id' => $data->book_id,
                    'book_title'=> $data->title,
                    'available_copies' => $data->available_copies
                    ]
                ];
        } else {
            $response = array('success' => false, 'message' => 'Data not found');
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    public function weather(){
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
        

        dd($api_result);

	}
}
