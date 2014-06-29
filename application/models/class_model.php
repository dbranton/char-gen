<?php

    class Class_model extends CI_Model {

        public function getClasses($selClass = NULL) { // if parameter is null, then get all classes
            if (is_null($selClass)) {
                $sql = "SELECT * FROM class_table WHERE type = 'class' ORDER BY name"; // WHERE subclass = ''";
            } else {
                $sql = "SELECT * FROM class_table WHERE type = 'class' AND name = '" . $selClass . "'";
            }
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                $character = array();
                foreach ($query->result() as $row) {
                    $class['name'] = $row->name;
                    $class['desc'] = $row->desc;
                    $class['ability_adj'] = $row->ability_adjustment;
                    $class['hit_dice'] = $row->hit_dice;
                    $class['armor_prof'] = $row->armor_shield_prof;
                    $class['weapon_prof'] = $row->weapon_prof;
                    $class['tools'] = $row->tools;
                    $class['saving_throws'] = $row->saving_throws;
                    $class['avail_skills'] = $row->avail_skills;
                    $class['num_skills'] = $row->num_skills;
                    $class['features'] = $this->_getClassLevelFeatures($row->id);  // returns array of key value pairs
                    $class['subclasses'] = $this->_getSubclasses($row->id);
                    if (count($class['subclasses']) == 0) {
                        $class['subclasses'] = NULL;
                    }
                    array_push($character, $class);
                }
                return (is_null($selClass)) ? $character : $character[0];
            }
        }

        private function _getSubclasses($classId) {   // ex: The Lifegiver, Circle of the Oak
            $sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND level IS NOT NULL";
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
        }


        /**
         * Use this query for phpmyadmin
            SELECT class_table.id, class_table.name, features_table.*, class_features.level
            FROM features_table JOIN class_features
            ON class_features.feature_id = features_table.id
            JOIN class_table
            ON class_table.id = class_features.class_id
            WHERE level IS NOT NULL ORDER BY class_table.name
         *
         */
        private function _getClassLevelFeatures($classId) {
            $sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND level IS NOT NULL ORDER BY level";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $class_feature['id'] = $row->id;
                    $class_feature['name'] = $row->name;
                    $class_feature['level'] = $row->level;
                    $class_feature['desc'] = $row->description;
                    $class_feature['benefit'] = $row->benefit;
                    if ($row->type == 'subclass') {
                        $class_feature['subclasses'] = $this->_getSubClassFeatures($row->id);
                        //$this->_getSubClassDesc($row->id);
                    }
                    if ($row->type == 'super_feature') {
                        $class_feature['subfeatures'] = $this->_getSubFeatures($row->id);
                    }
                    $features[$row->name] = $class_feature;
                    unset($class_feature['subclasses']);
                    unset($class_feature['subfeatures']);
                }
                return $features;
            }
        }

        // for features that have features (aka the Fighter's Superior Defense feature)
        /*private function _getSubFeatures($featureId) {
            $sql = "SELECT *" .
                " FROM features_table AS ft1" .
                " INNER JOIN feature_subfeature" .
                " ON feature_subfeature.feature_id = ft1.id" .
                " INNER JOIN features_table AS ft2" .
                " ON feature_subfeature.subfeature_id = ft2.id" .
                " WHERE feature_subfeature.feature_id = '" . $featureId . "'";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $subFeatures[$row->name] = $row->description;
                }
                return $subFeatures;
            }
        }*/

        private function _getSubClassFeatures($subclassId) {
            $sql = "SELECT class_table.*" .
                " FROM class_table" .
                " JOIN subclass_features" .
                " ON subclass_features.class_id = class_table.id" .
                " JOIN features_table" .
                " ON features_table.id = subclass_features.feature_id" .
                " WHERE subclass_features.feature_id = '" . $subclassId . "'";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $subclass_feature['id'] = $row->id;
                    $subclass_feature['name'] = $row->name;
                    $subclass_feature['desc'] = $row->desc;
                    $subclass_feature['armor_prof'] = $row->armor_shield_prof;
                    $subclass_feature['benefit'] = $this->_getSubClassFeatureBenefits($row->id);
                    $subclass[$row->name] = $subclass_feature;
                }
                return $subclass;
            } else {
                // no results
                //echo 'no subclass feature results';
                return 'no results';
            }
        }

        private function _getSubClassFeatureBenefits($subclassFeatureId) {
            /*$sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $classId . "' AND level IS NOT NULL ORDER BY level";*/

            $sql = "SELECT features_table.*, class_features.*" .
                " FROM features_table" .
                " JOIN class_features" .
                " ON class_features.feature_id = features_table.id" .
                " JOIN class_table" .
                " ON class_table.id = class_features.class_id" .
                " WHERE class_features.class_id = '" . $subclassFeatureId . "' ORDER BY level";
            $query = $this->db->query($sql);
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $class_feature['id'] = $row->id;
                    $class_feature['name'] = $row->name;
                    $class_feature['level'] = $row->level;
                    $class_feature['benefit'] = $row->description;
                    $features[$row->name] = $class_feature;
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