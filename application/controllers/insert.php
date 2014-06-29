<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class Insert extends CI_Controller {

        public function index() {
            $data['level'] = '5';
            $data['race'] = 'Dwarf';
            $data['class'] = 'Fighter';


            $this->load->model('site_model');
            $data['records'] = $this->site_model->getAll();
            $this->load->view('display', $data); // has to come last
        }

    }
?>