<?php
    function getArmor($armor, $type, $index) {
        $selArmor = $armor[$type][$index];
        echo '<tr>' .
            '<td>' . $selArmor['armor'] . '</td>' .
            '<td>' . $selArmor['cost'] . '</td>' .
            '<td>1' . $selArmor['armor_bonus'] . ' + Dex modifier</td>' .
            '<td>' . $selArmor['speed'] . '</td>' .
            '<td>' . $selArmor['armor_check_penalty'] . '</td>' .
            '</tr>';
    }
    function getWeapon($weapon, $type, $index) {
        $selWeapon = $weapon[$type][$index];
        echo '<tr>' .
            '<td>' . $selWeapon['name'] . '</td>' .
            '<td>' . $selWeapon['cost'] . '</td>' .
            '<td>' . $selWeapon['damage_medium'] . ' ' . $selWeapon['type'] . '</td>' .
            '<td>' . $selWeapon['properties'] . '</td>' .
            '</tr>';
    }
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" href="#collapseArmor">Armor</a>
    </div>
    <!--<div class="panel-body">
    </div>-->
    <div id="collapseArmor" class="collapse panel-collapse in">
        <table class="table table-striped panel-body" style="margin-bottom:0">
            <thead>
                <tr>
                    <th>Armor</th>
                    <th>Price</th>
                    <th>Armor Class (AC)</th>
                    <th>Speed</th>
                    <th>Stealth</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5"><em>Light Armor</em></td>
                </tr>
                <?php for ($i=0, $size=count($armor['light_armor']); $i<$size; $i++) {
                    getArmor($armor, 'light_armor', $i);
                } ?>
                <tr>
                    <td colspan="5"><em>Medium Armor</em></td>
                </tr>
                <?php for ($i=0, $size=count($armor['medium_armor']); $i<$size; $i++) {
                    getArmor($armor, 'medium_armor', $i);
                } ?>
                <tr>
                    <td colspan="5"><em>Heavy Armor</em></td>
                </tr>
                <?php for ($i=0, $size=count($armor['heavy_armor']); $i<$size; $i++) {
                    getArmor($armor, 'heavy_armor', $i);
                } ?>
                <tr>
                    <td colspan="5"><em>Shield</em></td>
                </tr>
                <?php for ($i=0, $size=count($armor['shields']); $i<$size; $i++) {
                    getArmor($armor, 'shields', $i);
                } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <a data-toggle="collapse" href="#collapseWeapon">Weapons</a>
    </div>
    <!--<div class="panel-body">
    </div>-->
    <div id="collapseWeapon" class="collapse panel-collapse in">
        <table class="table table-striped panel-body" style="margin-bottom:0">
            <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Damage</th>
                <th>Properties</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="4"><em>Simple Melee Weapons</em></td>
            </tr>
            <?php for ($i=0, $size=count($weapon['simple_melee']); $i<$size; $i++) {
                getWeapon($weapon, 'simple_melee', $i);
            } ?>
            <tr>
                <td colspan="4"><em>Simple Ranged Weapons</em></td>
            </tr>
            <?php for ($i=0, $size=count($weapon['simple_ranged']); $i<$size; $i++) {
                getWeapon($weapon, 'simple_ranged', $i);
            } ?>
            <tr>
                <td colspan="4"><em>Martial Melee Weapons</em></td>
            </tr>
            <?php for ($i=0, $size=count($weapon['martial_melee']); $i<$size; $i++) {
                getWeapon($weapon, 'martial_melee', $i);
            } ?>
            <tr>
                <td colspan="4"><em>Martial Ranged Weapons</em></td>
            </tr>
            <?php for ($i=0, $size=count($weapon['martial_ranged']); $i<$size; $i++) {
                getWeapon($weapon, 'martial_ranged', $i);
            } ?>
            </tbody>
        </table>
    </div>
</div>
