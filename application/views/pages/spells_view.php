<?php
    function echoSpellsByLevel($spells, $level) {
        foreach ($spells[$level] as $spell) {
            echo '<div class="well well-sm">';
            echo '<h4>' . $spell['name'] . '</h4>';
            echo '<p><i>' . $spell['type_desc'] . ' ' . ' </i></p>';
            echo '<ul class="list-unstyled">';
            echo '<li><b>Casting Time: </b>' . $spell['casting_time'] . '</li>';
            echo '<li><b>Range: </b>' . $spell['range'] . '</li>';
            echo '<li><b>Components: </b>' . $spell['components'] . '</li>';
            echo '<li><b>Duration: </b>' . $spell['duration'] . '</li>';
            echo '</ul>';
            echo '<p>' . $spell['desc'] . '</p>';
            echo '<a class="pull-right" href="#top">Back to top</a>';
            echo '<div class="clearfix"></div>';
            echo '</div>';
        }
    }
?>
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <?php
    $active_flag = FALSE;
    for ($i=0; $i<count($spells); $i++) {
        if (count($spells[$i]) == 0) {
            $i++;
        }
        $active = '';
        if (!$active_flag) {
            $active = 'active';
            $active_flag = TRUE;
        }
        $name = $i == 0 ? 'Cantrip' : 'Level ' . $i;
        echo '<li class="' . $active . '"><a href="#level' . $i . '" role="tab" data-toggle="tab">' . $name . '</a></li>';
    }
    ?>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <?php
    $active_flag = FALSE;
    for ($i=0; $i<count($spells); $i++) {
        if (count($spells[$i]) == 0) {
            $i++;
        }
        $active = '';
        if (!$active_flag) {
            $active = ' active';
            $active_flag = TRUE;
        }
        echo '<div class="tab-pane' . $active . '" id="level' . $i . '">';
        echo echoSpellsByLevel($spells, $i);
        echo '</div>';
    }
    ?>
</div>