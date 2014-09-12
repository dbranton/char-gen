<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spells extends CI_Controller {

    /*public function __construct() {
        parent::__construct();
    }*/

    public function index() {
        $this->load->model('spell_model');
        $classId = $this->input->get('class_id', TRUE) ? $this->input->get('class_id', TRUE) : NULL;
        $data['spells'] = $this->spell_model->getSpells($classId);
        if (!is_null($classId)) {
            $className = $this->spell_model->getClassName($classId);
            $data['title'] = $className . ' Spells';
        } else {
            $data['title'] = 'Spells';
        }
        $data['main_content'] = 'pages/spells_view';
        $data['type'] = '';
        $this->load->view('template', $data);
    }
}
?>