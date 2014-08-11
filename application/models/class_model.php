<?php

    class Class_model extends CI_Model {

        public function getClasses($selClass = NULL) { // if parameter is null, then get all classes
            if (is_null($selClass)) {
                $sql = "SELECT * FROM class_table WHERE type = 'class' AND active = '1' ORDER BY name"; // WHERE subclass = ''";
            } else {
                $sql = "SELECT * FROM class_table WHERE type = 'class' AND name = '" . $selClass . "' AND active = '1'";
            }
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $character = array();
                foreach ($query->result() as $row) {
                    $class['name'] = $row->name;
                    $class['desc'] = $row->desc;
                    $class['hit_dice'] = $row->hit_dice;
                    $class['armor_prof'] = $row->armor_shield_prof;
                    $class['weapon_prof'] = $row->weapon_prof;
                    $class['tools'] = $row->tools;
                    $class['saving_throws'] = $row->saving_throw_desc;
                    $class['saving_throw_code'] = $row->saving_throws;
                    $class['avail_skills_desc'] = $row->avail_skills_desc;
                    $class['avail_skills'] = $row->avail_skills;
                    $class['num_skills'] = $row->num_skills;
                    $class['features'] = $this->_getClassLevelFeatures($row->id);  // returns array of key value pairs
                    /*$class['subclasses'] = $this->_getSubclasses($row->id);
                    if (count($class['subclasses']) == 0) {
                        $class['subclasses'] = NULL;
                    }*/
                    array_push($character, $class);
                }
                return (is_null($selClass)) ? $character : $character[0];
            }
        }

        /*private function _getSubclasses($classId) {   // ex: The Lifegiver, Circle of the Oak
            $sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND class_features.level IS NOT NULL";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $subclasses = array();
                foreach ($query->result() as $row) {

                    if ($row->type == 'subclass') {
                        $subclasses = $this->_getSubClassFeatures($row->id);
                        //array_push($character, $subclasses);
                        //$this->_getSubClassDesc($row->id);
                    }

                }
                return $subclasses;
            }
        }*/


        private function _getClassFeatureBenefits($featureId) {
            $sql = "SELECT class_features.*" .
                " FROM class_features" .
                " JOIN features_table" .
                " ON features_table.id = class_features.feature_id" .
                " WHERE class_features.feature_id = '" . $featureId . "' AND class_features.level IS NOT NULL ORDER BY class_features.level, name";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $benefit_array = array();
                foreach ($query->result() as $row) {
                    $benefit['id'] = $row->id;
                    $benefit['description'] = $row->benefit;
                    $benefit['level'] = $row->level;
                    array_push($benefit_array, $benefit);
                }
                return $benefit_array;
            }
        }

        /**
         * Use this query for phpmyadmin
            SELECT class_table.id, class_table.name, features_table.*, class_features.level
            FROM features_table JOIN class_features
            ON class_features.feature_id = features_table.id
            JOIN class_table
            ON class_table.id = class_features.class_id
            WHERE level IS NOT NULL AND class_table.active = '1' ORDER BY class_table.name
         *
         */
        private function _getClassLevelFeatures($id, $isFeatureId = FALSE, $classId = NULL) {    // if isFeatureId is false, then use classId
            /*$sql = "SELECT DISTINCT features_table.*" . //, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND level IS NOT NULL ORDER BY level";*/
            if ($isFeatureId == FALSE) {
                $sql = "SELECT features_table.*" .
                    " FROM features_table" .
                    " JOIN class_table" .
                    " ON class_table.id = features_table.class_id" .
                    " WHERE features_table.class_id = '" . $id . "' AND features_table.level IS NOT NULL AND parent_id = '0'" .
                    " ORDER BY features_table.level, name";
            } else {
                $sql = "SELECT features_table.*" .
                    " FROM features_table" .
                    " WHERE features_table.parent_id = '" . $id . "'" .
                    " ORDER BY features_table.name";
            }
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $features = array();
                foreach ($query->result() as $row) {
                    //if ($row->parent_id == '0') {
                        $class_feature = array();   // reset
                        $class_feature['id'] = $row->id;
                        $class_feature['name'] = $row->name;
                        $class_feature['level'] = $row->level;
                        $class_feature['desc'] = $row->description;
                        $class_feature['benefit'] = $this->_getClassFeatureBenefits($row->id);
                        $class_feature['benefit_stat'] = $row->benefit;
                        $class_feature['benefit_value'] = $row->benefit_value;
                        if ($class_feature['benefit_stat'] == 'cantrips') {
                            $class_feature['cantrips'] = $this->_getCantrips($classId);
                        }
                        if ($row->type == 'subclass') {
                            $class_feature['subclasses'] = $this->_getSubClasses($row->id);
                            //$this->_getSubClassDesc($row->id);
                        }
                        if ($row->type == 'super_feature') {
                            $class_feature['subfeatures'] = $this->_getClassLevelFeatures($row->id, TRUE, $id);
                        }
                        array_push($features, $class_feature);
                        //$features[$class_feature['id']] = $class_feature; // was [$row->name] but can't because names are no longer unique
                        //unset($class_feature['subclasses']);
                        //unset($class_feature['subfeatures']);
                    //}
                }
                return $features;
            }
        }

        // for features that have features (aka the Fighter's Superior Defense feature)
        /*private function _getSubFeatures($featureId) {
            $sql = "SELECT *" .
                " FROM features_table AS ft1" .
                " WHERE parent_id = '" . $featureId . "'";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $subFeatures[$row->name] = $row->description;
                }
                return $subFeatures;
            }
        }*/

        private function _getCantrips($classId) {
            $sql = "SELECT spells_table.*" .
                " FROM spells_table " .
                " JOIN class_spells" .
                " ON class_spells.spell_id = spells_table.id" .
                " WHERE level = '0' AND class_spells.class_id = '" . $classId . "' ORDER BY name ASC";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $spells = array();
                foreach ($query->result() as $row) {
                    $spell['name'] = $row->name;
                    $spell['level'] = $row->level;
                    $spell['desc'] = $row->description;
                    $spell['type'] = $row->type;
                    $spell['casting_time'] = $row->casting_time;
                    $spell['range'] = $row->range;
                    $spell['components'] = $row->components;
                    $spell['duration'] = $row->duration;
                    array_push($spells, $spell);
                }
                return $spells;
            }
        }

        private function _getSubClasses($subclassId) {
            $sql = "SELECT class_table.*" .
                " FROM class_table" .
                " JOIN subclass_features" .
                " ON subclass_features.class_id = class_table.id" .
                " JOIN features_table" .
                " ON features_table.id = subclass_features.feature_id" .
                " WHERE subclass_features.feature_id = '" . $subclassId . "' AND active = '1'";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $subclasses = array();
                foreach ($query->result() as $row) {
                    $subclass['id'] = $row->id;
                    $subclass['name'] = $row->name;
                    $subclass['desc'] = $row->desc;
                    $subclass['armor_prof'] = $row->armor_shield_prof;
                    $subclass['benefit'] = $this->_getSubClassFeatures($row->id);    // features
                    array_push($subclasses, $subclass);
                }
                return $subclasses;
            } else {
                // no results
                //echo 'no subclass feature results';
                return 'no results';
            }
        }

        private function _getSubClassFeatures($subclassFeatureId) {
            /*$sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND level IS NOT NULL ORDER BY level";*/

            $sql = "SELECT features_table.*" . //, class_features.*" .
                " FROM features_table" .
                //" JOIN class_features" .
                //" ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = features_table.class_id" . //class_features.class_id" .
                " WHERE features_table.class_id = '" . $subclassFeatureId . "' ORDER BY features_table.level";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $features = array();
                foreach ($query->result() as $row) {
                    $class_feature['id'] = $row->id;
                    $class_feature['name'] = $row->name;
                    $class_feature['level'] = $row->level;
                    $class_feature['desc'] = $row->description;
                    $class_feature['benefit'] = $this->_getClassFeatureBenefits($row->id);
                    $class_feature['benefit_stat'] = $row->benefit;
                    $class_feature['benefit_value'] = $row->benefit_value;
                    //$features[$row->name] = $class_feature;
                    array_push($features, $class_feature);
                }
                return $features;
            }
        }
        /*private function _getSubClassDesc($subclassId) {
            $sql = "SELECT * FROM class_table WHERE name = '" . $subclassId . "'";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $data = $query->row();
                return $data->desc;
            }
        }*/

    }

?>