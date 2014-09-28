<div class="col-md-6">
    <?php //var_dump($character); ?>

    <table class="table">
        <thead>
            <tr>
                <th colspan="2">Level <?php echo $character['level'] . ' ' . $character['race'] . ' ' . $character['class']; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Background</th>
                <td><?php echo $character['background']['name']; ?></td>
            </tr>
            <tr>
                <th>Armor Class</th>
                <td><?php echo $character['armor_class']; ?></td>
            </tr>
            <tr>
                <th>Hit Points</th>
                <td><?php echo $character['hit_points'] . ' (1d' . $character['hit_dice'] . ' Hit Die)'; ?></td>
            </tr>
            <tr>
                <th>Initiative</th>
                <td><?php echo $character['initiative']; ?></td>
            </tr>
            <tr>
                <th>Speed</th>
                <td><?php echo $character['speed'] . ' feet'; ?></td>
            </tr>
            <tr>
                <th>Proficiency Bonus</th>
                <td><?php echo '+' . $character['proficiency_bonus']; ?></td>
            </tr>
            <tr>
                <th>Proficiencies</th>
                <td><?php echo $character['proficiencies']; ?></td>
            </tr>
            <tr>
                <th>Languages</th>
                <td><?php echo $character['languages']; ?></td>
            </tr>
            <tr>
                <th>Passive Perception</th>
                <td><?php echo $character['senses'];?></td>
            </tr>
        </tbody>
    </table>
    <div>
        <h4>Ability Scores</h4>
        <table class="table table-condensed">
            <thead>
                <th>Ability</th>
                <th>Base</th>
                <th>Mod</th>
                <th><abbr title="Saving Throws">ST</abbr></th>
            </thead>
            <tbody>
                <tr>
                    <td>Strength</td>
                    <td><?php echo $character['strength']; ?></td>
                    <td><?php echo $character['str_mod']; ?></td>
                    <td><?php echo $character['str_st']; ?></td>
                </tr>
                <tr>
                    <td>Dexterity</td>
                    <td><?php echo $character['dexterity']; ?></td>
                    <td><?php echo $character['dex_mod']; ?></td>
                    <td><?php echo $character['dex_st']; ?></td>
                </tr>
                <tr>
                    <td>Constitution</td>
                    <td><?php echo $character['constitution']; ?></td>
                    <td><?php echo $character['con_mod']; ?></td>
                    <td><?php echo $character['con_st']; ?></td>
                </tr>
                <tr>
                    <td>Intelligence</td>
                    <td><?php echo $character['intelligence']; ?></td>
                    <td><?php echo $character['int_mod']; ?></td>
                    <td><?php echo $character['int_st']; ?></td>
                </tr>
                <tr>
                    <td>Wisdom</td>
                    <td><?php echo $character['wisdom']; ?></td>
                    <td><?php echo $character['wis_mod']; ?></td>
                    <td><?php echo $character['wis_st']; ?></td>
                </tr>
                <tr>
                    <td>Charisma</td>
                    <td><?php echo $character['charisma']; ?></td>
                    <td><?php echo $character['cha_mod']; ?></td>
                    <td><?php echo $character['cha_st']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <h4>Skills</h4>
        <ul class="list-inline">
            <li>Acrobatics <?php echo $character['acrobatics']; ?></li>
            <li>Animal Handling <?php echo $character['acrobatics']; ?></li>
            <li>Arcana <?php echo $character['arcana']; ?></li>
            <li>Athletics <?php echo $character['athletics']; ?></li>
            <li>Deception <?php echo $character['deception']; ?></li>
            <li>History <?php echo $character['history']; ?></li>
            <li>Insight <?php echo $character['insight']; ?></li>
            <li>Intimidation <?php echo $character['intimidation']; ?></li>
            <li>Investigation <?php echo $character['investigation']; ?></li>
            <li>Medicine <?php echo $character['medicine']; ?></li>
            <li>Nature <?php echo $character['nature']; ?></li>
            <li>Perception <?php echo $character['perception']; ?></li>
            <li>Performance <?php echo $character['performance']; ?></li>
            <li>Persuasion <?php echo $character['persuasion']; ?></li>
            <li>Religion <?php echo $character['religion']; ?></li>
            <li>Sleight of Hand <?php echo $character['sleight_of_hand']; ?></li>
            <li>Stealth <?php echo $character['stealth']; ?></li>
            <li>Survival <?php echo $character['survival']; ?></li>
        </ul>
    </div>
    <?php if ($character['spellcasting']): ?>
    <div>
        <h4>Spellcasting</h4>
        <table class="table table-condensed">
            <tr>
                <th>Spellcasting Ability: </th>
                <td><?php echo $character['spellcasting']; ?></td>
            </tr>
            <tr>
                <th>Spell Save DC: </th>
                <td><?php echo $character['spell_save_dc']; ?></td>
            </tr>
            <tr>
                <th>Spell Attack Bonus: </th>
                <td><?php echo $character['spell_attk_bonus']; ?></td>
            </tr>
            <?php if ($character['cantrips']): ?>
            <tr>
                <th>Cantrips: </th>
                <td><?php echo $character['cantrips']; ?></td>
            </tr>
            <?php endif; ?>
            <?php foreach ($character['spells'] as $level => $spells_by_level): ?>
            <tr>
                <th><?php echo $level; ?></th>
                <td><?php echo $spells_by_level; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
    <?php if ($character['bonus_spellcasting']): ?>
    <div>
        <h4>Bonus Spellcasting</h4>
        <table class="table table-condensed">
            <tr>
                <th>Spellcasting Ability: </th>
                <td><?php echo $character['bonus_spellcasting']; ?></td>
            </tr>
            <tr>
                <th>Spell Save DC: </th>
                <td><?php echo $character['bonus_spell_save_dc']; ?></td>
            </tr>
            <tr>
                <th>Spell Attack Bonus: </th>
                <td><?php echo $character['bonus_spell_attk_bonus']; ?></td>
            </tr>
            <tr>
                <th>Cantrips: </th>
                <td><?php echo $character['bonus_cantrip']; ?></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
</div>
<div class="col-md-6">
    <?php
    if (is_array($character['traits'])) {
        echo '<h3>Racial Traits</h3>';
        //var_dump($character['traits']);
        foreach ($character['traits'] as $trait) {
            echo '<p><b>' . $trait['name'] . '</b>: ' . $trait['description'] . '</p>';
        }
    }
    if (is_array($character['features'])) {
        echo '<h3>Class Features</h3>';
        //var_dump($character['features']);
        foreach ($character['features'] as $feature) {
            echo '<p><b>' . $feature['name'] . '</b>: ' . $feature['benefit'] . '</p>';
        }
    }
    ?>
    <h3>Background Feature</h3>
    <p><b><?php echo $character['background']['trait_name']; ?></b>: <?php echo $character['background']['trait_desc']; ?></p>
</div>