<!--<div id="info_contents">-->
<?php $is_mobile = ($mobile) ? 'true' : 'false'; ?>
<div class="form-horizontal" ng-cloak ng-app="generator" ng-controller="CharGen" ng-init="init()">
    <?php if(!isset($this->session->userdata['logged_in']['username'])): ?>
    <div class="alert alert-warning">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>You are not currently logged in. You will not be able to save your character until you have logged in to your account.</p>
    </div>
    <?php endif; ?>
    <div class="alert alert-success" ng-show="successMessage">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>{{successMessage}}</p>
    </div>
    <div class="alert alert-danger" ng-show="errorMessage">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <p>{{errorMessage}}</p>
    </div>
    <input id="isMobile" type="hidden" value="<?php echo $is_mobile; ?>" />
    <div class="col-md-6 well">
        <div id="characterGenerator">
            <fieldset>
                <legend><a href="#backgroundPanel" data-toggle="collapse">Background</a></legend>
                <div id="backgroundPanel" class="collapse in">
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
                                <select ui-select2 ng-model="character.selectedLanguages" id="chosenLanguages" multiple max="{{select2Languages}}" style="width: 100%">
                                    <option ng-repeat="language in availableLanguages">{{language}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend><a href="#classPanel" data-toggle="collapse">Class</a></legend>
                <div id="classPanel" class="collapse in">
                    <div class="form-group">
                        <label class="col-sm-4 control-label">Class:</label>
                        <div class="col-sm-8">
                            <button type="button" class="btn btn-default" ng-click="openClassDialog()">
                                <span ng-hide="character.classObj">Select Class</span>
                                {{character.classObj.name}}
                            </button>
                        </div>
                    </div>
                    <div class="form-group" ng-show="subclasses.length > 0">
                        <label class="col-sm-4 control-label">Subclass:</label>
                        <div class="col-sm-8">
                            <button type="button" class="btn btn-default" ng-click="openSubclassDialog()"> <!--ng-class="{true:'disabled', false:''}[!character.classObj]"-->
                                <span ng-hide="character.classObj.subclassObj">Select Subclass</span>
                                {{character.classObj.subclassObj.name}}
                            </button>
                        </div>
                    </div>
                    <div class="form-group" ng-show="featureChoices.length > 0">
                        <label class="col-sm-4 control-label">Feature:</label>
                        <div class="col-sm-8">
                            <button type="button" class="btn btn-default" ng-click="openFeatureDialog()">
                                <span ng-hide="selectedFeature">Select Feature</span>
                                {{selectedFeature.name}}
                            </button>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset>
                <legend><a href="#skillsPanel" data-toggle="collapse">Skills</a></legend>
                <div class="collapse in" id="skillsPanel">
                    <label>Passive Wisdom (Perception):</label> {{character.passivePerception}}
                    <div ng-show="character.numSkillsLeft >= 0">{{character.numSkillsLeft}} Skills Left</div>
                    <skills></skills>
                </div>
            </fieldset>
            <fieldset>
                <legend><a href="#profPanel" data-toggle="collapse">Proficiencies (+{{character.profBonus}})</a></legend>
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
                </div>
            </fieldset>
            <fieldset>
                <legend><a href="#abilityPanel" data-toggle="collapse">Abilities</a></legend>
                <div id="abilityPanel" class="collapse in">
                    <table class="table table-bordered table-striped">
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
                                <td colspan="4">{{character.ability.pointsLeft}} Points Left</td>
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
                                <td colspan="3"><button type="button" class="btn btn-default" ng-click="openAbilityScoreDialog()">Change Base Abilities</button></td>
                            </tr>
                        </tfoot>-->
                    </table>
                </div>
            </fieldset>
        </div>
        <!--<form id="charGenForm" name="charGenForm" method="POST" action="insert_character.php">-->
        <form name="charGenForm" ng-submit="saveCharacter()" novalidate>
            <input type="hidden" id="inputName" name="charName" ng-model="character.name" required />
            <input type="hidden" id="inputRace" name="raceName" ng-model="character.raceObj.subrace.name" required />
            <input type="hidden" id="inputClass" name="className" ng-model="character.classObj.name" required />
            <input type="hidden" id="inputSubclass" name="subclassName" ng-model="character.classObj.subclassObj.name" ng-required="subclasses.length > 0" />
            <input type="hidden" id="inputFeature" name="featureName" ng-model="selectedFeature.name" ng-required="featureChoices.length > 0" />
            <input type="hidden" id="inputBackground" name="backgroundName" ng-model="character.background.name" required />
            <input type="hidden" id="inputLanguage" name="languageName" ng-model="numLanguagesLeft" is-empty />
            <input type="hidden" id="inputSkills" name="skillsName" ng-model="character.numSkillsLeft" is-empty />
            <input type="hidden" id="inputAbPts" name="abPtsLeftName" ng-model="character.ability.pointsLeft" is-empty />
            <!--<input type="text" id="inputAlignment" ng-model="character.alignment" />-->
            <!--<button class="btn btn-primary" ng-click="saveCharacter()" ng-disabled="charGenForm.$invalid">Save Character</button>-->
            <button type="submit" class="btn btn-primary">Save Character</button> <!--ng-disabled="charGenForm.$invalid"-->
            <div class="error-container" ng-show="validating && charGenForm.$invalid">
                <div><small class="text-danger" ng-show="charGenForm.charName.$error.required">Please name your character</small></div>
                <div><small class="text-danger" ng-show="charGenForm.raceName.$error.required">Please choose a race</small></div>
                <div><small class="text-danger" ng-show="charGenForm.backgroundName.$error.required">Please choose a background</small></div>
                <div><small class="text-danger" ng-show="charGenForm.languageName.$error.notEmpty">You need to choose your languages</small></div>
                <div><small class="text-danger" ng-show="charGenForm.className.$error.required">Please choose a class</small></div>
                <div><small class="text-danger" ng-show="charGenForm.subclassName.$error.required">Please choose a subclass</small></div>
                <div><small class="text-danger" ng-show="charGenForm.featureName.$error.required">You need to choose a feature for your class</small></div>
                <div><small class="text-danger" ng-show="charGenForm.skillsName.$error.notEmpty">Please select your skills</small></div>
                <div><small class="text-danger" ng-show="charGenForm.abPtsLeftName.$error.notEmpty">Please assign your ability scores</small></div>
            </div>
        </form>
    </div> <!-- col-md-6 -->

    <div class="col-md-6 well" ng-hide="character.racialTraits.length == 0 && character.classFeatures.length == 0">
        <div ng-show="character.classObj">
            <div class="col-md-4 alert alert-danger">
                <div class="text-center">Hit Points</div>
                <div class="text-center">{{character.hitPoints}}</div>
            </div>
            <div class="col-md-4 alert alert-success">
                <div class="text-center">Initiative</div>
                <div class="text-center">{{character.initiative}}</div>
            </div>
            <div class="col-md-4 alert alert-info">
                <div class="text-center">Armor Class</div>
                <div class="text-center">{{character.armorClass}}</div>
            </div>
        </div>
        <div ng-hide="character.racialTraits.length == 0" id="racialTraits">
            <h4>Racial Traits</h4>
            <dl>
                <div ng-repeat="racialTrait in character.racialTraits">
                    <dt>{{racialTrait.name}}</dt>
                    <dd ng-bind-html="racialTrait.benefit"></dd>
                </div>
            </dl>
        </div>
        <div ng-hide="character.classFeatures.length == 0" id="classFeatures">
            <h4>Class Features</h4>
            <dl>
                <div ng-repeat="classFeature in character.classFeatures">
                    <dt>{{classFeature.name}}</dt>
                    <dd ng-bind-html="classFeature.benefit"></dd>
                </div>
            </dl>
        </div>
    </div>
</div> <!-- ng-app -->