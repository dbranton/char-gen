<!--<div id="info_contents">-->
<?php $is_mobile = ($mobile) ? 'true' : 'false'; ?>
<?php if(!isset($this->session->userdata['logged_in']['username'])): ?>
        <div class="alert alert-dismissable alert-warning">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>You are not currently logged in. You will not be able to save your character until you have logged in to your account.</p>
        </div>
<?php endif; ?>
<div class="form-horizontal" ng-cloak ng-app="generator" ng-controller="CharGen" ng-init="init()">
    <input id="isMobile" type="hidden" value="<?php echo $is_mobile; ?>" />
    <div class="col-md-6 well">
        <div id="characterGenerator">
            <fieldset>
                <legend>Background</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Name:</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" ng-model="character.name"="openRaceDialog()" placeholder="Pick a Name..." />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Race:</label>
                    <div class="col-sm-8">
                        <button type="button" class="btn btn-default" ng-click="openRaceDialog()">
                            <span ng-hide="character.raceObj">Select Race</span>
                            {{character.raceObj.subrace.name}}
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Background:</label>
                    <div class="col-sm-8">
                        <button type="button" ng-class="{true:'disabled', false:''}[!character.raceObj]" class="btn btn-default" ng-click="openBackgroundDialog()">
                            <span ng-hide="character.background">Select Background</span>
                            {{character.background.name}}
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Languages:</label>
                    <div class="col-sm-8 text">
                        <span ng-hide="character.languages">None</span>
                        {{character.languages}}
                        <div ng-hide="!character.background || character.numLanguages == 0">
                            <select ui-select2 ng-model="selectedLanguages" id="chosenLanguages" multiple max="{{select2Languages}}" style="width: 100%">
                                <option ng-repeat="language in availableLanguages">{{language}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Class</legend>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Class:</label>
                    <div class="col-sm-8">
                        <button type="button" class="btn btn-default" ng-click="openClassDialog()">
                            <span ng-hide="character.classObj">Select Class</span>
                            {{character.classObj.name}}
                        </button>
                    </div>
                </div>
                <div class="form-group"> <!--ng-show="character.classObj.subclasses"-->
                    <label class="col-sm-4 control-label">Subclass:</label>
                    <div class="col-sm-8">
                        <button type="button" ng-class="{true:'disabled', false:''}[!character.classObj]" class="btn btn-default" ng-click="openSubclassDialog()">
                            <span ng-hide="character.classObj.subclassObj">Select Subclass</span>
                            {{character.classObj.subclassObj.name}}
                        </button>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Proficiencies (+{{character.profBonus}})</legend>
                <div class="control-group-container">
                    <div class="form-group">
                        <div class="col-sm-4 control-label">
                            <label>Armor:</label>
                        </div>
                        <div class="col-sm-8 text">
                            <span ng-hide="character.armor">None</span>
                            {{character.armor}}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 control-label">
                            <label>Weapons:</label>
                        </div>
                        <div class="col-sm-8 text">
                            <span ng-hide="character.weapons">None</span>
                            {{character.weapons}}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 control-label">
                            <label>Skills:</label>
                        </div>
                        <div class="col-sm-8 text">
                            <span ng-hide="character.skills">None</span>
                            {{character.skills}}
                            <div ng-show="character.classObj"> <!-- can't hide select2s -->
                                <select ui-select2 id="chosenSkills" style="width:100%" placeholder="Select Skill(s)" multiple ng-model="selectedSkills"
                                    max="{{select2Skills}}">
                                    <!--ng-disabled="selectedSkills.length == character.classObj.num_skills"--> <!--select2Skills-->
                                    <!--<option></option>-->
                                    <option ng-repeat="skill in availableSkills" ng-disabled="skill.disabled">{{skill.name}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"> <!--ng-show="character.savingThrows" -->
                        <label class="col-sm-4 control-label">Saving Throws:</label>
                        <div class="col-sm-8 text"><span ng-hide="character.savingThrows">None</span>{{character.savingThrows}}</div>
                    </div>
                    <div class="form-group"> <!--ng-show="character.tools"-->
                        <label class="col-sm-4 control-label">Tools:</label>
                        <div class="col-sm-8 text"><span ng-hide="character.tools">None</span>{{character.tools}}</div>
                    </div>
                </div>
            </fieldset>
            <!--<div class="control-group">
                <label class="control-label"><button type="button" class="btn" ng-click="openAlignmentDialog()">Select Alignment</button></label>
                <div class="controls"><span id="selectedAlignment">{{character.alignment}}</span></div>            </div>-->
            <fieldset>
                <legend>Abilities</legend>
                <div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Ability</th>
                                <th>Base</th>
                                <th>Mod</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">{{character.ability.pointsLeft}} Points Left</td>
                            </tr>
                            <tr>
                                <td>Strength</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('str', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.str.score + (bonusAbility.str)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('str', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.str.mod}}</td>
                            </tr>
                            <tr>
                                <td>Dexterity</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.dex.score + (bonusAbility.dex)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.dex.mod}}</td>
                            </tr>
                            <tr>
                                <td>Constitution</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('con', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.con.score + (bonusAbility.con)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('con', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.con.mod}}</td>
                            </tr>
                            <tr>
                                <td>Intelligence</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('int', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.int.score + (bonusAbility.int)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('int', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.int.mod}}</td>
                            </tr>
                            <tr>
                                <td>Wisdom</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.wis.score + (bonusAbility.wis)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.wis.mod}}</td>
                            </tr>
                            <tr>
                                <td>Charisma</td>
                                <td>
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', -1)"><i class="fa fa-minus"></i></button>
                                    <input type="text" class="ability" readonly value="{{character.ability.cha.score + (bonusAbility.cha)}}" />
                                    <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', 1)"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>{{character.ability.cha.mod}}</td>
                            </tr>
                        </tbody>
                        <!--<tfoot>
                            <tr>
                                <td colspan="3"><button type="button" class="btn btn-default" ng-click="openAbilityScoreDialog()">Change Base Abilities</button></td>
                            </tr>
                        </tfoot>-->
                    </table>
                </div>
            </fieldset>
        </div>
        <!--<form id="charGenForm" name="charGenForm" method="POST" action="insert_character.php">-->
        <?php echo form_open('user/saveCharacter', array('id' => 'charGenForm', 'name' => 'charGenForm')); ?>
            <input type="hidden" id="inputName" name="charName" ng-model="character.name" required />
            <input type="hidden" id="inputRace" name="raceName" ng-model="character.raceObj.subrace.name" required />
            <input type="hidden" id="inputClass" name="className" ng-model="character.classObj.name" required />
            <input type="hidden" id="inputSubclass" name="subclassName" ng-model="character.classObj.subclassObj.name" required />
            <input type="hidden" id="inputBackground" name="backgroundName" ng-model="character.background.name" required />
            <input type="hidden" id="inputLanguage" name="languageName" ng-model="character.languages" />
            <input type="hidden" id="inputArmor" name="armorName" ng-model="character.armor" />
            <input type="hidden" id="inputWeapons" name="weaponsName" ng-model="character.weapons" />
            <input type="hidden" id="inputSkills" name="skillsName" ng-model="character.skills" />
            <input type="hidden" id="inputSavingThrows" name="savingThrowsName" ng-model="character.savingThrows" />
            <input type="hidden" id="inputTools" name="toolsName" ng-model="character.tools" />
            <input type="hidden" id="inputStr" name="strScoreName" ng-model="character.ability.str.score" />
            <input type="hidden" id="inputDex" name="dexScoreName" ng-model="character.ability.dex.score" />
            <input type="hidden" id="inputCon" name="conScoreName" ng-model="character.ability.con.score" />
            <input type="hidden" id="inputInt" name="intScoreName" ng-model="character.ability.int.score" />
            <input type="hidden" id="inputWis" name="wisScoreName" ng-model="character.ability.wis.score" />
            <input type="hidden" id="inputCha" name="chaScoreName" ng-model="character.ability.cha.score" />
            <input type="hidden" id="inputHP" name="hitPointsName" ng-model="character.hitPoints" />
            <input type="hidden" id="inputHitDice" name="hitDiceName" ng-model="character.classObj.hit_dice" />
            <input type="hidden" id="inputSpeed" name="speedName" ng-model="character.speed" />
            <input type="hidden" id="inputSize" name="sizeName" ng-model="character.size" />
            <input type="hidden" id="inputInit" name="initiativeName" ng-model="character.initiative" />
            <input type="hidden" id="inputArmor" name="armorName" ng-model="character.armorClass" />
            <input type="hidden" id="inputRacialTraits" name="racialTraitsName" ng-model="character.racialTraitIds" />
            <input type="hidden" id="inputClassFeatures" name="classFeaturesName" ng-model="character.classFeatureIds" />
            <!--<input type="text" id="inputAlignment" ng-model="character.alignment" />-->
            <button class="btn btn-primary" ng-click="saveCharacter()" ng-disabled="charGenForm.$invalid">Save Character</button>
        <?php echo form_close(); ?>
    </div> <!-- col-md-6 -->

    <div class="col-md-6 well" ng-hide="character.racialTraits.length == 0 && character.classFeatures.length == 0">
        <div ng-hide="character.racialTraits.length == 0" id="racialTraits">
            <h4>Racial Traits</h4>
            <ul>
                <li ng-repeat="racialTrait in character.racialTraits"><strong>{{racialTrait.name}}: </strong>
                    {{racialTrait.benefit}}</li>
            </ul>
        </div>
        <div ng-hide="character.classFeatures.length == 0" id="classFeatures">
            <h4>Class Features</h4>
            <ul>
                <li ng-repeat="classFeature in character.classFeatures"><strong>{{classFeature.name}}: </strong>
                    {{classFeature.benefit}}</li>
            </ul>
        </div>
    </div>
    <?php
        $this->load->view('dialog_race', $is_mobile);
        $this->load->view('dialog_background');
        $this->load->view('dialog_class');
        $this->load->view('dialog_ability');
    ?>
    <!--<div ng-include src="http://localhost/Fantasy_Fighter/char-gen/index.php/dialog/raceDialog"></div>-->
</div> <!-- ng-app -->