<div class="modal-header">
    <h4 class="modal-title">Character Summary</h4>
</div>
<div class="modal-body">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#general" role="tab" data-toggle="tab">General</a></li>
        <li><a href="#features" role="tab" data-toggle="tab">Features</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="general">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr>
                            <th>Name: </th>
                            <td>{{character.name}}</td>
                        </tr>
                        <tr>
                            <th>Level: </th>
                            <td>{{character.level}}</td>
                        </tr>
                        <tr>
                            <th>Race: </th>
                            <td>{{character.raceObj.subrace.name}}</td>
                        </tr>
                        <tr>
                            <th>Background: </th>
                            <td>{{character.background.name}}</td>
                        </tr>
                        <tr>
                            <th>Class: </th>
                            <td>{{character.classObj.name}} <span ng-show="character.classObj.subclassObj.name">({{character.classObj.subclassObj.name}})</span></td>
                        </tr>
                        <tr>
                            <th>Armor Class: </th>
                            <td>{{character.armorClass}}</td>
                        </tr>
                        <tr>
                            <th>Initiative: </th>
                            <td>{{character.initiative}}</td>
                        </tr>
                        <tr>
                            <th>Speed: </th>
                            <td>{{character.speed}}</td>
                        </tr>
                        <tr>
                            <th>Hit Points: </th>
                            <td>{{character.hitPointsDesc}}</td>
                        </tr>
                        <tr>
                            <th>Proficiency Bonus: </th>
                            <td>{{character.profBonus}}</td>
                        </tr>
                        <tr ng-if="character.armor != 'None'">
                            <th>Armor Proficiencies: </th>
                            <td>{{character.armor}}</td>
                        </tr>
                        <tr>
                            <th>Weapon Proficiencies: </th>
                            <td>{{character.weapons}}</td>
                        </tr>
                        <tr ng-if="character.tools">
                            <th>Tool Proficiencies: </th>
                            <td>{{character.tools}}</td>
                        </tr>
                        <tr>
                            <th>Languages: </th>
                            <td>{{character.languages}}</td>
                        </tr>
                        <tr>
                            <th>Skills: </th>
                            <td>
                                <ul class="list-inline">
                                    <li ng-repeat="skill in character.skillsArr">{{skill}}</li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <table id="abilitySummary" class="table table-bordered table-striped table-condensed">
                            <thead>
                            <tr>
                                <th>Ability</th>
                                <th>Score</th>
                                <th>Mod</th>
                                <th><abbr title="Saving Throw">ST</abbr></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Strength</td>
                                <td>{{character.ability.str.score}}</td>
                                <td>{{character.ability.str.mod}}</td>
                                <td>{{character.ability.str.savingThrow}}</td>
                            </tr>
                            <tr>
                                <td>Dexterity</td>
                                <td>{{character.ability.dex.score}}</td>
                                <td>{{character.ability.dex.mod}}</td>
                                <td>{{character.ability.dex.savingThrow}}</td>
                            </tr>
                            <tr>
                                <td>Constitution</td>
                                <td>{{character.ability.con.score}}</td>
                                <td>{{character.ability.con.mod}}</td>
                                <td>{{character.ability.con.savingThrow}}</td>
                            </tr>
                            <tr>
                                <td>Intelligence</td>
                                <td>{{character.ability.int.score}}</td>
                                <td>{{character.ability.int.mod}}</td>
                                <td>{{character.ability.int.savingThrow}}</td>
                            </tr>
                            <tr>
                                <td>Wisdom</td>
                                <td>{{character.ability.wis.score}}</td>
                                <td>{{character.ability.wis.mod}}</td>
                                <td>{{character.ability.wis.savingThrow}}</td>
                            </tr>
                            <tr>
                                <td>Charisma</td>
                                <td>{{character.ability.cha.score}}</td>
                                <td>{{character.ability.cha.mod}}</td>
                                <td>{{character.ability.cha.savingThrow}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row" ng-if="character.classObj.spellcasting || character.raceObj.spellcasting">
                        <h4>Spellcasting</h4>
                        <table class="table table-condensed" ng-if="character.classObj.spellcasting">
                            <tr>
                                <th>Spellcasting Ability: </th>
                                <td>{{character.classObj.spellcasting.spellAbility}}</td>
                            </tr>
                            <tr>
                                <th>Spell Save DC: </th>
                                <td>{{character.classObj.spellcasting.spellSaveDC}}</td>
                            </tr>
                            <tr>
                                <th>Spell Attack Bonus: </th>
                                <td>{{character.classObj.spellcasting.spellAttkBonus}}</td>
                            </tr>
                            <tr ng-if="character.classObj.selectedCantrips">
                                <th>Cantrips: </th>
                                <td><i>{{character.classObj.selectedCantrips}}</i></td>
                            </tr>
                            <tr ng-repeat="spellByLevel in character.classObj.selectedSpellsByLevel">
                                <th>Level {{$index+1}} Spells: </th>
                                <td><i>{{spellByLevel}}</i></td>
                            </tr>
                        </table>
                        <table class="table table-condensed" ng-if="character.raceObj.spellcasting">
                            <tr>
                                <th>Spellcasting Ability: </th>
                                <td>{{character.raceObj.spellcasting.spellAbility}}</td>
                            </tr>
                            <tr>
                                <th>Spell Save DC: </th>
                                <td>{{character.raceObj.spellcasting.spellSaveDC}}</td>
                            </tr>
                            <tr>
                                <th>Spell Attack Bonus: </th>
                                <td>{{character.raceObj.spellcasting.spellAttkBonus}}</td>
                            </tr>
                            <tr ng-if="character.raceObj.cantrip">
                                <th>Cantrips: </th>
                                <td><i>{{character.raceObj.cantrip}}</i></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="features">
            <div class="row">
                <div class="col-md-4"> <!-- racial traits -->
                    <h4>Racial Traits</h4>
                    <dl>
                        <div ng-repeat="racialTrait in character.raceObj.racialTraits">
                            <dt>{{racialTrait.name}}</dt>
                            <dd ng-bind-html="racialTrait.benefit"></dd>
                        </div>
                    </dl>
                </div>
                <div class="col-md-4"> <!-- class features -->
                    <h4>Class Features</h4>
                    <dl>
                        <div ng-repeat="classFeature in character.classObj.classFeatures">
                            <dt>{{classFeature.name}}</dt>
                            <dd ng-bind-html="classFeature.benefit"></dd>
                        </div>
                    </dl>
                </div>
                <div class="col-md-4"> <!-- background feature -->
                    <h4>Background Feature</h4>
                    <dl>
                        <dt>{{character.background.trait_name}}</dt>
                        <dd>{{character.background.trait_desc}}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button ng-click="close()" class="btn btn-default">Close</button>
</div>
