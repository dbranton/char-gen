<?php

class Spell_model extends CI_Model {

    public function getSpells() {
        $spells = array();
        $cantrips = $this->_getSpellsByLevel(0);
        $level1Spells = $this->_getSpellsByLevel(1);
        $level2Spells = $this->_getSpellsByLevel(2);
        array_push($spells, $cantrips, $level1Spells, $level2Spells);
        return $spells;
    }

    private function _getSpellsByLevel($level) {
        $sql = "SELECT * FROM spells_table WHERE level = '" . $level . "' ORDER BY name ASC";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $spells = array();
            foreach ($query->result() as $row) {
                $spell['name'] = $row->name;
                $spell['level'] = $row->level;  //$row->level == '0' ? 'Cantrip' : 'Level ' . $row->level;
                $spell['desc'] = $row->description;
                $spell['type'] = $row->type;
                $spell['type_desc'] = $row->level == '0' ? $row->type . ' cantrip' : 'Level ' . $row->level . ' ' . $row->type;
                if ($row->ritual == '1') {
                    $spell['type_desc'] .= ' (ritual)';
                }
                $spell['casting_time'] = $row->casting_time;
                $spell['range'] = $row->range;
                $spell['components'] = $row->components;
                $spell['duration'] = $row->duration;
                array_push($spells, $spell);
            }
            return $spells;
        }
    }
}
?>