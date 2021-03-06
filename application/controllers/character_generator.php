<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Character_generator extends CI_Controller {
    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->library('user_agent');
        $data['mobile'] = $this->agent->is_mobile();
        $data['logged_in'] = isset($this->session->userdata['logged_in']);
        $data['title'] = 'Character Generator';
        $data['type'] = 'character_generator';
        $data['main_content'] = 'pages/character_generator_view';  // races view works the same for classes
        $this->load->view('template', $data);
    }

    public function json_get_races() {
        $this->load->model('race_model');
        $data = $this->race_model->getRaces();
        print json_encode($data);
    }

    public function json_get_classes($selClass = null) {
        $this->load->model('class_model');
        $data = $this->class_model->getClasses($selClass);
        print json_encode($data);
    }

    public function json_get_backgrounds() {
        $this->load->model('background_model');
        $data = $this->background_model->getBackgrounds();
        print json_encode($data);
    }

    public function json_get_alignments() {
        $this->load->model('alignment_model');
        $data = $this->alignment_model->getAlignments();
        print json_encode($data);
    }

    public function json_get_languages() {
        $this->load->model('language_model');
        $data = $this->language_model->getLanguages();
        print json_encode($data);
    }

    public function json_get_skills() {
        $this->load->model('skill_model');
        $data = $this->skill_model->getSkills();
        print json_encode($data);
    }

    public function json_get_spells() {
        $this->load->model('spell_model');
        $classId = $this->input->get('class_id', TRUE);
        $maxSpellLevel = $this->input->get('max_spell_level', TRUE);
        $restrictedSchool1 = $this->input->get('restricted_school_1', TRUE);
        $restrictedSchool2 = $this->input->get('restricted_school_2', TRUE);
        $term = $this->input->get('term', TRUE);
        $data = $this->spell_model->getSpells($classId, $maxSpellLevel, $term, $restrictedSchool1, $restrictedSchool2);
        print json_encode($data);
    }

}


?>
