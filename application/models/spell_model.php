<?php

class Spell_model extends CI_Model {
    public $classId = NULL;
    public $term = '';
    public $restrictedSchool = "'Abjuration','Conjuration','Divination','Enchantment','Evocation','Illusion','Necromancy','Transmutation'";
    public function getSpells($classId = NULL, $maxSpellLevel = 10, $term = '', $restrictedSchool1 = FALSE, $restrictedSchool2 = FALSE) {
        $this->classId = $classId;
        $this->term = $term;
        if ($restrictedSchool1 != FALSE && $restrictedSchool2 != FALSE) {
            $this->restrictedSchool = "'" . $restrictedSchool1 . "','" . $restrictedSchool2 . "'";
        }
        $this->restrictedSchool1 = $restrictedSchool1;
        $this->restrictedSchool2 = $restrictedSchool2;
        $spells = array();
        for ($i=0; $i<=intval($maxSpellLevel); $i++) {
            if (intval($maxSpellLevel) < 10 && $i == 0) {
                $i++;   // skip cantrips
            }
            $spellsByLevel = $this->_getSpellsByLevel($i);
            if (count($spellsByLevel) > 0 || $term !== '') {
                array_push($spells, $spellsByLevel);
            } else {
                break;
            }
        }
        return $spells;
    }

    public function getClassName($classId) {
        $sql = "SELECT name FROM class_table WHERE id = '" . $classId . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->name;
        }
    }

    private function _getSpellsByLevel($level) {
        if (is_null($this->classId)) {
            $sql = "SELECT * FROM spells_table WHERE level = '" . $level . "' ORDER BY name ASC";
        } else {
            $sql = "SELECT spells_table.*" .
                " FROM spells_table" .
                " JOIN class_spells" .
                " ON class_spells.spell_id = spells_table.id" .
                " WHERE type IN (" . $this->restrictedSchool . ") AND level = '" . $level . "' AND class_id = '" . $this->classId . "'" .
                " AND name LIKE '%" . $this->term . "%' ORDER BY name ASC";
        }
        $query = $this->db->query($sql);
        $spells = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $spell['id'] = $row->id;
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
        }
        return $spells;
    }
}
?>