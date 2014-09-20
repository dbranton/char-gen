'use strict';
var charGenService = angular.module('charGenService', ['LocalStorageModule']);
charGenService.factory('charGenFactory', function($http, $timeout, localStorageService) {
    var ABILITIES = ['str', 'dex', 'con', 'int', 'wis', 'cha'];
    var MIN_ABILITY = 8;
    var MAX_ABILITY = 15;
    //var ABILITY_MAPPER = {'str':'Strength', 'dex':'Dexterity', 'con':'Constitution', 'int':'Intelligence', 'wis':'Wisdom', 'cha':'Charisma'};

    Array.prototype.getIndexBy = function (name, value) {
        for (var i = 0; i < this.length; i++) {
            if (this[i][name] == value) {
                return i;
            }
        }
        return -1;
    };
    Array.prototype.spliceByName = function(name, val) {
        this.splice(this.indexOf(name), val);
    };
    Array.prototype.diff = function(a) {
        return this.filter(function(i) {return a.indexOf(i) < 0;});
    };

    function Character() {
        this.name = null,
        this.raceObj = {};
        this.classObj = {}; // contains subclasses property
        this.background = null;   // & background.skills
        //this.selectedSkills = [], // now in classObj
        this.selectedLanguages = null,
        //this.proficientSkills = '', // same as selectedSkills except in string form
        this.skills = [{"name":"Acrobatics","ability":"Dex","val":0,"proficient":false,"disabled":true},{"name":"Animal Handling","ability":"Wis","val":0,"proficient":false,"disabled":true},{"name":"Arcana","ability":"Int","val":0,"proficient":false,"disabled":true},{"name":"Athletics","ability":"Str","val":0,"proficient":false,"disabled":true},{"name":"Deception","ability":"Cha","val":0,"proficient":false,"disabled":true},{"name":"History","ability":"Int","val":0,"proficient":false,"disabled":true},{"name":"Insight","ability":"Wis","val":0,"proficient":false,"disabled":true},{"name":"Intimidation","ability":"Cha","val":0,"proficient":false,"disabled":true},{"name":"Investigation","ability":"Int","val":0,"proficient":false,"disabled":true},{"name":"Medicine","ability":"Wis","val":0,"proficient":false,"disabled":true},{"name":"Nature","ability":"Int","val":0,"proficient":false,"disabled":true},{"name":"Perception","ability":"Wis","val":0,"proficient":false,"disabled":true},{"name":"Performance","ability":"Cha","val":0,"proficient":false,"disabled":true},{"name":"Persuasion","ability":"Cha","val":0,"proficient":false,"disabled":true},{"name":"Religion","ability":"Int","val":0,"proficient":false,"disabled":true},{"name":"Sleight of Hand","ability":"Dex","val":0,"proficient":false,"disabled":true},{"name":"Stealth","ability":"Dex","val":0,"proficient":false,"disabled":true},{"name":"Survival","ability":"Wis","val":0,"proficient":false,"disabled":true}];
        this.languages = null;
        this.numLanguages = 0;
        //alignment = null;
        this.armorClass = null;
        this.attackMod = null;
        this.savingThrows = null;
        this.hitPoints = null;
        //this.classFeatures = [];  // now located in classObj
        //this.featureIds = [];
        //this.racialTraits = [];   // now located in raceObj
        //this.racialTraitIds = [];
        this.speed = null;
        this.initiative = null;
        this.armor = null;
        this.weapons = null;
        this.tools = null;
        this.size = null;
        this.profBonus = 0;
        this.passivePerception = 10;
    }
    var enabledSkills;
    Character.prototype.updateSkillProficiency = function(skillName, isAdded, disabled) {   // if isAdded is false, then remove skill
        var that = this;
        if (angular.isArray(this.skills)) {
            if (skillName) {
                angular.forEach(this.skills, function(skill, i) {
                    if (skillName.indexOf(skill.name) !== -1) {
                        if (disabled) {    // skills[i].proficient will already be updated when user checks/unchecks a skill
                            that.skills[i].proficient = isAdded;
                            that.skills[i].disabled = true;
                        }
                        that.updateSkillScore(skill.name);
                        if (isAdded) {  // add skill
                            if (that.classObj.selectedSkills && that.classObj.selectedSkills.indexOf(skill.name) === -1) {
                                that.classObj.selectedSkills.push(skill.name);
                                that.classObj.selectedSkills.sort(); // no need to sort a spliced array
                                that.numSkillsLeft--;
                            }
                            if (that.numSkillsLeft === 0) {
                                enabledSkills = [];
                                that.skills.forEach(function(skill) {
                                    if (skill.disabled === false) {
                                        enabledSkills.push(skill.name);   // save currently enabled skills for later
                                    }
                                });
                                that.enableSkills(false);  // then disable all skills except the checked ones
                            }
                        } else {    // remove skill
                            that.classObj.selectedSkills.spliceByName(skill.name, 1); // remove skill
                            // remove skill from expertise if it exists
                            if (that.classObj.expertise && that.classObj.expertise.selectedExpertise &&
                                    that.classObj.expertise.selectedExpertise.indexOf(skill.name) !== -1) {
                                that.classObj.expertise.selectedExpertise.spliceByName(skill.name, 1);
                                if (that.classObj.expertise.type === 'expertise') {
                                    that.numSkillsLeft++; // +4 to +0: 'expertise' only
                                }
                            } else {
                                that.numSkillsLeft++;
                            }
                            if (that.numSkillsLeft === 1) {
                                that.enableSkills(enabledSkills);    // reenable proficient skills that were disabled
                            }
                        }
                    }
                });
            } else {    // wipe proficiencies for all skills
                this.skills.forEach(function(skill, i) {
                    that.skills[i].proficient = false;
                    that.updateSkillScore(skill.name);
                });
            }
        }
        if (this.classObj.num_skills && disabled) { // disable is true if background was selected, or selected High Elf (perception)
            this.numSkillsLeft = parseInt(this.classObj.num_skills);    // resets numSkills since some skills will be automatically selected
        }
        this.getProficientSkills();
    };
    Character.prototype.enableSkills = function(classSkills, backgroundSkills) {   // skills is an array, exceptions is comma separated string
        var that = this;
        if (angular.isArray(classSkills) && this.numSkillsLeft > 0) {
            enabledSkills = [];
            angular.forEach(classSkills, function(skill, i) {
                for (var index=0; index<that.skills.length; index++) {
                    if (that.skills[index].name === skill) {
                        break;
                    }
                }
                if (!backgroundSkills || backgroundSkills.indexOf(classSkills[i]) === -1) {
                    that.skills[index].disabled = false;
                    enabledSkills.push(that.skills[index].name); // ADDED 9/8/2014 in case something broke
                } else {    // class skill shares with background skill, so disable it
                    that.skills[index].disabled = true;
                }
            })
        } else {    // disable everything that isn't checked
            angular.forEach(that.skills, function(skill, i) {
                if (that.skills[i].proficient === false) {
                    that.skills[i].disabled = true;
                }
            });
        }
    };
    Character.prototype.handleSkills = function() {
        var classSkills = this.classObj.avail_skills ? this.classObj.avail_skills.split(', ') : [];    // disable everything if empty array
        this.updateSkillProficiency(false); // wipe skill proficiencies
        this.enableSkills(false);   // disable all skills
        if (this.background) {
            this.updateSkillProficiency(this.background.skills, true, true);
            this.enableSkills(classSkills, this.background.skills);
        } else {
            this.enableSkills(classSkills);
        }
    };
    Character.prototype.updateSkillScore = function(skillName, isAdded) {    // if no parameter, update all skills
        var abilityMapper = {
            Str: 'str', Dex: 'dex', Con: 'con', Int: 'int', Wis: 'wis', Cha: 'cha'
        };
        var profBonus = 0;
        var that = this;
        var selectedExpertise = this.classObj.expertise ? this.classObj.expertise.selectedExpertise : null;    // array/null
        angular.forEach(this.skills, function(skill, i) {
            if (!skillName || skill.name === skillName) {   // skillName might be an array of skills
                if (angular.isDefined(isAdded)) {   // means that it is an expertise skill
                    if (selectedExpertise.indexOf(skill.name) !== -1) { // && !skill.proficient
                        if (!skill.proficient) {    // level 1: +0 to +4
                            skill.proficient = true;    // in case expertise skill is not proficient
                            that.classObj.selectedSkills.push(skill.name);
                            that.classObj.selectedSkills.sort();
                        } else if (skillName === skill.name && !skill.disabled &&
                                that.classObj.expertise.type === 'selected_expertise') {   // level 1: +2 to +4
                            //selectedExpertise[selectedExpertise.length-1]
                            that.numSkillsLeft++;
                            that.enableSkills(enabledSkills);
                        }
                        if (that.classObj.expertise.type === 'selected_expertise') {
                            skill.disabled = true;
                        }
                    } else if (that.classObj.expertise.type === 'selected_expertise' && // removing expertise skill
                            selectedExpertise.indexOf(skill.name) === -1 &&
                                that.classObj.expertise.list.indexOf(skill.name) !== -1 && skill.proficient) {
                        skill.proficient = false;
                        if (that.numSkillsLeft > 0 && that.classObj.avail_skills.indexOf(skill.name) !== -1) {
                            skill.disabled = false; // only enable skill if it belongs on the class skill list and numSkillsLeft > 0
                        }
                        that.classObj.selectedSkills.spliceByName(skill.name, 1);
                        //that.enableSkills(enabledSkills);
                    }
                }
                profBonus = skill.proficient ? that.profBonus : 0;
                that.skills[i].val = profBonus + that.ability[abilityMapper[skill.ability]].mod;
                if (selectedExpertise && selectedExpertise.indexOf(skill.name) !== -1) {
                    that.skills[i].val += profBonus;
                    //that.enableSkills(enabledSkills);
                }
                if (skill.name === "Perception") {
                    that.passivePerception = 10 + that.skills[i].val;   // handle passive perception
                }
            }
        });
    };
    Character.prototype.getProficientSkills = function() {
        var profSkillsArray = [];
        this.skills.forEach(function(skill) {
            if (skill.proficient) {
                profSkillsArray.push(skill.name);
            }
        });
        this.proficientSkills = profSkillsArray.join(', ');
    };
    Character.prototype.calculateSavingThrows = function(ability) {
        var bonus = 0;
        if (this.savingThrows && this.savingThrows.indexOf(ability) !== -1) {
            bonus = this.profBonus;
        }
        return this.ability[ability].mod + bonus;
    };
    Character.prototype.calculateModifiers = function(ability) {
        if (ability) {
            this.ability[ability].mod = Math.floor((this.ability[ability].score - 10) / 2);
            this.ability[ability].savingThrow = this.calculateSavingThrows(ability);
        } else {    // apply to all abilities
            this.ability.str.mod = returnModifier(this.ability.str.score);
            this.ability.dex.mod = returnModifier(this.ability.dex.score);
            this.ability.con.mod = returnModifier(this.ability.con.score);
            this.ability.int.mod = returnModifier(this.ability.int.score);
            this.ability.wis.mod = returnModifier(this.ability.wis.score);
            this.ability.cha.mod = returnModifier(this.ability.cha.score);
            var that = this;
            ABILITIES.forEach(function(ability) {
                that.ability[ability].savingThrow = that.calculateSavingThrows(ability);
            });
        }
        this.updateSkillScore();    // called to update because modifier changes might affect scores
        // update spellcasting stats if any
        if (this.raceObj.spellcasting && (!ability || this.raceObj.spellcasting.spellAbility === ability)) {
            this.handleSpellcasting('raceObj');
        };
        if (this.classObj.spellcasting && (!ability || this.classObj.spellcasting.spellAbility === ability)) {
            this.handleSpellcasting();
        }
        // handle dexterity-specific stats
        if (!ability || ability === 'dex') {
            this.initiative = this.ability.dex.mod;
            this.armorClass = 10 + this.ability.dex.mod;
        }
        if (!ability || ability === 'con') {
            this.handleHitPoints();
        }
        if (this.classObj.bonusArmorAbility) {
            this.armorClass = 10 + this.ability.dex.mod + this.ability[this.classObj.bonusArmorAbility].mod;
        }

        function returnModifier(score) {
            return Math.floor((score - 10) / 2);
        }
    };
    Character.prototype.modifyAbilityScore = function(ability, value) {
        var diff = this.ability[ability].max - MAX_ABILITY,
            currValue = this.ability[ability].score, abilityCost,
            pointsLeft = this.ability.pointsLeft,
            currValueMinusDiff = currValue - diff;
        if (pointsLeft > 0 && (value > 0 && currValue < this.ability[ability].max) || (value < 0 && currValue > this.ability[ability].min)) {
            if ((value > 0 && currValueMinusDiff >= 13 && pointsLeft <= 1) ||
                (value > 0 && currValueMinusDiff >= 15 && pointsLeft <= 2)) {
                // do nothing
            } else {
                if (value > 0) {    // value == 1
                    if (currValueMinusDiff >= 15) {
                        abilityCost = 3;
                    } else if (currValueMinusDiff >= 13) {
                        abilityCost = 2;
                    } else if (currValueMinusDiff < 13) {
                        abilityCost = 1;
                    }
                    this.ability.pointsLeft -= abilityCost;
                } else {    // value == -1
                    if (pointsLeft === 0 && this.ability.bonusPointsLeftArr.indexOf(ability) !== -1) {
                        this.ability.bonusPointsLeftArr.splice(this.ability.bonusPointsLeftArr.indexOf(ability), 1);    // remove from array
                    } else {
                        if (currValueMinusDiff > 15) {
                            abilityCost = 3;
                        } else if (currValueMinusDiff > 13) {
                            abilityCost = 2;
                        } else if (currValueMinusDiff <= 13) {
                            abilityCost = 1;
                        }
                        this.ability.pointsLeft += abilityCost;
                    }
                }
                updateScore(this, ability, value);
            }
        } else if (pointsLeft === 0) {
            if (this.ability[ability].score < 20 && this.ability.bonusPoints - this.ability.bonusPointsLeftArr.length > 0) {
                updateScore(this, ability, value); // increment ability and push to array to store where the bonus point went
                //this.ability.bonusPointsLeftArr.push({"ability": ability, "score": this.ability[ability].score});
                this.ability.bonusPointsLeftArr.push(ability);  // ex: ['str', 'str']
            }
        }
        function updateScore(character, ability, value) {
            character.ability[ability].score += value;
            character.ability[ability].mod = Math.floor((character.ability[ability].score-10)/2);
            character.calculateModifiers(ability);
        }
    };
    Character.prototype.resetRacialBonuses = function() {
        var diff = 0;
        var that = this;
        angular.forEach(ABILITIES, function(ability) {
            diff = that.ability[ability].max - MAX_ABILITY; // ex: 0, 1, or 2
            if (diff > 0) {
                that.ability[ability].score -= diff;
                that.calculateModifiers(ability);
                that.ability[ability].max = MAX_ABILITY;
                that.ability[ability].min = MIN_ABILITY;
            }
        });
    };
    Character.prototype.handleSpellcasting = function(objType) {    // classObj or raceObj
        var type = objType ? objType : 'classObj';
        this[type].spellcasting.spellSaveDC = 8 + this.profBonus + this.ability[this[type].spellcasting.spellAbility].mod;
        this[type].spellcasting.spellAttkBonus = this.profBonus + this.ability[this[type].spellcasting.spellAbility].mod;
    };
    Character.prototype.handleFeatureBonuses = function(featureBonus) {
        var bonusArray = [], characterArray = [], that = this, expertiseArr;
        this.armor = this.classObj.armor_prof || null;
        this.weapons = this.classObj.weapon_prof || null;
        for (var bonusProp in featureBonus) {
            var propArray = bonusProp.split(' : ');  // usually results in one item
            propArray.forEach(function(prop, ind) {
                featureBonus[prop] = featureBonus[bonusProp].split(' : ')[ind];
                if (prop === 'baseSpeed') {
                    that.speed = parseInt(featureBonus[prop]);
                } else if (that[prop] !== null && (prop === 'initiative' || prop === 'armorClass' || prop === 'attackMod' ||
                    prop === 'speed' || prop === 'numLanguages')) {
                    that[prop] += parseInt(featureBonus[prop]); // character prop needs to exist to add
                } else if (that[prop] !== null && prop === 'hitPoints') {   // assume hitPoint bonuses apply every level
                    that[prop] += (that.level * (parseInt(featureBonus[prop])));    // multiply hitPoint bonus by level
                } else if (ABILITIES.indexOf(prop) !== -1) {    // ex: 'str', 'dex', etc.
                    //bonusArray = featureBonus[bonusProp].split(', ');   // primarily for human "1, 1, 1, 1, 1, 1" becomes an array
                    that.ability[prop].score += parseInt(featureBonus[prop]);
                    that.ability[prop].max += parseInt(featureBonus[prop]);
                    that.ability[prop].min += parseInt(featureBonus[prop]);
                    that.calculateModifiers(prop);
                } else if (prop === 'armor' || prop === 'weapons') {
                    var allResults = '';
                    if (prop === 'armor') {
                        allResults = 'All Armor';
                    } else if (prop === 'weapons') {
                        allResults = 'Martial Weapons';
                    }
                    if (that[prop] && that[prop] !== 'None') {
                        if (that[prop].indexOf(allResults) === -1) {
                            bonusArray = featureBonus[prop].split(', ');    // ex: ['longsword', 'shortsword', 'shortbow', 'longbow']
                            characterArray = that[prop].split(', ');   // ex: ['Simple weapons', 'martial weapons']
                            bonusArray.forEach(function(weapon) {
                                if (that[prop].indexOf(weapon) === -1) {
                                    characterArray.push(weapon);
                                }
                            });
                            that[prop] = characterArray.join(', ');
                        }
                    } else {
                        that[prop] = featureBonus[prop];
                    }
                } else if (prop === 'tools' || prop === 'savingThrows') {
                    if (that[prop] && that[prop].indexOf(featureBonus[prop]) === -1) {
                        that[prop] += ', ' + featureBonus[prop];
                    } else if (!that[prop]) {
                        that[prop] = featureBonus[prop];
                    }   // else do nothing
                    that[prop] = that[prop].split(', ');
                    that[prop].sort();
                    that[prop] = that[prop].join(', ');
                    if (prop === 'savingThrows') {
                        that.calculateModifiers();
                    }
                } else if (prop === 'defense') {
                    that.classObj.bonusArmorAbility = featureBonus[prop];   // ex: wis
                    that.armorClass += that.ability[featureBonus[prop]].mod;
                } else if (prop === 'skills') {
                    that.updateSkillProficiency(featureBonus[prop], true, true);
                } else if (prop === 'cantrips') {
                    that.classObj.numCantrips = parseInt(featureBonus[prop]);
                } else if (prop === 'spells_known') {   // ex: 28, 1, 2: 28 is class_id, 1 is max spell level and 2 is number of spells known
                    bonusArray = featureBonus[prop].split(', ');
                    that.classObj.spellcasting.class_id = bonusArray[0];
                    that.classObj.spellcasting.max_spell_level = bonusArray[1];
                    that.classObj.spellcasting.numSpellsKnown = parseInt(bonusArray[2]);
                    if (bonusArray[3] && bonusArray[4]) {
                        that.classObj.spellcasting.restricted_schools = [bonusArray[3], bonusArray[4]];  // ex: Abjuration, Evocation
                    }
                } else if (prop === 'bonus_spells_known') {   // ex: 4, 2, 2: 4 is class_id, 2 is max spell level and 2 is number of spells known
                    bonusArray = featureBonus[prop].split(', ');
                    that.classObj.spellcasting.bonus = {    // expects spellcasting to exist
                        class_id: bonusArray[0],
                        max_spell_level: bonusArray[1],
                        numSpellsKnown: parseInt(bonusArray[2])
                    };
                } else if (prop === 'bonus_race_cantrip') {  // assume this always comes before spellcasting if it exists
                    bonusArray = featureBonus[prop].split(', ');    // 'Thaumaturgy, cha' becomes an ['Thaumaturgy', 'cha']
                    that.raceObj.spellcasting = {};
                    that.raceObj.spellcasting.spellAbility = bonusArray[1];
                    that.handleSpellcasting('raceObj');
                    that.raceObj.cantrip = bonusArray[0];
                } else if (prop === 'bonus_race_cantrip_choice') {
                    that.raceObj.spellcasting = {};
                    that.raceObj.spellcasting.spellAbility = featureBonus[prop];    // ex: 'int'
                    that.handleSpellcasting('raceObj');
                } else if (prop === 'spellcasting') {
                    that.classObj.spellcasting = that.classObj.spellcasting || {};
                    that.classObj.spellcasting.spellAbility = featureBonus[prop];    //ABILITY_MAPPER[featureBonus[prop]];
                    if (that.classObj.selectedClassCantrips) {  // incase you switch from a race with bonus cantrip to one without one
                        that.classObj.selectedCantrips = angular.copy(that.classObj.selectedClassCantrips)
                    }
                    if (that.raceObj.spellcasting &&
                            that.raceObj.spellcasting.spellAbility === that.classObj.spellcasting.spellAbility) {  // the race's bonus cantrip spell ability is the same as the spellcasting classes' spell ability
                        that.classObj.selectedClassCantrips = that.classObj.selectedClassCantrips || [];
                        //that.classObj.selectedCantrips.push(that.raceObj.cantrip);
                        that.classObj.selectedCantrips = that.classObj.selectedClassCantrips.concat(that.raceObj.cantrip);  // uses concat so selectedClassCantrips does not change
                        that.classObj.selectedCantrips.sort();
                        that.raceObj.spellcasting = null;
                    }
                    that.handleSpellcasting();
                } else if (prop === 'expertise') {
                    expertiseArr = that.classObj.selectedSkills; //angular.copy(that.selectedSkills);
                    bonusArray = featureBonus[prop].split(', ');    // ex: "2, Thieves' Tools"
                    if (expertiseArr.indexOf(bonusArray[1]) === -1) {
                        expertiseArr.push(bonusArray[1]);
                    }
                    that.classObj.expertise.type = prop;
                    that.classObj.expertise.numExpertise = bonusArray[0]; // ex: ['Acrobatics', ... , 'Thieves' Tools']
                    that.classObj.expertise.list = expertiseArr;
                } else if (prop === 'selected_expertise') {
                    bonusArray = featureBonus[prop].split(', ');    // ex: [2, Arcana, History, Nature, Religion]
                    expertiseArr = bonusArray.slice(1); // ex: [Arcana, History, Nature, Religion]
                    that.classObj.expertise.type = prop;
                    that.classObj.expertise.numExpertise = bonusArray[0];
                    that.classObj.expertise.list = expertiseArr;
                } else if (prop === 'additional_feature') {
                    angular.forEach(that.classObj.selectedFeatures, function(feature) {
                        if (feature.label === featureBonus[prop]) {
                            feature.max = 2;    // assume feature_choices is 1
                        }
                    });
                } else if (prop === 'bonus_class_cantrip_choice') { // ex: Circle of the Moon get bonus druid cantrip
                    that.classObj.numCantrips += parseInt(featureBonus[prop]);    // usually 1
                } else if (prop === 'bonus_class_cantrip') {    // for Light Domain bonus cantrip (light)
                    that.classObj.selectedCantrips = that.classObj.selectedCantrips || [];
                    if (that.classObj.selectedCantrips.indexOf(featureBonus[prop]) === -1) {
                        $timeout(function() {
                            that.classObj.selectedCantrips.push(featureBonus[prop]);    // causes an empty result to show in the cantrips list
                            that.classObj.selectedCantrips.sort();
                        }); // needs to come after render occurs in select2.js
                    }
                    that.classObj.numCantrips++;
                    // Doesn't work for Arcane Trickster, since cantrips is in classObj.subclassObj
                    /*angular.forEach(that.classObj.cantrips, function(cantrip, idx, cantrips) {
                        if (cantrip.name === featureBonus[prop]) {
                            cantrips.splice(idx, 1);    // potentially dangerous
                        }
                    });*/
                }
                /*else if (prop === 'languages') {    // taken care of by determineRace
                    that.defaultLanguages = featureBonus[prop]; // string
                    that.languages = featureBonus[prop].split(', ');
                    if (that.selectedLanguages) {
                        for (var i=0; i<that.selectedLanguages.length; i++) {
                            if (that.selectedLanguages[i]) {
                                that.languages.push(that.selectedLanguages[i]);
                            }
                        }
                    }
                    that.languages.sort();
                    that.languages = that.languages.join(', ');
                }*/
            });
        }
    };
    Character.prototype.determineLevelBonus = function(level) {
        var index = level - 1,
            PROFICIENCY_ARRAY = [2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6],
            ABILITY_BONUS_ARRAY = [0, 0, 0, 2, 2, 2, 2, 4, 4, 4, 4, 6, 6, 6, 6, 8, 8, 8, 10, 10];
        this.profBonus = PROFICIENCY_ARRAY[index];
        this.ability.bonusPoints = ABILITY_BONUS_ARRAY[index];
    };
    Character.prototype.determineRace = function() {    // change abilityObj name
        this.speed = parseInt(this.raceObj.speed_value);
        this.size = this.raceObj.size_value;
        this.defaultLanguages = this.raceObj.languages || ''; // string
        this.languages = this.defaultLanguages.split(', ');
        if (this.selectedLanguages) {
            for (var i=0; i<this.selectedLanguages.length; i++) {
                if (this.selectedLanguages[i]) {
                    this.languages.push(this.selectedLanguages[i]);
                }
            }
        }
        this.languages.sort();
        this.languages = this.languages.join(', ');
    };
    Character.prototype.handleHitPoints = function() {
        var hpPerLevel;
        if (this.classObj.hit_dice) {
            hpPerLevel = Math.ceil(((parseInt(this.classObj.hit_dice))+1)/2);   // ex: if HD=10, then hpPerLevel is 6
            this.hitPoints = parseInt(this.classObj.hit_dice) + this.ability.con.mod;
            this.bonusHP = this.ability.con.mod;
            if (this.level > 1) {
                for (var i=1; i<this.level; i++) {
                    this.hitPoints += hpPerLevel + this.ability.con.mod;
                    this.bonusHP += this.ability.con.mod;
                }
            }
        }
    };
    Character.prototype.resetSkills = function() {
        if (this.classObj) {
            this.numSkillsLeft = parseInt(this.classObj.num_skills);
            this.classObj.selectedSkills = [];
        }
    };
    Character.prototype.determineClass = function() {    // handles hp and saving throws
        this.handleHitPoints();
        this.savingThrows = this.classObj.saving_throw_code;   // e.g. "wis, cha"
        this.initiative = this.ability.dex.mod;
        this.armorClass = 10 + this.ability.dex.mod;
        this.numSkillsLeft = parseInt(this.classObj.num_skills);
        this.classObj.selectedSkills = [];
    };
    // Handles combining background and tool skills
    Character.prototype.handleTools = function() {   // TODO: Refactor
        var classTools, backgroundTools;
        this.tools = [];
        if (this.classObj.tools && this.classObj.tools.indexOf('None') === -1) {
            classTools = this.classObj.tools.split(', ');
            this.tools = this.tools.concat(classTools);
        }
        if (this.background && this.background.tools) { // background tools can be blank in the database
            backgroundTools = this.background.tools.split(', ');
            this.tools = this.tools.concat(backgroundTools);
        }
        this.tools = $.unique(this.tools);  // remove potential duplicates
        this.tools.sort();
        this.tools = this.tools.join(', '); // return to a string
    };
    Character.prototype.prefillCharacter = function(storedCharacter) {
        for (var prop in storedCharacter) {
            if (storedCharacter.hasOwnProperty(prop)) {
                this[prop] = storedCharacter[prop];
            }
        }
    };

    var character = new Character();
    character.ability = {
        str: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        dex: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        con: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        int: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        wis: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        cha: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0,
            savingThrow: 0,
            min: 8,
            max: 15
        },
        bonusPoints: 0, // for Ability Score Improvement
        bonusPointsLeftArr: [],
        pointsLeft: 15  // 27 points to spend with all 8s
    };
    var newCharacter = angular.copy(character); // for creating new character

    function returnHttpProp(path) {
        return $http({
            url: window.location.pathname + path,
            method: "GET",
            cache: true
        });
    }
    return {
        getNewCharacter: function(level) {
            var charLevel = level ? level : 1;
            character = angular.copy(newCharacter); // resets character
            character.level = charLevel;
            character.calculateModifiers(); // recalculate ability modifiers
            character.determineLevelBonus(charLevel);
            return character;
        },
        returnStoredCharacter: function() {
            return localStorageService.get('character');
        },
        storeCharacter: function() {
            localStorageService.set('character', JSON.stringify(character));
        },
        getLanguages: function() {
            return returnHttpProp('/json_get_languages');
        },
        getRaces: function() {
            return returnHttpProp('/json_get_races');
        },
        getBackgrounds: function() {
            return returnHttpProp('/json_get_backgrounds');
        },
        getClasses: function() {
            return returnHttpProp('/json_get_classes');
        },
        saveCharacter: function() {
            var saveCharacterUrl = location.pathname.replace('character_generator', 'user/saveCharacter');
            return $http({
                method: 'POST',
                url: saveCharacterUrl,
                data: {'character': character},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}  // needed for php since default is application/json
            });
        },
        returnSelect2SpellsConfig: function() {
            return select2Spells;
        }
    };
});