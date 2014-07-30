'use strict';
var charGenService = angular.module('charGenService', []);
charGenService.factory('charGenFactory', function() {
    var ABILITIES = ['str', 'dex', 'con', 'int', 'wis', 'cha'];
    var MIN_ABILITY = 8;
    var MAX_ABILITY = 15;

    function Character() {
        this.name = null,
        this.raceObj = null;
        this.classObj = null; // contains subclasses property
        this.background = null;   // & background.skills
        this.selectedSkills = [],
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
        this.classFeatures = [];
        //this.featureIds = [];
        this.racialTraits = [];
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
    Character.prototype.updateSkillProficiency = function(skillName, disable) {
        var that = this;
        if (angular.isArray(this.skills)) {
            if (skillName) {
                this.skills.forEach(function(skill, i) {
                    if (skillName.indexOf(skill.name) !== -1) {
                        that.skills[i].proficient = true;
                        that.updateSkillScore(skill.name);
                        that.selectedSkills.push(skill.name);
                        that.selectedSkills.sort();
                        if (disable) {
                            that.skills[i].disabled = true;
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
    };
    Character.prototype.enableSkills = function(classSkills, backgroundSkills) {   // skills is an array, exceptions is comma separated string
        var that = this;
        if (angular.isArray(classSkills)) {
            classSkills.forEach(function(skill, i) {
                for (var index=0; index<that.skills.length; index++) {
                    if (that.skills[index].name === skill) {
                        break;
                    }
                }
                if (!backgroundSkills || backgroundSkills.indexOf(classSkills[i]) === -1) {
                    that.skills[index].disabled = false;
                } else {    // class skill shares with background skill, so disable it
                    that.skills[index].disabled = true;
                }
            })
        } else {    // disable everything that isn't checked
            that.skills.forEach(function(skill, i) {
                if (that.skills[i].proficient === false) {
                    that.skills[i].disabled = true;
                }
            });
        }
    };
    Character.prototype.handleSkills = function() {
        var classSkills = this.classObj ? this.classObj.avail_skills.split(', ') : [];    // disable everything if empty array
        this.updateSkillProficiency(false); // wipe skill proficiencies
        this.enableSkills(false);   // disable all skills
        if (this.background) {
            this.updateSkillProficiency(this.background.skills, true);
            this.enableSkills(classSkills, this.background.skills);
        } else {
            this.enableSkills(classSkills);
        }
    };
    Character.prototype.updateSkillScore = function(skillName) {    // if no parameter, update all skills
        var abilityMapper = {
            Str: 'str', Dex: 'dex', Con: 'con', Int: 'int', Wis: 'wis', Cha: 'cha'
        };
        var profBonus = 0;
        var that = this;
        this.skills.forEach(function(skill, i) {
            if (!skillName || skill.name === skillName) {
                profBonus = skill.proficient ? that.profBonus : 0;
                that.skills[i].val = profBonus + that.ability[abilityMapper[skill.ability]].mod;
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
            this.ability.str.mod = Math.floor((this.ability.str.score - 10) / 2);
            this.ability.dex.mod = Math.floor((this.ability.dex.score - 10) / 2);
            this.ability.con.mod = Math.floor((this.ability.con.score - 10) / 2);
            this.ability.int.mod = Math.floor((this.ability.int.score - 10) / 2);
            this.ability.wis.mod = Math.floor((this.ability.wis.score - 10) / 2);
            this.ability.cha.mod = Math.floor((this.ability.cha.score - 10) / 2);
            var that = this;
            ABILITIES.forEach(function(ability) {
                that.ability[ability].savingThrow = that.calculateSavingThrows(ability);
            });
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
                    if (currValueMinusDiff > 15) {
                        abilityCost = 3;
                    } else if (currValueMinusDiff > 13) {
                        abilityCost = 2;
                    } else if (currValueMinusDiff <= 13) {
                        abilityCost = 1;
                    }
                    this.ability.pointsLeft += abilityCost;
                }
                this.ability[ability].score += value;
                this.ability[ability].mod = Math.floor((this.ability[ability].score-10)/2);
                this.calculateModifiers(ability);
            }
        }
    };
    Character.prototype.resetRacialBonuses = function() {
        var diff = 0;
        var that = this;
        ABILITIES.forEach(function(ability) {
            diff = that.ability[ability].max - MAX_ABILITY; // ex: 0, 1, or 2
            if (diff > 0) {
                that.ability[ability].score -= diff;
                that.calculateModifiers(ability);
                that.ability[ability].max = MAX_ABILITY;
                that.ability[ability].min = MIN_ABILITY;
            }
        });
    };
    Character.prototype.handleFeatureBonuses = function(featureBonus) {
        var bonusArray = [], characterArray = [], that = this;
        this.armor = this.classObj ? this.classObj.armor_prof : null;
        this.weapons = this.classObj ? this.classObj.weapon_prof : null;
        for (var bonusProp in featureBonus) {
            var propArray = bonusProp.split(', ');
            propArray.forEach(function(prop) {
                if (that[prop] !== null && (prop === 'hitPoints' || prop === 'initiative' || prop === 'armorClass' || prop === 'attackMod' ||
                    prop === 'speed' || prop === 'numLanguages')) {
                    that[prop] += parseInt(featureBonus[prop]); // character prop needs to exist to add
                } else if (ABILITIES.indexOf(prop) !== -1) {    // ex: 'str', 'dex', etc.
                    that.ability[prop].score += parseInt(featureBonus[bonusProp]);
                    that.ability[prop].max += parseInt(featureBonus[bonusProp]);
                    that.ability[prop].min += parseInt(featureBonus[bonusProp]);
                    that.calculateModifiers(prop);
                } else if (prop === 'armor' || prop === 'weapons') {
                    var allResults = '';
                    if (prop === 'armor') {
                        allResults = 'All armor';
                    } else if (prop === 'weapons') {
                        allResults = 'martial weapons';
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
                    that[prop] = that[prop].join(', ')
                } else if (prop === 'skills') {
                    that.updateSkillProficiency(featureBonus[prop], true);
                } /*else if (prop === 'languages') {    // taken care of by determineRace
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
    Character.prototype.determineRace = function() {    // change abilityObj name
        if (!this.raceObj) {
            return;
        }
        this.speed = parseInt(this.raceObj.speed_value);
        this.size = this.raceObj.size_value;

        this.defaultLanguages = this.raceObj.languages; // string
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

    var character = new Character();
    /*var character = {
        //level: 14,  // 1
        name: null,
        raceObj: null,
        classObj: null, // contains subclasses property
        background: null,   // & background.skills
        selectedSkills: [],
        skills: null,
        languages: null,
        numLanguages: 0,
        //alignment: null,
        armorClass: null,
        attackMod: null,
        savingThrows: null,
        hitPoints: null,
        classFeatures: [],
        classFeatureIds: [],
        racialTraits: [],
        racialTraitIds: [],
        speed: null,
        initiative: null,
        armor: null,
        weapons: null,
        tools: null,
        size: null,
        profBonus: 0
    };*/
    var copy = angular.copy(character);

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
        bonusAbility: null,
        pointsLeft: 15  // 27 points to spend with all 8s
    };
    return {
        getNewCharacter: function(level) {
            var charLevel = level ? level : 1;
            character.level = charLevel;
            character.profBonus = this.determineProficiencyBonus(charLevel);
            return character;
        },
        resetCharacter: function() {    // without changing ability scores
            var charLevel = character.level,
                charAbilities = character.ability,
                profBonus = character.profBonus,
                skills = character.skills,
                name = character.name;
            character = angular.copy(copy);
            character.level = charLevel;
            character.ability = charAbilities;
            character.profBonus = profBonus;
            character.skills = skills;
            character.name = name;
            return character;
        },
        determineClass: function() {    // handles hp and saving throws
            character.hitPoints = parseInt(character.classObj.hit_dice) + character.ability.con.mod;
            character.savingThrows = character.classObj.saving_throw_code;   // e.g. "wis, cha"
            character.initiative = character.ability.dex.mod;
            character.armorClass = 10 + character.ability.dex.mod;
            character.numSkillsLeft = parseInt(character.classObj.num_skills);
            return character;
        },
        determineProficiencyBonus: function(level) {
            var index = level - 1,
                PROFICIENCY_ARRAY = [2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6, 6, 6];
            return PROFICIENCY_ARRAY[index];
        },
        // Handles combining background and tool skills
        handleTools: function() {   // TODO: Refactor
            var classTools, backgroundTools;
            character.tools = [];
            if (character.classObj && character.classObj.tools.indexOf('None') === -1) {
                classTools = character.classObj.tools.split(', ');
                character.tools = character.tools.concat(classTools);
            }
            if (character.background && character.background.tools) { // background tools can be blank in the database
                backgroundTools = character.background.tools.split(', ');
                character.tools = character.tools.concat(backgroundTools);
            }
            character.tools = $.unique(character.tools);  // remove potential duplicates
            character.tools.sort();
            character.tools = character.tools.join(', '); // return to a string
            /*$scope.character.tools = $scope.character.background.skills.split(', ');
             if ($scope.selectedSkills) {
             for (var i=0; i<$scope.selectedSkills.length; i++) {
             if ($scope.character.background.skills.indexOf($scope.selectedSkills[i]) === -1) {
             $scope.character.skills.push($scope.selectedSkills[i]);
             } else {
             $scope.selectedSkills.splice(i, 1);
             }
             }
             }
             $scope.character.skills.sort();
             $scope.character.skills = $scope.character.skills.join(', ');*/
            return character;
        }
    };
});