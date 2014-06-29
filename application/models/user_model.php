<?php

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function login($username, $password) {
        $this->db->select('id, username, password');
        $this->db->from('user_table');
        $this->db->where('username', $username);
        $this->db->where('password', MD5($password));
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_user() {
        $data = array(
            'username' => $this->input->post('username'),
            'email' => $this->input->post('email'),
            'password' => md5($this->input->post('password')),
            'join_date' => standard_date('DATE_RFC822', time())
        );
        $this->db->insert('user_table', $data);
    }

    private function _calculateModifiers($value) {
        return floor(($value-10)/2);
    }

    /*
     * SELECT character_table.*, features_table.name FROM character_table
        INNER JOIN character_features
        ON character_features.character_id = character_table.id
        INNER JOIN features_table
        ON features_table.id = character_features.feature_id;
     */
    public function add_character() {
        $user_id = $this->session->userdata['logged_in']['id'];
        $this->load->helper('string');
        $generatedId =  $user_id . '_' . random_string('numeric',5);    // ex: 9_54824
        $str = intval($this->input->post('strScoreName'));
        $dex = intval($this->input->post('dexScoreName'));
        $con = intval($this->input->post('conScoreName'));
        $int = intval($this->input->post('intScoreName'));
        $wis = intval($this->input->post('wisScoreName'));
        $cha = intval($this->input->post('chaScoreName'));
        $strMod = $this->_calculateModifiers($str);
        $dexMod = $this->_calculateModifiers($dex);
        $conMod = $this->_calculateModifiers($con);
        $intMod = $this->_calculateModifiers($int);
        $wisMod = $this->_calculateModifiers($wis);
        $chaMod = $this->_calculateModifiers($cha);
        /*$racialTraitIds = explode(',', $this->input->post('racialTraitsName'));   // ex: ['2','5','16']
        $racialTraits = array();
        for($i=0, $size=count($racialTraitIds); $i<$size; $i++) {
            array_push($racialTraits, array(
                'character_id' => $generatedId,
                'feature_id' => $racialTraitIds[$i]
            ));
        }
        $classFeatureIds = explode(',', $this->input->post('classFeaturesName'));
        $classFeatures = array();
        for($j=0, $jsize=count($classFeatureIds); $j<$jsize; $j++) {
            array_push($racialTraits, array(
                'character_id' => $generatedId,
                'feature_id' => $classFeatureIds[$j]
            ));
        }*/
        $data = array(
            'key' => $generatedId,
            'name' => $this->input->post('charName'),
            'race' => $this->input->post('raceName'),
            'background' => $this->input->post('backgroundName'),
            'languages' => $this->input->post('languageName'),
            'size' => $this->input->post('sizeName'),
            'class' => $this->input->post('className'),
            'pseudo_class' => $this->input->post('subclassName'),
            'level' => 1,
            'skills' => $this->input->post('skillsName'),
            'hit_points' => $this->input->post('hitPointsName'),
            'hit_dice' => $this->input->post('hitDiceName'),
            'initiative' => $this->input->post('initiativeName'),
            'speed' => $this->input->post('speedName'),
            'proficiency_bonus' => 1,
            'armor_class' => $this->input->post('armorName'),
            //'base_attk_bonus' => ???,
            'tools' => $this->input->post('toolsName'),
            'saving_throws' => $this->input->post('savingThrowsName'),
            'strength' => $str,
            'dexterity' => $dex,
            'constitution' => $con,
            'intelligence' => $int,
            'wisdom' => $wis,
            'charisma' => $cha,
            'str_mod' => $strMod,
            'dex_mod' => $dexMod,
            'con_mod' => $conMod,
            'int_mod' => $intMod,
            'wis_mod' => $wisMod,
            'cha_mod' => $chaMod,
            'user_id' => $user_id,
            'date_added' => date("m/d/Y")
        );
        //$this->db->insert('character_table', $data);
        //$this->db->insert_batch('character_features', $racialTraits);
    }

    public function get_characters() {
        //$this->db->get('character_table', $data);
        $userId = $this->session->userdata['logged_in']['id'];
        $sql = "SELECT character_table.*" .
            " FROM character_table" .
            " WHERE character_table.user_id = '" . $userId . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $characters = array();
            foreach ($query->result() as $row) {
                $character['id'] = $row->id;
                $character['name'] = $row->name;
                $character['level'] = $row->level;
                $character['race'] = $row->race;
                $character['class'] = $row->class;
                array_push($characters, $character);
            }
            return $characters;
        } else {
            echo 'No Characters';
        }
    }

    public function get_character($characterId) {
        $userId = $this->session->userdata['logged_in']['id'];
        $sql = "SELECT character_table.*" .
            " FROM character_table" .
            " WHERE character_table.user_id = '" . $userId . "' AND character_table.id = '" . $characterId . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $character['name'] = $row->name;
            $character['level'] = $row->level;
            $character['race'] = $row->race;
            $character['class'] = $row->class;
            $character['armor_class'] = $row->armor_class;
            $character['hit_points'] = $row->hit_points;
            $character['hit_dice'] = $row->hit_dice;
            $character['proficiency_bonus'] = $row->proficiency_bonus;
            $character['speed'] = $row->speed;
            $character['languages'] = $row->languages;
            $character['strength'] = $row->strength;
            $character['dexterity'] = $row->dexterity;
            $character['constitution'] = $row->constitution;
            $character['intelligence'] = $row->intelligence;
            $character['wisdom'] = $row->wisdom;
            $character['charisma'] = $row->charisma;
            $character['skills'] = $row->skills;
            $character['traits'] = $this->_getRacialTraits($characterId);
            $character['features'] = $this->_getClassFeatures($characterId, $row->level);
            $character['background'] = $this->_getBackground($row->background);
            return $character;
        } else {
            // TODO: handle error
        }
    }

    private function _getRacialTraits($characterId) {
        $sql = "SELECT features_table.*" .
            " FROM features_table" .
            " JOIN race_features" .
            " ON race_features.feature_id = features_table.id" .
            " JOIN race_table" .
            " ON race_table.readable_id = race_features.race_id" .
            " JOIN character_table" .
            " ON character_table.race = race_table.subrace" .
            " WHERE character_table.id = '" . $characterId . "' AND (race_features.subrace_id = '' OR race_features.subrace_id = race_table.id)" .
            " ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $racialTraits = array();
            foreach ($query->result() as $row) {
                $racialTrait['name'] = $row->name;
                $racialTrait['description'] = $row->description;
                array_push($racialTraits, $racialTrait);
            }
            return $racialTraits;
        }
    }

    private function _getClassFeatures($characterId, $level) {
        $sql = "SELECT features_table.*" .
            " FROM features_table" .
            " JOIN class_features" .
            " ON class_features.feature_id = features_table.id" .
            " JOIN class_table" .
            " ON class_table.id = class_features.class_id" .
            " JOIN character_table" .
            " ON character_table.class = class_table.name" .
            " WHERE character_table.id = '" . $characterId . "' AND class_features.level <= '" . $level . "'" .
            " ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $classFeatures = array();
            foreach ($query->result() as $row) {
                $classFeature['name'] = $row->name;
                $classFeature['benefit'] = $row->benefit;
                array_push($classFeatures, $classFeature);
            }
            return $classFeatures;
        }
    }

    private function _getBackground($backgroundName) {
        $sql = "SELECT background_table.*" .
            " FROM background_table" .
            " JOIN character_table" .
            " ON character_table.background = background_table.name" .
            " WHERE background_table.name = '" . $backgroundName . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $background['name'] = $row->name;
            $background['trait_name'] = $row->trait_name;
            $background['trait_desc'] = $row->trait_desc;
            $background['tools'] = $row->tools;
            return $background;
        }
    }
}
?>
