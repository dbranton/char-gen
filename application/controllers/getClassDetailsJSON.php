<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



//require_once("JSON.php");
//$json = new Services_JSON();

//$jsonFile = file_get_contents("classJSON");
//$json = json_decode($jsonFile);

$character = array();


class GetClassDetailsJSON extends CI_Controller {
    public function index() {
        $this->load->model('class_model');
        $data['classes'] = $this->class_model->getClasses();
        //var_dump($data['classes']);
        if(!isset($required)) {
            echo json_encode($data);
        }

    }
}

?>