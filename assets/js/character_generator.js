var charGenService = angular.module('charGenService', []);
charGenService.factory('charGenFactory', function() {
    var ABILITY_MAPPER = {
        Strength: 'str',
        Dexterity: 'dex',
        Constitution: 'con',
        Intelligence: 'int',
        Wisdom: 'wis',
        Charisma: 'cha'
    };
    return {
        calculateModifiers: function(abilityObj) {
            abilityObj.str.mod = Math.floor((abilityObj.str.score - 10) / 2);
            abilityObj.dex.mod = Math.floor((abilityObj.dex.score - 10) / 2);
            abilityObj.con.mod = Math.floor((abilityObj.con.score - 10) / 2);
            abilityObj.int.mod = Math.floor((abilityObj.int.score - 10) / 2);
            abilityObj.wis.mod = Math.floor((abilityObj.wis.score - 10) / 2);
            abilityObj.cha.mod = Math.floor((abilityObj.cha.score - 10) / 2);
            return abilityObj;
        },
        // TODO: account for bonusHP (Hill Dwarf)
        determineRace: function(characterObj) {    // change abilityObj name
            if (!characterObj.raceObj) {
                return characterObj;
            }
            characterObj.speed = parseInt(characterObj.raceObj.speed_value);
            characterObj.size = characterObj.raceObj.size.split('.')[0];    // TODO: change
            for (var ability in ABILITY_MAPPER) {
                if (characterObj.ability[ABILITY_MAPPER[ability]].raceBonus === 1) {
                    characterObj.ability[ABILITY_MAPPER[ability]].score -= 1;   // reset from old race
                }
                characterObj.ability[ABILITY_MAPPER[ability]].raceBonus = 0;
                if (characterObj.raceObj.ability_score_adjustment.indexOf(ability) !== -1) {
                    characterObj.ability[ABILITY_MAPPER[ability]].raceBonus = 1;
                }
                if (characterObj.raceObj.subrace.ability_score_adjustment) {
                    if (characterObj.raceObj.subrace.ability_score_adjustment.indexOf(ability) !== -1) {
                        characterObj.ability[ABILITY_MAPPER[ability]].raceBonus = 1;
                    }
                }
                if (characterObj.raceObj.ability_score_adjustment.indexOf("each") !== -1) { // TODO: change
                    characterObj.ability[ABILITY_MAPPER[ability]].raceBonus = 1;
                }
                characterObj.ability[ABILITY_MAPPER[ability]].score += characterObj.ability[ABILITY_MAPPER[ability]].raceBonus;
            }
            return characterObj;
            /*switch(characterObj.raceObj.name) {
                case "Hill Dwarf":
                    attributeObj.ability.con.score += val;
                    attributeObj.ability.str.score += val;
                    attributeObj.racialHP = val;
                    break;
                case "Mountain Dwarf":
                    attributeObj.ability.con.score += val;
                    attributeObj.ability.wis.score += val;
                    attributeObj.racialHP = null;
                    break;
                case "High Elf":
                    attributeObj.ability.dex.score += val;
                    attributeObj.ability.int.score += val;
                    attributeObj.racialHP = null;
                    break;
                case "Wood Elf":
                    attributeObj.ability.dex.score += val;
                    attributeObj.ability.wis.score += val;
                    attributeObj.racialHP = null;
                    break;
                case "Lightfoot Halfling":
                    attributeObj.ability.dex.score += val;
                    attributeObj.ability.cha.score += val;
                    attributeObj.racialHP = null;
                    break;
                case "Stout Halfling":
                    attributeObj.ability.dex.score += val;
                    attributeObj.ability.con.score += val;
                    attributeObj.racialHP = null;
                    break;
                case "Human":
                    attributeObj.ability.str.score += val;
                    attributeObj.ability.dex.score += val;
                    attributeObj.ability.con.score += val;
                    attributeObj.ability.int.score += val;
                    attributeObj.ability.wis.score += val;
                    attributeObj.ability.cha.score += val;
                    attributeObj.racialHP = null;
                    break;
            }
            return attributeObj;*/
        },
        determineClass: function(characterObj) {
            characterObj.classHP = parseInt(characterObj.classObj.hit_dice);
            for (var ability in ABILITY_MAPPER) {
                if (characterObj.classObj.ability_adj.indexOf(ability) !== -1) {
                    characterObj.ability[ABILITY_MAPPER[ability]].bonus = true;
                } else {
                    characterObj.ability[ABILITY_MAPPER[ability]].bonus = false;
                }
            }
            characterObj.armor = characterObj.classObj.armor_prof;
            characterObj.weapons = characterObj.classObj.weapon_prof;
            return characterObj;
        },
        determineProficiencyBonus: function(level) {
            var index = level - 1,
                PROFICIENCY_ARRAY = [1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5, 6, 6];
            return PROFICIENCY_ARRAY[index];
        }
    };
});