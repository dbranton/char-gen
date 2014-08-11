<?php

class Race_model extends CI_Model {

    public function getRaces($selRace = NULL) {    // if parameter is null, then get all races
        if (is_null($selRace)) {
            $sql = "SELECT * FROM race_table WHERE subrace = ''";
        } else {
            $sql = "SELECT * FROM race_table WHERE subrace = '' AND name = '" . $selRace . "'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $character = array();
            foreach ($query->result() as $row) {
                $race['name'] = $row->name;
                $race['desc'] = $row->description;
                $race['ability_score_adjustment'] = $row->ability_score_adjustment;
                $race['ability_bonus'] = intval($row->ability_bonus);
                $race['ability_name'] = $row->ability_name;
                $race['size'] = $row->size_desc;
                $race['size_value'] = $row->size;
                $race['speed'] = $row->speed_desc;
                $race['speed_value'] = $row->speed;
                $race['language_desc'] = $row->language_desc;
                $race['languages'] = $row->languages;
                $race['traits'] = $this->getTraits($row->readable_id);
                $race['subraces'] = $this->getSubraces($row->name);
                array_push($character, $race);
            }
            return (is_null($selRace)) ? $character : $character[0];
        }
    }

    public function getDescription($race) {
        $sql = "SELECT description FROM race_table WHERE name = '" . $race . "' AND subrace = ''";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->row();
            return $data->description;
        }
    }

    public function getTraits($raceId) {
        $sql = "SELECT DISTINCT features_table.*, race_features.benefit AS benefit_desc, race_features.id AS race_feature_id" .
            " FROM features_table" .
            " JOIN race_features" .
            " ON race_features.feature_id = features_table.id" .
            " JOIN race_table" .
            " ON race_table.readable_id = race_features.race_id" .
            " WHERE race_features.race_id = '" . $raceId . "' AND race_features.subrace_id = ''" .
            " ORDER BY name ASC";
        //$sql = "SELECT * FROM race_features WHERE race = '" . $race . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $traits = array();
            foreach ($query->result() as $row) {
                //$trait[$row->name] = $row->description;
                $trait['id'] = $row->race_feature_id;
                $trait['name'] = $row->name;
                $trait['description'] = $row->description;
                $trait['benefit_desc'] = $row->benefit_desc;
                $trait['benefit_stat'] = $row->benefit;
                $trait['benefit_value'] = $row->benefit_value;
                $trait['per_level'] = $row->per_level;
                array_push($traits, $trait);
            }
            return $traits;
        }
    }

    public function getSubraces($race) {
        $sql = "SELECT * FROM race_table WHERE name = '" . $race . "' AND subrace <> ''";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $character = array();
            foreach ($query->result() as $row) {
                $subrace['name'] = $row->subrace;
                $subrace['desc'] = $row->subrace_desc;
                $subrace['ability_score_adjustment'] = $row->ability_score_adjustment;
                $subrace['ability_bonus'] = intval($row->ability_bonus);
                $subrace['ability_name'] = $row->ability_name;
                $subrace['traits'] = $this->getSubraceTraits($row->id);
                array_push($character, $subrace);
            }
            return $character;
        }

    }

    public function getSubraceTraits($subraceId) {
        $sql = "SELECT features_table.*, race_features.benefit AS benefit_desc, race_features.id AS race_feature_id" .
            " FROM features_table" .
            " JOIN race_features" .
            " ON race_features.feature_id = features_table.id" .
            " JOIN race_table" .
            " ON race_table.id = race_features.subrace_id" .
            " WHERE race_features.subrace_id = '" . $subraceId . "'" .
            " ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $traits = array();
            foreach ($query->result() as $row) {
                $trait['id'] = $row->race_feature_id;
                $trait['name'] = $row->name;
                $trait['description'] = $row->description;
                $trait['benefit_desc'] = $row->benefit_desc;
                $trait['benefit_stat'] = $row->benefit;
                $trait['benefit_value'] = $row->benefit_value;
                $trait['per_level'] = $row->per_level;
                array_push($traits, $trait);
            }
            return $traits;
        }
    }

}

?>