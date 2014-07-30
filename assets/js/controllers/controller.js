'use strict';

var charModule = angular.module('generator', ['ngSanitize', 'ui.bootstrap.modal', 'charGenService', 'ui.select2', 'charGenDirective']);

charModule.run(function($rootScope) {
    /*
        Receive emitted message and broadcast it.
        Event names must be distinct or browser will blow up!
     */
    $rootScope.$on('handleEmit', function(event, args) {
        $rootScope.$broadcast('handleBroadcast', args);
    });
});


/**
 * Angular Controller
 * @param $scope
 * @param $modal
 */
// TODO: refactor
function CharGen($scope, $modal, $http, charGenFactory) {
    $scope.Math = window.Math;
    $scope.character = charGenFactory.getNewCharacter(1);

    // Initialize variables
    $scope.racialBonus = {};
    $scope.searchText = '';
    $scope.subclasses = [];
    $scope.opts = {
        backdrop: true,
        keyboard: true,
        backdropClick: true,
        templateUrl: 'dialog'
    };
    $scope.numLanguagesLeft = 0;

    $scope.saveCharacter = function() {
        var saveCharacterUrl = location.pathname.replace('character_generator', 'user/saveCharacter');
        //var stringifiedCharacter = JSON.stringify($scope.character);
        $scope.validating = true;
        if ($scope.charGenForm.$valid) {
            $http({
                method: 'POST',
                url: saveCharacterUrl,
                data: {'character': $scope.character},
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}  // needed for php since default is application/json
            })
            .success(function(data, status, headers, config) {
                $scope.successMessage = "Character saved successfully";
                $scope.errorMessage = null;
            })
            .error(function(data, status, headers, config) {
                $scope.successMessage = null;
                $scope.errorMessage = data.message;
            });
        }
    };

    $scope.filterByName = function(value) {
        if (value.name) {
            return value.name.toLowerCase().indexOf($scope.searchText.toLowerCase()) !== -1;
        }
    };

    $scope.openDialog = function() {
        $modal.open($scope.opts);
        //dialog.open();
    };

    $scope.openRaceDialog = function() {
        $scope.opts.templateUrl = 'dialog/raceDialog'; //'raceModal.html';
        $scope.opts.controller = DialogRaceController;
        $scope.opts.resolve = {
            raceData: function() { return angular.copy($scope.raceData); }
        };
        $scope.openDialog();
    };

    $scope.openClassDialog = function() {
        $scope.opts.templateUrl = 'dialog/classDialog'; //'classModal.html';
        $scope.opts.controller = DialogClassController;
        $scope.opts.resolve = {
            classData: function() { return angular.copy($scope.classData); }
        };
        $scope.openDialog();
    };

    $scope.openSubclassDialog = function() {
        $scope.opts.templateUrl = 'dialog/classDialog'; //'classModal.html';
        $scope.opts.controller = DialogSubclassController;
        $scope.opts.resolve = {
            subclasses: function() { return angular.copy($scope.subclasses); }
        };
        $scope.openDialog();
    };

    $scope.openFeatureDialog = function() {
        $scope.opts.templateUrl = 'dialog/classDialog';
        $scope.opts.controller = DialogFeatureController;
        $scope.opts.resolve = {
            features: function() { return angular.copy($scope.featureChoices); }
        };
        $scope.openDialog();
    };

    $scope.openBackgroundDialog = function() {
        $scope.opts.templateUrl = 'dialog/background';  //'backgroundModal.html';
        $scope.opts.controller = DialogBackgroundController;
        $scope.opts.resolve = {
            backgroundData: function() { return angular.copy($scope.backgroundData); }
        };
        $scope.openDialog();
    };

    // Deprecated; could still be used for mobile purposes though
    function determineNumItems(selector, value) {
        $scope.isMobile = $('#isMobile').val();
        if ($scope.isMobile === 'true') {
            $('.select2-input').prop('readonly', true);
        }

        return value;   //$scope.select2Languages;
    }

    // Watches
    $scope.$watch('selectedSkills', function(newValue) {
        if (angular.isArray(newValue) && newValue.length > 0) {
            var numSkills = parseInt($scope.character.classObj.num_skills);
            if (newValue.length <= numSkills) {
                if ($scope.character.background || $scope.character.raceObj) {
                    handleSkills(); // newValue
                } else {
                    $scope.character.skills = newValue.join(", ");
                }
            }
        }
    });
    $scope.$watch('character.selectedLanguages', function(newValue, oldValue) {   // triggered whenever a language is selected
        if ((newValue || (!newValue && oldValue)) && $scope.character.raceObj) {   //(newValue || oldValue)
            // TODO: handleLanguages();
            $scope.character.selectedLanguages = $scope.character.selectedLanguages || oldValue;
            var selectedLanguages = newValue || oldValue;
            var languages = $scope.character.defaultLanguages ? $scope.character.defaultLanguages.split(', ') : [];
            languages = languages.concat(selectedLanguages);
            languages.sort();
            $scope.character.languages = languages.join(', ');
            $scope.numLanguagesLeft = $scope.character.numLanguages - selectedLanguages.length;
        }
    });
    $scope.$watch('character.raceObj.subrace.name', function(newValue) {
       if (newValue) {
           var raceLanguages = $scope.character.raceObj.languages.split(', ');
           //$scope.availableLanguages = [];  //LANGUAGE_LIST;  // commented out because it was clearing the language list in the ui
           //var languageList = angular.copy(LANGUAGE_LIST);    //$scope.availableLanguages;
           var languageList = [];
           for (var i=0, ilen=LANGUAGE_LIST.length; i<ilen; i++) {
                var language = LANGUAGE_LIST[i];
                if ($scope.character.raceObj.languages.indexOf(language) === -1) {    // the race language is found in the race list
                    languageList.push(language);
                    //languageList.splice(i, 1);
                    //languageList[i] = null;
                    //$scope.availableLanguages.splice(i, 1);
                    //$scope.availableLanguages.push(LANGUAGE_LIST[i]);   //language
                }
           }
           $scope.availableLanguages = languageList;
       }
    });
    $scope.$watch('character.ability.str.mod', function(newValue) {
        if (!isNaN(newValue)) {
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.ability.dex.mod', function(newValue) {
        if (!isNaN(newValue)) {
            $scope.character.initiative = newValue;
            $scope.character.armorClass = 10 + newValue;
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.ability.con.mod', function(newValue) {
        if (!isNaN(newValue) && $scope.character.classObj) {
            $scope.character.hitPoints = parseInt($scope.character.classObj.hit_dice) + newValue;
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.ability.int.mod', function(newValue) {
        if (!isNaN(newValue)) {
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.ability.wis.mod', function(newValue) {
        if (!isNaN(newValue)) {
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.ability.cha.mod', function(newValue) {
        if (!isNaN(newValue)) {
            $scope.character.updateSkillScore();
        }
    });
    $scope.$watch('character.numLanguages', function(newValue) {
        if (angular.isDefined(newValue)) {//} && $scope.character.background) {
            $scope.character.selectedLanguages = $scope.character.selectedLanguages || [];
            $scope.select2Languages = newValue; // represents the 'max' attribute for select2
            $scope.numLanguagesLeft = $scope.character.numLanguages - $scope.character.selectedLanguages.length;
            //$scope.selectedLanguages.length = newValue; //determineNumItems('#chosenLanguages', newValue);
        }
    });

    // determines points left
    $scope.incrementAbility = function(ability, value) {    // value can only be 1 or -1
        $scope.character.modifyAbilityScore(ability, value);
    };

    function returnHttpProp(path) {
        return {
            url: window.location.pathname + path,
            method: "GET",
            cache: true
        };
    }

    var LANGUAGE_LIST = [];
    $scope.init = function() {
        $http(returnHttpProp('/json_get_languages')).success(function(data) {
            for (var i=0, ilen=data.length; i<ilen; i++) {
                LANGUAGE_LIST.push(data[i].name);
            }
            //LANGUAGE_LIST = data;
            $scope.availableLanguages = angular.copy(LANGUAGE_LIST);
            //determineNumItems('#chosenLanguages', 0);
        });

        $http(returnHttpProp('/json_get_races')).success(function(data) {
            $scope.raceData = data;
        });

        $http(returnHttpProp('/json_get_backgrounds')).success(function(data) {
            $scope.backgroundData = data;
        });

        $http(returnHttpProp('/json_get_classes')).success(function(data) {
            $scope.classData = data;
        });

    };

    $scope.$on('handleBroadcast', function(event, args) {
        var features = {};  // reset
        if (args.checked) {
            if (!args.subclass && !args.selectedFeature) {
                $scope.character = charGenFactory.resetCharacter();
            }
            //if (args.race && $scope.race) { // only true if user selected a different race
                $scope.character.resetRacialBonuses();
            //}
            if (args.race || args.background) {
                $scope.character.selectedLanguages = []; // reset languages only if race or background is selected
            }
            if (args.race || $scope.race) { // && args.racialTraits) { // args.race is a string of the race's name
                $scope.race = args.race || $scope.race;
                $scope.racialBonus = {};    // reset racial bonuses
                $scope.character.raceObj = $scope.race;
                //$scope.character.racialTraits = args.racialTraits;
                $scope.character.racialTraits = []; // reset
                if ($scope.race.traits) {
                    $scope.race.traits.forEach(function(trait) {
                        if (trait.benefit_desc) {
                            $scope.character.racialTraits.push(new KeyValue(trait.id, trait.name, trait.benefit_desc));
                        }
                        if (trait.benefit_stat) {
                            features[trait.benefit_stat] = trait.benefit_value;
                        }
                    });
                }
                if ($scope.race.subrace.traits) {
                    $scope.race.subrace.traits.forEach(function(trait) {
                        if (trait.benefit_desc) {
                            $scope.character.racialTraits.push(new KeyValue(trait.id, trait.name, trait.benefit_desc));
                        }
                        if (trait.benefit_stat) {
                            features[trait.benefit_stat] = trait.benefit_value;
                        }
                    });
                }

                $scope.character.determineRace();   // now takes care of languages
                //$scope.character.racialTraitIds = args.racialTraitIds;
            }
            if ((args.clazz || $scope.clazz) && !args.subclass && !args.selectedFeature) {
                $scope.clazz = args.clazz || $scope.clazz;
                $scope.character.classObj = $scope.clazz;   // used to determine if class contains subclasses
                if (args.clazz) {
                    $scope.selectedFeature = null;    // reset
                }
                /** what was this for again??
                if ($scope.class && $scope.ability.bonusAbility) {
                    $scope.ability[$scope.ability.bonusAbility].score -= 1;
                    $scope.ability.bonusAbility = null;
                }*/
                //$scope.character.className = args.clazz.name;
                $scope.character = charGenFactory.determineClass();
                if ($scope.clazz.features) {
                    $scope.character.classFeatures = [];    // reset
                    $scope.character.featureIds = [];  // reset
                    $scope.subclasses = [];    // reset
                    $scope.featureChoices = []; // reset
                    $scope.clazz.features.forEach(function(featureObj) {
                        if (featureObj.level <= $scope.character.level) {
                            var tempBenefit = '', classFeatureId = '';
                            if (featureObj.subclasses) {
                                $scope.subclasses = featureObj.subclasses;
                            }
                            if (featureObj.benefit) {
                                featureObj.benefit.forEach(function(benefitObj) {
                                    if (benefitObj.level <= $scope.character.level) {
                                        classFeatureId = benefitObj.id;
                                        tempBenefit = benefitObj.description;
                                    }
                                });
                                $scope.character.classFeatures.push(new KeyValue(classFeatureId, featureObj.name, tempBenefit));
                                tempBenefit = '';   // reset
                            }
                            // if parent feature is a choice, set $scope.featureChoices for dialog, else set subfeatures
                            if (featureObj.benefit_stat === 'feature_choice') {
                                $scope.featureChoices = featureObj.subfeatures;
                            }else if (featureObj.subfeatures) {
                                featureObj.subfeatures.forEach(function(subfeature) {
                                    if (subfeature.benefit) {
                                        subfeature.benefit.forEach(function(benefitObj) {
                                            if (benefitObj.level <= $scope.character.level) {
                                                classFeatureId = benefitObj.id
                                                tempBenefit = benefitObj.description;
                                            }
                                        });
                                        $scope.character.classFeatures.push(new KeyValue(classFeatureId, subfeature.name, tempBenefit));
                                        tempBenefit = '';   // reset
                                    }
                                });
                            }
                            if (featureObj.benefit_stat) {
                                features[featureObj.benefit_stat] = featureObj.benefit_value;
                            }
                        }
                    });
                    $scope.currentClassFeatures = $scope.character.classFeatures;
                }
                $scope.character = charGenFactory.handleTools();
            }
            if (args.subclass || $scope.subclass) {
                $scope.subclass = args.subclass || $scope.subclass;
                //$scope.subclass = $scope.classObj.subclasses[args.subclass.name];    // object
                $scope.character.classObj.subclassObj = args.subclass;
                var subclassFeatures = [];
                // TODO: change subclass property from benefit to features
                if ($scope.subclass.benefit) {
                    //for (var subclassProp in args.subclass.benefit) {
                    $scope.subclass.benefit.forEach(function(feature) {
                        if (feature.level <= $scope.character.level) {
                            var tempSubclassBenefit = '';
                            if (angular.isArray(feature.benefit)) {
                                feature.benefit.forEach(function(benefit) {
                                    if (benefit.level <= $scope.character.level) {
                                        tempSubclassBenefit = benefit;
                                    }
                                });
                                subclassFeatures.push(new KeyValue(tempSubclassBenefit.id, feature.name, tempSubclassBenefit.description));
                            }
                            if (feature.benefit_stat) {
                                features[feature.benefit_stat] = feature.benefit_value;
                            }
                        }
                    });
                    $scope.character.classFeatures = $scope.currentClassFeatures.concat(subclassFeatures);
                }
            }
            if (args.selectedFeature || $scope.selectedFeature) {
                $scope.selectedFeature = args.selectedFeature || $scope.selectedFeature;
                if ($scope.selectedFeature.benefit) {
                    var tempFeatureChoice = '';
                    $scope.selectedFeature.benefit.forEach(function(feature) {
                        if (feature.level <= $scope.character.level) {
                            tempFeatureChoice = feature;
                        }
                    });
                    $scope.character.classFeatures.push(new KeyValue(tempFeatureChoice.id, $scope.selectedFeature.name, tempFeatureChoice.description));
                }
            }
            /*if (args.html) {
                $scope.html = args.html;
            }*/
            if (args.background || $scope.background) {
                $scope.background = args.background || $scope.background;
                $scope.character.background = $scope.background;
                $scope.character = charGenFactory.handleTools();
            }
            $scope.character.numLanguages = $scope.character.background ? parseInt($scope.character.background.languages) : 0;
            $scope.character.calculateModifiers(); // update ability modifiers
            /*if ($scope.character.ability['int'].mod > 0) {  // needs to come after
                $scope.character.numLanguages += $scope.character.ability['int'].mod;
            }*/
            //$scope.character.hitPoints = $scope.character.classHP + $scope.character.ability.con.mod;   // no longer accounts for dialog ability

            // needs to be at the very end to alter existing properties
            if (!args.subclass && !args.selectedFeature) {  // assuming that features and subclasses don't affect skills
                $scope.character.handleSkills();
            }
            $scope.character.handleFeatureBonuses(features);
            args.checked = false;
        }
    });
}

function DialogRaceController($scope, $modalInstance, raceData) {
    $scope.title = 'Select Race';

    var data = raceData;
    $scope.values = [];     // clears the data
    $scope.subvalues = [];
    var subrace, raceObj, outerIndex = 0;
    for (var i=0; i<data.length; i++) {
        if (!data[i].subraces) {
            data[i].subraces = [];
            subrace = {
                name: data[i].name,
                desc: data[i].desc
            };
            data[i].subraces.push(subrace);
        }
        $scope.values.push(data[i]);
    }
    for (var j=0; j<$scope.values.length; j++) {
        //$scope.subvalues = $scope.subvalues.concat($scope.values[j].subraces);
        for (var k=0; k<$scope.values[j].subraces.length; k++) {
            raceObj = angular.copy($scope.values[j]);
            $scope.subvalues = $scope.subvalues.concat(raceObj);   // include race properties
            //delete $scope.subvalues[k].subraces;
            $scope.subvalues[outerIndex].subrace = $scope.values[j].subraces[k];    // include subrace properties
            outerIndex++;
        }
    }
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempClass = '';

    $scope.showDescription = function(selectobj, raceObj) {
        $scope.raceObj = raceObj;
        $scope.selectedIndex = selectobj.$index;
        var subrace;
        $scope.tempRace = '';
        $scope.featureType = 'Race Traits';
        $scope.size = '';
        $scope.speed = '';
        $scope.traits = [];
        $scope.features = [];
        for (var i=0; i<$scope.subvalues.length; i++) {
            //if ($scope.values[i].name === raceObj.name) {
            //if ($scope.subvalues[i].name === subraceObj.name) {
                //for (var j=0; j<$scope.subvalues[i].subraces.length; j++) {
                    //race = $scope.values[i];
                    //subrace = $scope.values[i].subraces[j];
                    subrace = $scope.subvalues[i].subrace;
                    /*if (!subrace) {
                        subrace = race;
                    }*/
                    if (subrace.name === raceObj.subrace.name) {   // selected the right subrace
                        $scope.tempRace = raceObj.subrace;
                        $scope.description = subrace.desc;
                        //$scope.race_aba = raceObj.ability_score_adjustment;   // was for dialog UI
                        //$scope.subrace_aba = subrace.ability_score_adjustment;
                        $scope.size = raceObj.size;
                        $scope.speed = raceObj.speed;
                        //$scope.languages = raceObj.languages;
                        $scope.racialTraitIds = [];
                        for (var trait in raceObj.traits) {    // race traits
                            if (raceObj.traits.hasOwnProperty(trait)) {
                                var raceTrait = raceObj.traits[trait];
                                $scope.traits.push(new KeyValue(raceTrait.id, raceTrait.name, raceTrait.description,
                                    raceTrait.benefit, raceTrait.benefit_value, raceTrait.per_level));
                                $scope.racialTraitIds.push(raceObj.traits[trait].id);
                            }
                        }
                        for (var trait in subrace.traits) { // subrace traits
                            if (subrace.traits.hasOwnProperty(trait)) {
                                var subraceTrait = subrace.traits[trait];
                                $scope.traits.push(new KeyValue(subraceTrait.id, subraceTrait.name, subraceTrait.description,
                                    subraceTrait.benefit, subraceTrait.benefit_value, subraceTrait.per_level));
                                $scope.racialTraitIds.push(subrace.traits[trait].id);
                            }
                        }
                        break;
                    }
                //}
            //}
        }
    };

    $scope.done = function() {
        if ($scope.tempRace) {  // the subrace name
            $scope.$emit('handleEmit', {race: $scope.raceObj, checked: true}); // racialTraits: $scope.traits, racialTraitIds: $scope.racialTraitIds,
            $modalInstance.close();
        } else {
            alert("Please select a race");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

// the dialog is injected in the specified controller
function DialogClassController($scope, $modalInstance, classData){

    $scope.title = 'Select Class';
    var data = classData;
    $scope.values = [];     // clears the data
    for (var i=0; i<data.length; i++) {
        $scope.values.push(data[i]);
    }
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempClass = '';

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        $scope.tempClass = null;
        $scope.featureType = 'Class Features';
        $scope.traits = [], $scope.traits2 = [];
        $scope.features = [];
        //for (var i=0; i<$scope.values.length; i++) {
        $scope.values.forEach(function(value) {
            if (value.name === selectobj.value.name) {
                $scope.tempClass = selectobj.value; // object
                $scope.description = value.desc;
                $scope.traitsTitle = "Hit Points";
                $scope.traits.push(new NameDesc("Hit Dice", "1d" + value.hit_dice + " per " + value.name + " level"),
                    new NameDesc("Hit Points at 1st Level", value.hit_dice + " + your Constitution modifier"),
                    new NameDesc("Hit Points at Higher Levels", "1d" + value.hit_dice + " + your Constitution modifier per " +
                        value.name + " level after 1st"));
                $scope.traits2Title = "Proficiencies";
                $scope.traits2.push(new NameDesc("Armor", value.armor_prof), new KeyValue("Weapons", value.weapon_prof),
                    new NameDesc("Tools", value.tools), new NameDesc("Saving Throws", value.saving_throws),
                    new NameDesc("Skills", "Choose " + value.num_skills + " from " + value.avail_skills));
                //for (var feature in value.features) {
                if (angular.isArray(value.features)) {
                    value.features.forEach(function(obj) {
                        $scope.features.push(new NameDesc(obj.name, obj.desc));
                    });
                }
                // foreach equivalent of break
            }
        });
    };

    $scope.done = function() {
        if ($scope.tempClass) {
            $scope.$emit('handleEmit', {clazz: $scope.tempClass, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a class");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogSubclassController($scope, $modalInstance, subclasses){
    //$scope.class = character.classObj.name;
    $scope.title = 'Select Subclass';   // change later
    $scope.values = subclasses;

    /*$http({
        url: window.location.pathname + '/json_get_classes/' + $scope.class,
        method: "GET",
        cache: true
    }).success(function(data) {
        $scope.values = [];     // clears the data
        for (var subclass in data.subclasses) {
            $scope.values.push(data.subclasses[subclass]);
        }

    });*/

    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempSubclass = '';

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        var value;
        $scope.tempSubclass = '';
        $scope.featureType = 'Class Features';
        $scope.traits = [];
        $scope.features = [];
        for (var i=0; i<$scope.values.length; i++) {
            if ($scope.values[i].name === selectobj.value.name) {
                $scope.tempSubclass = selectobj.value;
                value = $scope.values[i];
                $scope.description = value.desc;
                /*if (value.align) {
                    $scope.features.push(new NameDesc("Alignment", value.align));
                }*/
                if (value.armor_prof) {
                    $scope.features.push(new NameDesc("Armor and Shield Proficiencies", value.armor_prof));
                }
                for (var feature in value.benefit) {
                    if (value.benefit.hasOwnProperty(feature)) {
                        $scope.features.push(new NameDesc(value.benefit[feature].name, value.benefit[feature].desc));
                    }
                }
                break;
            }
        }
    };

    $scope.done = function() {
        if ($scope.tempSubclass) {
            $scope.$emit('handleEmit', {subclass: $scope.tempSubclass, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a subclass");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogFeatureController($scope, $modalInstance, features) {
    $scope.title = 'Select Feature';
    $scope.values = features;

    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempFeature = '';

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        var value;
        $scope.tempFeature = '';
        $scope.features = [];
        for (var i=0; i<$scope.values.length; i++) {
            if ($scope.values[i].name === selectobj.value.name) {
                $scope.tempFeature = selectobj.value;
                value = $scope.values[i];
                $scope.features.push(new KeyValue(value.id, value.name, value.desc));
                break;
            }
        }
    };

    $scope.done = function() {
        if ($scope.tempFeature) {
            $scope.$emit('handleEmit', {selectedFeature: $scope.tempFeature, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a feature");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogBackgroundController($scope, $modalInstance, backgroundData) {
    //$scope.templateUrl = 'dialog/background';
    $scope.title = 'Select Background';
    var data = backgroundData;
    $scope.values = [];     // clears the data
    for (var i=0; i<data.length; i++) {
        $scope.values.push(data[i]);
    }
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    //$scope.tempBackground = '';

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        var value;
        $scope.tempBackground = '';
        $scope.featureType = 'Background Trait';
        $scope.traitName = '';
        $scope.traitDesc = '';
        //$scope.skills = [];
        $scope.skills = '';
        for (var i=0; i<$scope.values.length; i++) {
            if ($scope.values[i].name === selectobj.value.name) {
                $scope.tempBackground = selectobj.value;
                value = $scope.values[i];
                $scope.description = value.desc;
                $scope.traitName = value.trait_name;
                $scope.traitDesc = value.trait_desc;
                $scope.skills = value.skills;
                $scope.tools = value.tools;
                $scope.languages = value.language_desc;
                /*for (var j=0; j<value.skills.length; j++) {
                    $scope.skills.push(value.skills[j]);
                }*/
                break;
            }
        }
    };

    $scope.done = function() {
        if ($scope.tempBackground) {
            $scope.$emit('handleEmit', {background: $scope.tempBackground, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a background");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function KeyValue(id, name, benefit, benefit_stat, benefit_value, per_level) {
    this.id = id;
    this.name = name;
    this.benefit = benefit;
    this.benefit_stat = benefit_stat;
    this.benefit_value = benefit_value;
    this.per_level = per_level;
}
function NameDesc(name, desc) {
    this.name = name;
    this.benefit = desc;
}
