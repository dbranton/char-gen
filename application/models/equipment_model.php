<?php

class Equipment_model extends CI_Model {

    public function getArmors() {
        $sql = "SELECT * FROM armor_table WHERE active = '1' ORDER BY sequence ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $equipment = array(
                'light_armor' => array(),
                'medium_armor' => array(),
                'heavy_armor' => array(),
                'shields' => array()
            );
            foreach ($query->result() as $row) {
                $armor['armor'] = $row->armor;
                $armor['cost'] = $row->cost;
                $armor['armor_bonus'] = $row->armor_bonus;
                $armor['max_dex_bonus'] = $row->max_dex_bonus;
                if ($armor['max_dex_bonus'] == '0') {
                    $armor['bonus_desc'] = $armor['armor_bonus'];
                } else if ($armor['max_dex_bonus'] > 0) {
                    $armor['bonus_desc'] = $armor['armor_bonus'] . ' + Dexterity modifier (max ' . $armor['max_dex_bonus'] . ')';
                } else {
                    $armor['bonus_desc'] = $armor['armor_bonus'] . ' + Dexterity modifier';
                }
                $armor['max_dex_bonus'] = $row->max_dex_bonus;
                $armor['armor_check_penalty'] = (empty($row->armor_check_penalty)) ? '&mdash;' : $row->armor_check_penalty;
                $armor['strength'] = (empty($row->strength)) ? '&mdash;' : 'Str ' . $row->strength;
                array_push($equipment[$row->armor_type], $armor);
            }
            return $equipment;
        }
    }

    public function getWeapons() {
        $sql = "SELECT * FROM weapons_table WHERE active = '1' ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $equipment = array(
                'simple_melee' => array(),
                'simple_ranged' => array(),
                'martial_melee' => array(),
                'martial_ranged' => array()
            );
            foreach ($query->result() as $row) {
                $weapon['name'] = $row->name;
                $weapon['cost'] = (empty($row->cost)) ? '&mdash;' : $row->cost;
                $weapon['damage_medium'] = $row->damage_medium;
                $weapon['type'] = $row->type;
                $weapon['properties'] = (empty($row->properties)) ? '&mdash;' : $row->properties;
                array_push($equipment[$row->style], $weapon);
            }
            return $equipment;
        }
    }
}

?>