<!--<div id="info_contents">-->
<?php $is_mobile = ($mobile) ? 'true' : 'false'; $is_logged_in = ($logged_in) ? 'true' : 'false'; ?>

<div id="charGenApp" class="form-horizontal" ng-app="generator" ng-controller="CharGen" ng-init="init()">
    <span class="hide">{{isMobile=<?php echo $is_mobile; ?>}} {{isLoggedIn=<?php echo $is_logged_in; ?>}}</span>
    <span ng-hide="true">Loading Character Generator...</span>
    <div ng-cloak>
        <div ng-if="!isLoggedIn" class="alert alert-warning" ng-hide="successMessage">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>You are not currently logged in. If saved, your character will be stored on the browser instead of your account.</p>
        </div>
        <div id="successMsg" class="alert alert-success" ng-show="successMessage">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p>{{successMessage}}</p>
        </div>
        <div class="alert alert-danger" ng-show="errorMessage">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <div ng-bind-html="errorMessage"></div>
        </div>
        <div class="row panel panel-default">
            <div class="panel-heading">
                <div class="pull-left">
                    <button class="btn btn-primary" ng-click="openNewCharDialog()">Create New Character</button>
                </div>
                <div class="pull-right" ng-show="storedCharacter">
                    Last Stored Character: <button type="button" class="btn btn-default" ng-click="fillInCharacter()">{{storedCharacter.name}}</button>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="col-md-6" id="charGenCol1">
                    <fieldset>
                        <legend data-target="#backgroundPanel" data-toggle="collapse">Background</legend>
                        <div id="backgroundPanel" class="collapse in">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Level:</label>
                                <div class="col-sm-8 text">
                                    {{character.level}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Name:</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" ng-model="character.name"="openRaceDialog()" /> <!--placeholder="Pick a Name..."-->
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Race:</label>
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openRaceDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.raceObj.subrace.name" id="selectRace"
                                                style="width: 100%" ng-change="broadcastObj(raceData, character.raceObj.subrace.name, 'race')">
                                            <option ng-repeat="race in raceData" value="{{race.subrace.name}}">{{race.subrace.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Background:</label>
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openBackgroundDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.background.name" id="selectBackground"
                                                style="width: 100%" ng-change="broadcastObj(backgroundData, character.background.name, 'background')">
                                            <option ng-repeat="background in backgroundData" value="{{background.name}}">{{background.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="character.raceObj.cantrip || character.raceObj.cantrips">
                                <label class="col-sm-4 control-label">Bonus Cantrip:</label>
                                <div class="col-sm-8">
                                    <div class="text" ng-hide="character.raceObj.cantrips">{{character.raceObj.cantrip}}</div>
                                    <!-- still needs work -->
                                    <div class="input-group" ng-show="character.raceObj.cantrips">
                                        <label class="input-group-addon btn btn-default" ng-click="openBonusCantripDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.raceObj.cantrip" id="bonusCantrip" style="width: 100%"
                                                ng-change="broadcastNonObj(character.raceObj.cantrip, 'selectedBonusCantrip')">
                                            <option ng-repeat="cantrip in character.raceObj.cantrips">{{cantrip.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend data-target="#classPanel" data-toggle="collapse">Class</legend>
                        <div id="classPanel" class="collapse in">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Class:</label>
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openClassDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.classObj.name" id="selectClass"
                                                style="width: 100%" ng-change="broadcastObj(classData, character.classObj.name, 'clazz')"
                                                ng-disabled="!character.raceObj.name">
                                            <option ng-repeat="classObj in classData" value="{{classObj.name}}">{{classObj.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="character.classObj.subclasses.length > 0 || character.classObj.subclassObj">
                                <label class="col-sm-4 control-label">{{character.classObj.subclassName}}:</label>
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openSubclassDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.classObj.subclassObj.name"
                                                id="selectSubclass" style="width: 100%" ng-change="broadcastObj(character.classObj.subclasses, character.classObj.subclassObj.name, 'subclass')">
                                            <option ng-repeat="subclassObj in character.classObj.subclasses" value="{{subclassObj.name}}">{{subclassObj.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- for Class-based feature choices (ex: Fighting Style, Metamagic) -->
                            <div class="form-group" ng-repeat="selectedFeature in character.classObj.selectedFeatures">
                                <label class="col-sm-4 control-label">{{selectedFeature.label}}:</label> <!--Feature:</label>-->
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openFeatureDialog(selectedFeature, 'classArr')">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="selectedFeature.name" multiple max="{{selectedFeature.max}}"
                                                style="width: 100%" ng-change="broadcastArray(selectedFeature, 'selectedFeatures', 'classArr')">
                                            <option ng-repeat="feature in selectedFeature.choices" value="{{feature.name}}">{{feature.name}}</option> <!--character.classObj.featureChoices-->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- for subclass feature choices (ex: Totem Aspect, Combat Superiority, etc.) -->
                            <div class="form-group" ng-repeat="selectedFeature in character.classObj.subclassObj.selectedFeatures">
                                <label class="col-sm-4 control-label">{{selectedFeature.label}}:</label> <!--Feature:</label>-->
                                <div class="col-sm-8">
                                    <div ng-class="{'input-group':!isMobile}">
                                        <label ng-if="!isMobile" class="input-group-addon btn btn-default" ng-click="openFeatureDialog(selectedFeature, 'subclassArr')">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="selectedFeature.name" multiple max="{{selectedFeature.max}}"
                                                style="width: 100%" ng-change="broadcastArray(selectedFeature, 'selectedFeatures', 'subclassArr')">
                                            <option ng-repeat="feature in selectedFeature.choices" value="{{feature.name}}">{{feature.name}}</option> <!--character.classObj.featureChoices-->
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="character.classObj.cantrips">
                                <label class="col-sm-4 control-label">Cantrips:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <label class="input-group-addon btn btn-default" ng-click="openCantripDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.classObj.selectedCantrips" id="chosenCantrips" multiple max="{{character.classObj.numCantrips}}" style="width: 100%" ng-change="broadcastNonObj(character.classObj.selectedCantrips, 'selectedCantrips')">
                                            <option ng-repeat="cantrip in character.classObj.cantrips">{{cantrip.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- subclass cantrips -->
                            <div class="form-group" ng-show="character.classObj.subclassObj.cantrips">
                                <label class="col-sm-4 control-label">Cantrips:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <label class="input-group-addon btn btn-default" ng-click="openSubclassCantripDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>
                                        <select ui-select2 ng-model="character.classObj.selectedCantrips" id="chosenSubclassCantrips" multiple max="{{character.classObj.numCantrips}}" style="width: 100%" ng-change="broadcastNonObj(character.classObj.selectedCantrips, 'selectedCantrips')">
                                            <option ng-repeat="cantrip in character.classObj.subclassObj.cantrips">{{cantrip.name}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="character.classObj.spellcasting.numSpellsKnown">
                                <label class="col-sm-4 control-label">Spells:</label>
                                <div class="col-sm-8">
                                    <div> <!--class="input-group"-->
                                        <!--<label class="input-group-addon btn btn-default" ng-click="openSubclassCantripDialog()">
                                            <span class="fa fa-columns"></span>
                                        </label>-->
                                        <!--<select ui-select2 ng-model="character.classObj.selectedSpells" id="chosenSubclassCantrips" multiple max="{{character.classObj.numCantrips}}" style="width: 100%" ng-change="broadcastNonObj(character.classObj.selectedCantrips, 'selectedCantrips')">
                                            <option ng-repeat="cantrip in character.classObj.subclassObj.cantrips">{{cantrip.name}}</option>
                                        </select>-->
                                        <input type="hidden"
                                               select2-spellcasting="character.classObj.spellcasting"
                                               bonus="character.classObj.spellcasting.bonusSelectedSpells"
                                               ui-select2="select2Spells" ng-model="character.classObj.spellcasting.selectedSpells"
                                               max="{{character.classObj.spellcasting.numSpellsKnown}}" style="width:100%" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" ng-show="character.classObj.spellcasting.bonus.numSpellsKnown"> <!-- ng-if causes problems! -->
                                <label class="col-sm-4 control-label">Bonus Spells:</label>
                                <div class="col-sm-8">
                                    <div>
                                        <input type="hidden" select2-spellcasting="character.classObj.spellcasting.bonus"
                                               primary="character.classObj.spellcasting.selectedSpells"
                                               ui-select2="bonusSelect2Spells" ng-model="character.classObj.spellcasting.bonusSelectedSpells"
                                               max="{{character.classObj.spellcasting.bonus.numSpellsKnown}}" style="width:100%" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend data-target="#abilityPanel" data-toggle="collapse">Abilities</legend>
                        <div id="abilityPanel" class="collapse in">
                            <table class="table table-bordered table-striped table-condensed">
                                <thead>
                                <tr>
                                    <th>Ability</th>
                                    <th>Base</th>
                                    <th>Mod</th>
                                    <th><abbr title="Saving Throws">ST</abbr></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="4">{{character.ability.pointsLeft}} + {{character.ability.bonusPoints - character.ability.bonusPointsLeftArr.length}} Points Left</td>
                                </tr>
                                <tr>
                                    <td>Strength</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('str', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.str.score + (bonusAbility.str)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('str', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.str.mod >= 0">+</span>{{character.ability.str.mod}}</td>
                                    <td><span ng-show="character.ability.str.savingThrow >= 0">+</span>{{character.ability.str.savingThrow}}</td>
                                </tr>
                                <tr>
                                    <td>Dexterity</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.dex.score + (bonusAbility.dex)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.dex.mod >= 0">+</span>{{character.ability.dex.mod}}</td>
                                    <td><span ng-show="character.ability.dex.savingThrow >= 0">+</span>{{character.ability.dex.savingThrow}}</td>
                                </tr>
                                <tr>
                                    <td>Constitution</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('con', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.con.score + (bonusAbility.con)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('con', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.con.mod >= 0">+</span>{{character.ability.con.mod}}</td>
                                    <td><span ng-show="character.ability.con.savingThrow >= 0">+</span>{{character.ability.con.savingThrow}}</td>
                                </tr>
                                <tr>
                                    <td>Intelligence</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('int', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.int.score + (bonusAbility.int)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('int', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.int.mod >= 0">+</span>{{character.ability.int.mod}}</td>
                                    <td><span ng-show="character.ability.int.savingThrow >= 0">+</span>{{character.ability.int.savingThrow}}</td>
                                </tr>
                                <tr>
                                    <td>Wisdom</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.wis.score + (bonusAbility.wis)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.wis.mod >= 0">+</span>{{character.ability.wis.mod}}</td>
                                    <td><span ng-show="character.ability.wis.savingThrow >= 0">+</span>{{character.ability.wis.savingThrow}}</td>
                                </tr>
                                <tr>
                                    <td>Charisma</td>
                                    <td>
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', -1)"><i class="fa fa-minus"></i></button>
                                        <input type="text" class="ability" readonly value="{{character.ability.cha.score + (bonusAbility.cha)}}" />
                                        <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', 1)"><i class="fa fa-plus"></i></button>
                                    </td>
                                    <td><span ng-show="character.ability.cha.mod >= 0">+</span>{{character.ability.cha.mod}}</td>
                                    <td><span ng-show="character.ability.cha.savingThrow >= 0">+</span>{{character.ability.cha.savingThrow}}</td>
                                </tr>
                                </tbody>
                                <!--<tfoot>
                                    <tr>
                                        <td colspan="4">*Recommended</td>
                                    </tr>
                                </tfoot>-->
                            </table>
                        </div>
                    </fieldset>
                </div> <!-- col-md-6 -->

                <div class="col-md-6" id="charGenCol2">
                    <fieldset><!--{{character.proficientSkills}}-->
                        <legend data-target="#skillsPanel" data-toggle="collapse">Skills</legend>
                        <div class="collapse in" id="skillsPanel">
                            <label>Passive Wisdom (Perception):</label> {{character.passivePerception}}
                            <div ng-show="character.numSkillsLeft >= 0">{{character.numSkillsLeft}} Skills Left</div>
                            <skills></skills>
                            <div class="form-group" ng-show="character.classObj.expertise">
                                <label class="col-sm-4 control-label">{{character.classObj.expertise.label}}:</label>
                                <div class="col-sm-8 text">
                                    <select ui-select2 expertise ng-model="character.classObj.expertise.selectedExpertise" id="chosenExpertise" multiple max="{{character.classObj.expertise.numExpertise}}" style="width: 100%" ng-change="changeExpertise()">
                                        <option ng-repeat="expertise in character.classObj.expertise.list">{{expertise}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend data-target="#profPanel" data-toggle="collapse">Proficiencies (+{{character.profBonus}})</legend>
                        <div class="control-group-container collapse in" id="profPanel">
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
                                <label class="col-sm-4 control-label">Tools:</label>
                                <div class="col-sm-8 text"><span ng-hide="character.tools">None</span>{{character.tools}}</div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Languages:</label>
                                <div class="col-sm-8 text">
                                    <span ng-hide="character.languages">None</span>
                                    {{character.languages}}
                                    <div ng-hide="!character.background.name || character.numLanguages == 0">
                                        <select ui-select2 languages="character.numLanguages" ng-model="character.selectedLanguages" id="chosenLanguages" multiple max="{{select2Languages}}" bonus-languages="character.classObj.bonusLanguages" style="width: 100%">
                                            <option ng-repeat="language in availableLanguages">{{language}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div> <!-- col-md-6 -->
            </div> <!-- panel-body -->
            <div class="panel-footer">
                <form name="charGenForm" ng-submit="saveCharacter()" novalidate>
                    <input type="hidden" id="inputName" name="charName" ng-model="character.name" required />
                    <input type="hidden" id="inputRace" name="raceName" ng-model="character.raceObj.subrace.name" required />
                    <input type="hidden" id="inputClass" name="className" ng-model="character.classObj.name" required />
                    <input type="hidden" id="inputSubclass" name="subclassName" ng-model="character.classObj.subclassObj.name" ng-required="subclasses.length > 0" />
                    <!--<input type="hidden" id="inputFeature" name="featureName" ng-model="selectedFeature.name" ng-required="featureChoices.length > 0" />-->
                    <input type="hidden" id="inputBackground" name="backgroundName" ng-model="character.background.name" required />
                    <input type="hidden" id="inputLanguage" name="languageName" ng-model="numLanguagesLeft" is-empty />
                    <input type="hidden" id="inputSkills" name="skillsName" ng-model="character.numSkillsLeft" is-empty />
                    <input type="hidden" id="inputAbPts" name="abPtsLeftName" ng-model="character.ability.pointsLeft" is-empty />
                    <!--<input type="text" id="inputAlignment" ng-model="character.alignment" />-->
                    <!--<button class="btn btn-primary" ng-click="saveCharacter()" ng-disabled="charGenForm.$invalid">Save Character</button>-->
                    <button type="submit" class="btn btn-primary">Save Character</button> <!--ng-disabled="charGenForm.$invalid"-->
                    <button type="button" class="btn btn-default" ng-click="openSummary()" ng-if="!isMobile">Open Summary</button>
                    <div class="error-container" ng-show="validating && charGenForm.$invalid">
                        <div><small class="text-danger" ng-show="charGenForm.charName.$error.required">Please name your character</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.raceName.$error.required">Please choose a race</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.backgroundName.$error.required">Please choose a background</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.languageName.$error.notEmpty">You need to choose your languages</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.className.$error.required">Please choose a class</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.subclassName.$error.required">Please choose a subclass</small></div>
                        <!--<div><small class="text-danger" ng-show="charGenForm.featureName.$error.required">You need to choose a feature for your class</small></div>-->
                        <div><small class="text-danger" ng-show="charGenForm.skillsName.$error.notEmpty">Please select your skills</small></div>
                        <div><small class="text-danger" ng-show="charGenForm.abPtsLeftName.$error.notEmpty">Please assign your ability scores</small></div>
                    </div>
                </form>
            </div>
        </div> <!-- panel -->
    </div>
</div> <!-- ng-app -->