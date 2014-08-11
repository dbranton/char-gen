<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spells extends CI_Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function index() {
        $this->load->model('spell_model');
        $data['spells'] = $this->spell_model->getSpells();
        $data['title'] = 'Spells';
        $data['main_content'] = 'pages/spells_view';
        $this->load->view('template', $data);
    }

}
?>