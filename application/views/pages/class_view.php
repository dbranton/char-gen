<?php
    echo '<p>' . $class['desc'] . '</p>';?>
    <h4>Hit Points</h4>
    <ul>
        <li><b>Hit Dice</b>: 1d<?php echo $class['hit_dice']; ?> per <?php echo $class['name']; ?> level</li>
        <li><b>Hit Points at 1st</b>: <?php echo $class['hit_dice']; ?> + your Constitution modifier</li>
        <li><b>Weapon Proficiencies</b>: 1d<?php echo $class['hit_dice']; ?> + your Constitution modifier per <?php echo $class['name']; ?> level after 1st</li>
    </ul>
    <h4>Proficiencies</h4>
    <ul>
        <li><b>Armor</b>: <?php echo $class['armor_prof']; ?></li>
        <li><b>Weapons</b>: <?php echo $class['weapon_prof']; ?></li>
        <li><b>Tools</b>: <?php echo $class['tools']; ?></li>
        <li><b>Saving Throws</b>: <?php echo $class['saving_throws']; ?></li>
        <li><b>Skills</b>: <?php echo $class['avail_skills_desc']; ?></li>
    </ul>
<?php
    if (is_array($class['features'])) {
        foreach ($class['features'] as $featureId => $class_feature) {
            echo '<h3>' . $class_feature['name'] . '</h3>';
            echo '<p>' . $class_feature['desc'] . '</p>';
            //echo '<p>' . $class_feature['benefit'] . '</p>';
            if (isset($class_feature['subclasses']) && is_array($class_feature['subclasses'])) {
                echo '<div class="panel-group">';
                foreach ($class_feature['subclasses'] as $subclassName => $subclassArray) {
                    echo '<div class="panel panel-default">';
                    echo '<div class="panel-heading" data-toggle="collapse" data-target="#subclass-' . $subclassArray['id'] . '">';
                    echo '<h4 class="panel-title">' . $subclassArray['name'] . '</h4>';
                    echo '</div>';  // end panel-heading
                    echo '<div id="subclass-' . $subclassArray['id'] . '" class="panel-body collapse">';
                    echo '<p>' . $subclassArray['desc'] . '</p>';
                    echo '<dl>';
                    /*if ($subclassArray['align'] !== '') {
                        echo '<li><b>Alignment</b>: ' . $subclassArray['align'] . '</li>';
                    }*/
                    if (is_array($subclassArray['benefit'])) {
                        foreach ($subclassArray['benefit'] as $name => $abilityInfo) {
                            echo '<dt>' . $abilityInfo['name'] . '</dt>';
                            echo '<dd>' . $abilityInfo['desc'];
                            if (isset($abilityInfo['subfeatures'])) {
                                echo '<ul>';
                                foreach ($abilityInfo['subfeatures'] as $name => $abilityInfo) {
                                    echo '<li><b>' . $abilityInfo['name'] . '</b>: ' . $abilityInfo['desc'] . '</li>';
                                }
                                echo '</ul>';
                            }
                            echo '</dd>';
                        }
                    }
                    echo '</dl>';
                    echo '</div>'; // end panel-body
                    echo '</div>';  // end panel
                }
                echo '</div>';  // end panel-group
            }
            if (isset($class_feature['subfeatures']) && is_array($class_feature['subfeatures'])) {
                echo '<dl>';
                foreach ($class_feature['subfeatures'] as $id => $subfeature) {
                    echo '<dt>' . $subfeature['name'] . '</dt>';
                    echo '<dd>'. $subfeature['desc'] . '</dd>';
                }
                echo '</dl>';
            }
        }
    }

?>