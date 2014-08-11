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
    <li class="active"><a href="#cantrip" role="tab" data-toggle="tab">Cantrip</a></li>
    <li><a href="#level1" role="tab" data-toggle="tab">Level 1</a></li>
    <li><a href="#level2" role="tab" data-toggle="tab">Level 2</a></li>
    <li><a href="#level3" role="tab" data-toggle="tab">Level 3</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="cantrip">
        <?php echoSpellsByLevel($spells, 0); ?>
    </div>
    <div class="tab-pane" id="level1">
        <?php echoSpellsByLevel($spells, 1); ?>
    </div>
    <div class="tab-pane" id="level2">Coming soon...</div>
    <div class="tab-pane" id="level3">Coming soon...</div>
</div>