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
    public function add_character($user_id, $character) {
        $this->load->helper('string');
        $generatedId =  $user_id . '_' . random_string('numeric',5);    // ex: 9_54824
        $name = $character->name;
        $level = $character->level;
        $raceName = $character->raceObj->subrace->name; // ex: Mountain Dwarf
        $racialTraitIds = '';
        foreach ($character->raceObj->racialTraits as $racialTrait) {
            if ($racialTraitIds != '') {
                $racialTraitIds .= ', ';
            }
            $racialTraitIds .= $racialTrait->id;
        }
        $classFeatureIds = '';
        foreach ($character->classObj->classFeatures as $classFeature) {
            if ($classFeatureIds != '') {
                $classFeatureIds .= ', ';
            }
            $classFeatureIds .= $classFeature->id;
        }
        $size = $character->size;
        $speed = $character->speed;
        $backgroundName = $character->background->name;
        $languages = $character->languages;
        $className = $character->classObj->name;
        $subclassName = isset($character->classObj->subclassObj) ? $character->classObj->subclassObj->name : '';
        $spellAbility = isset($character->classObj->spellcasting) ? $character->classObj->spellcasting->spellAbility : '';
        $spellSaveDC = isset($character->classObj->spellcasting) ? $character->classObj->spellcasting->spellSaveDC : 0;
        $spellAttkBonus = isset($character->classObj->spellcasting) ? $character->classObj->spellcasting->spellAttkBonus : 0;
        $bonusSpellAbility = isset($character->raceObj->spellcasting) ? $character->raceObj->spellcasting->spellAbility : '';
        $bonusSpellSaveDC = isset($character->raceObj->spellcasting) ? $character->raceObj->spellcasting->spellSaveDC : 0;
        $bonusSpellAttkBonus = isset($character->raceObj->spellcasting) ? $character->raceObj->spellcasting->spellAttkBonus : 0;
        $bonusCantrip = isset($character->raceObj->cantrip) ? $character->raceObj->cantrip : '';
        $hitDice = intval($character->classObj->hit_dice);
        $hitPoints = $character->hitPoints;
        $profBonus = $character->profBonus;
        $skillProf = $character->proficientSkills;
        $armorProf = $character->armor;
        $weaponProf = $character->weapons;
        $toolProf = $character->tools;
        foreach ($character->skills as $skill) {
            switch ($skill->name) {
                case 'Acrobatics':
                    $acrobatics = $skill->val;
                    break;
                case 'Animal Handling':
                    $animalHandling = $skill->val;
                    break;
                case 'Arcana':
                    $arcana = $skill->val;
                    break;
                case 'Athletics':
                    $athletics = $skill->val;
                    break;
                case 'Deception':
                    $deception = $skill->val;
                    break;
                case 'History':
                    $history = $skill->val;
                    break;
                case 'Insight':
                    $insight = $skill->val;
                    break;
                case 'Intimidation':
                    $intimidation = $skill->val;
                    break;
                case 'Investigation':
                    $investigation = $skill->val;
                    break;
                case 'Medicine':
                    $medicine = $skill->val;
                    break;
                case 'Nature':
                    $nature = $skill->val;
                    break;
                case 'Perception':
                    $perception = $skill->val;
                    break;
                case 'Performance':
                    $performance = $skill->val;
                    break;
                case 'Persuasion':
                    $persuasion = $skill->val;
                    break;
                case 'Religion':
                    $religion = $skill->val;
                    break;
                case 'Sleight of Hand':
                    $sleightOfHand = $skill->val;
                    break;
                case 'Stealth':
                    $stealth = $skill->val;
                    break;
                case 'Survival':
                    $survival = $skill->val;
                    break;
            }
        }
        $str = $character->ability->str->score;
        $dex = $character->ability->dex->score;
        $con = $character->ability->con->score;
        $int = $character->ability->int->score;
        $wis = $character->ability->wis->score;
        $cha = $character->ability->cha->score;
        $strMod = $character->ability->str->mod;
        $dexMod = $character->ability->dex->mod;
        $conMod = $character->ability->con->mod;
        $intMod = $character->ability->int->mod;
        $wisMod = $character->ability->wis->mod;
        $chaMod = $character->ability->cha->mod;
        $savingThrows = $character->savingThrows;
        $strSave = $character->ability->str->savingThrow;
        $dexSave = $character->ability->dex->savingThrow;
        $conSave = $character->ability->con->savingThrow;
        $intSave = $character->ability->int->savingThrow;
        $wisSave = $character->ability->wis->savingThrow;
        $chaSave = $character->ability->cha->savingThrow;
        $initiative = $character->initiative;
        $armorClass = $character->armorClass;
        $passivePerception = $character->passivePerception;
        $expertise = isset($character->classObj->selectedExpertise) ? implode(', ', $character->classObj->selectedExpertise) : '';
        $cantrips = isset($character->classObj->selectedCantrips) ? implode(', ', $character->classObj->selectedCantrips) : '';
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
            'name' => $name,
            'race' => $raceName,
            'racial_trait_ids' => $racialTraitIds,
            'background' => $backgroundName,
            'languages' => $languages,
            'size' => $size,
            'class' => $className,
            'pseudo_class' => $subclassName,
            'class_feature_ids' => $classFeatureIds,
            'cantrips' => $cantrips,
            'bonus_cantrip' => $bonusCantrip,
            'level' => $level,
            'skills' => $skillProf,
            'hit_points' => $hitPoints,
            'hit_dice' => $hitDice,
            'initiative' => $initiative,
            'speed' => $speed,
            'proficiency_bonus' => $profBonus,
            'armor_class' => $armorClass,
            //'base_attk_bonus' => ???,
            'armor_prof' => $armorProf,
            'weapon_prof' => $weaponProf,
            'tool_prof' => $toolProf,
            'saving_throws' => $savingThrows,
            'str_save' => $strSave,
            'dex_save' => $dexSave,
            'con_save' => $conSave,
            'int_save' => $intSave,
            'wis_save' => $wisSave,
            'cha_save' => $chaSave,
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
            'acrobatics' => $acrobatics,
            'animal_handling' => $animalHandling,
            'arcana' => $arcana,
            'athletics' => $athletics,
            'deception' => $deception,
            'history' => $history,
            'insight' => $insight,
            'intimidation' => $intimidation,
            'investigation' => $investigation,
            'medicine' => $medicine,
            'nature' => $nature,
            'perception' => $perception,
            'performance' => $performance,
            'persuasion' => $persuasion,
            'religion' => $religion,
            'sleight_of_hand' => $sleightOfHand,
            'stealth' => $stealth,
            'survival' => $survival,
            'senses' => $passivePerception,
            'expertise' => $expertise,
            'spell_ability' => $spellAbility,
            'spell_save_dc' => $spellSaveDC,
            'spell_attk_bonus' => $spellAttkBonus,
            'bonus_spell_ability' => $bonusSpellAbility,
            'bonus_spell_save_dc' => $bonusSpellSaveDC,
            'bonus_spell_attk_bonus' => $bonusSpellAttkBonus,
            'user_id' => $user_id,
            'date_added' => date("m/d/Y")
        );
        //return $data;   // for testing only
        // TODO: uncomment later
        $this->db->insert('character_table', $data);
        //$this->db->insert_batch('character_features', $racialTraits); // no longer needed
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
                //$character['user_id'] = $row->user_id;
                $character['name'] = $row->name;
                $character['level'] = $row->level;
                $character['race'] = $row->race;
                $character['class'] = $row->class;
                array_push($characters, $character);
            }
            return $characters;
        } else {
            return array();  // no characters
        }
    }

    public function get_character($characterId) {
        $userId = $this->session->userdata['logged_in']['id'];
        $sql = "SELECT character_table.*" .
            " FROM character_table" .
            " WHERE character_table.user_id = '" . $userId . "' AND character_table.id = '" . $characterId . "'";
        $query = $this->db->query($sql);
        $ability_mapper = array('str'=>'Strength', 'dex'=>'Dexterity', 'con'=>'Constitution', 'int'=>'Intelligence', 'wis'=>'Wisdom', 'cha'=>'Charisma');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $character['name'] = $row->name;
            $character['level'] = $row->level;
            $character['race'] = $row->race;
            $character['class'] = $row->pseudo_class ? $row->class . ' (' . $row->pseudo_class . ')' : $row->class;
            $character['armor_class'] = $row->armor_class;
            $character['hit_points'] = $row->hit_points;
            $character['hit_dice'] = $row->hit_dice;
            $character['initiative'] = $row->initiative >= 0 ? '+' . $row->initiative : $row->initiative;
            $character['proficiency_bonus'] = $row->proficiency_bonus;
            $character['proficiencies'] = $row->armor_prof != 'None' ? $row->armor_prof . ', ' : '';
            $character['proficiencies'] .= $row->weapon_prof;
            $toolProf = $row->tool_prof != '' ? ', ' . $row->tool_prof : '';
            $character['proficiencies'] .= $toolProf;
            $character['speed'] = $row->speed;
            $character['languages'] = $row->languages;
            $character['strength'] = $row->strength;
            $character['dexterity'] = $row->dexterity;
            $character['constitution'] = $row->constitution;
            $character['intelligence'] = $row->intelligence;
            $character['wisdom'] = $row->wisdom;
            $character['charisma'] = $row->charisma;
            $character['str_mod'] = $row->str_mod >= 0 ? '+' . $row->str_mod : $row->str_mod;
            $character['dex_mod'] = $row->dex_mod >= 0 ? '+' . $row->dex_mod : $row->dex_mod;
            $character['con_mod'] = $row->con_mod >= 0 ? '+' . $row->con_mod : $row->con_mod;
            $character['int_mod'] = $row->int_mod >= 0 ? '+' . $row->int_mod : $row->int_mod;
            $character['wis_mod'] = $row->wis_mod >= 0 ? '+' . $row->wis_mod : $row->wis_mod;
            $character['cha_mod'] = $row->cha_mod >= 0 ? '+' . $row->cha_mod : $row->cha_mod;
            $character['str_st'] = $row->str_save >= 0 ? '+' . $row->str_save : $row->str_save;
            $character['dex_st'] = $row->dex_save >= 0 ? '+' . $row->dex_save : $row->dex_save;
            $character['con_st'] = $row->con_save >= 0 ? '+' . $row->con_save : $row->con_save;
            $character['int_st'] = $row->int_save >= 0 ? '+' . $row->int_save : $row->int_save;
            $character['wis_st'] = $row->wis_save >= 0 ? '+' . $row->wis_save : $row->wis_save;
            $character['cha_st'] = $row->cha_save >= 0 ? '+' . $row->cha_save : $row->cha_save;
            //$character['skills'] = $row->skills;
            $character['acrobatics'] = $row->acrobatics >= 0 ? '+' . $row->acrobatics : $row->acrobatics;
            $character['animal_handling'] = $row->animal_handling >= 0 ? '+' . $row->animal_handling : $row->animal_handling;
            $character['arcana'] = $row->arcana >= 0 ? '+' . $row->arcana : $row->arcana;
            $character['athletics'] = $row->athletics >= 0 ? '+' . $row->athletics : $row->athletics;
            $character['deception'] = $row->deception >= 0 ? '+' . $row->deception : $row->deception;
            $character['history'] = $row->history >= 0 ? '+' . $row->history : $row->history;
            $character['insight'] = $row->insight >= 0 ? '+' . $row->insight : $row->insight;
            $character['intimidation'] = $row->intimidation >= 0 ? '+' . $row->intimidation : $row->intimidation;
            $character['investigation'] = $row->investigation >= 0 ? '+' . $row->investigation : $row->investigation;
            $character['medicine'] = $row->medicine >= 0 ? '+' . $row->medicine : $row->medicine;
            $character['nature'] = $row->nature >= 0 ? '+' . $row->nature : $row->nature;
            $character['perception'] = $row->perception >= 0 ? '+' . $row->perception : $row->perception;
            $character['performance'] = $row->performance >= 0 ? '+' . $row->performance : $row->performance;
            $character['persuasion'] = $row->persuasion >= 0 ? '+' . $row->persuasion : $row->persuasion;
            $character['religion'] = $row->religion >= 0 ? '+' . $row->religion : $row->religion;
            $character['sleight_of_hand'] = $row->sleight_of_hand >= 0 ? '+' . $row->sleight_of_hand : $row->sleight_of_hand;
            $character['stealth'] = $row->stealth >= 0 ? '+' . $row->stealth : $row->stealth;
            $character['survival'] = $row->survival >= 0 ? '+' . $row->survival : $row->survival;
            $character['senses'] = $row->senses;
            $character['traits'] = $this->_getRacialTraits($row->racial_trait_ids);
            $character['features'] = $this->_getClassFeatures($row->class_feature_ids, $row->cantrips, $row->expertise);
            $character['background'] = $this->_getBackground($row->background);
            $character['spellcasting'] = !empty($row->spell_ability) ? $ability_mapper[$row->spell_ability] : NULL;
            $character['spell_save_dc'] = !empty($row->spell_save_dc) ? $row->spell_save_dc : NULL;
            $character['spell_attk_bonus'] = $row->spell_attk_bonus >= 0 ? '+' . $row->spell_attk_bonus : $row->spell_attk_bonus;
            $character['bonus_spellcasting'] = !empty($row->bonus_spell_ability) ? $ability_mapper[$row->bonus_spell_ability] : NULL;
            $character['bonus_spell_save_dc'] = $row->bonus_spell_save_dc;
            $character['bonus_spell_attk_bonus'] = $row->bonus_spell_attk_bonus >= 0 ? '+' . $row->bonus_spell_attk_bonus : $row->bonus_spell_attk_bonus;
            $character['cantrips'] = $row->cantrips;
            $character['bonus_cantrip'] = $row->bonus_cantrip;
            return $character;
        } else {
            // TODO: handle error
        }
    }

    public function delete_character($characterId) {
        $this->db->delete('character_table', array('id' => $characterId));  // DELETE FROM character_table WHERE id = $characterId
    }

    private function _getRacialTraits($racialTraitIds) {
        $racialTraitIds = !empty($racialTraitIds) ? $racialTraitIds : "''";
        $sql = "SELECT race_features.*" .
            " FROM race_features" .
            " WHERE race_features.id IN (" . $racialTraitIds . ")";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $racialTraits = array();
            foreach ($query->result() as $row) {
                $racialTrait['name'] = $this->_getFeatureName($row->feature_id);
                $racialTrait['description'] = $row->benefit;
                array_push($racialTraits, $racialTrait);
            }
            return $racialTraits;
        }
    }

    private function _getFeatureName($featureId) {
        $sql = "SELECT features_table.name" .
            " FROM features_table" .
            " WHERE id = '" . $featureId . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->name;
        }
    }

    private function _getClassFeatures($classFeatureIds, $cantrips, $expertise) {
        $sql = "SELECT class_features.*" .
            " FROM class_features" .
            " WHERE class_features.id IN (" . $classFeatureIds . ")" .
            " ORDER BY level ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $classFeatures = array();
            foreach ($query->result() as $row) {
                $classFeature['name'] = $this->_getFeatureName($row->feature_id);
                if ($row->type == 'cantrips') {
                    $classFeature['benefit'] = 'You know the following cantrips, and can cast them at will: ' . $cantrips;
                } else {
                    $classFeature['benefit'] = $row->benefit;
                }
                // TODO: test this
                if ($row->type == 'expertise') {
                    $classFeature['benefit'] = 'Your proficiency bonus with the following are doubled: ' . $expertise;
                }
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
