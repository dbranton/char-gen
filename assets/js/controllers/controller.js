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
function CharGen($scope, $modal, $location, $anchorScroll, charGenFactory) {
    $scope.Math = window.Math;
    $scope.character = charGenFactory.getNewCharacter();    // defaults to level 1 character if user chooses not to select a level at first

    // Initialize variables
    $scope.racialBonus = {};
    $scope.searchText = '';
    $scope.subclasses = [];
    $scope.storedCharacter = null;
    var opts = {
        backdrop: true,
        keyboard: true,
        backdropClick: true,
        templateUrl: 'dialog'
    };
    $scope.numLanguagesLeft = 0;

    charGenFactory.checkIfLoggedIn().success(function(data) {
        $scope.isLoggedIn = JSON.parse(data);
        $scope.isNotLoggedIn = !$scope.isLoggedIn;
    });
    $scope.fillInCharacter = function() {
        $scope.storedCharacter = charGenFactory.returnStoredCharacter();
        $scope.character.prefillCharacter($scope.storedCharacter);
        $scope.race = $scope.character.raceObj;
        $scope.clazz = $scope.character.classObj;
    };

    $scope.saveCharacter = function() {
        //var stringifiedCharacter = JSON.stringify($scope.character);
        $scope.validating = true;
        if ($scope.charGenForm.$valid) {
            if ($scope.isLoggedIn) {
                charGenFactory.saveCharacter()
                    .success(function(data, status, headers, config) {
                        $scope.successMessage = "Character saved successfully";
                        $scope.errorMessage = null;
                        $location.hash('wrap');
                        $anchorScroll();
                    })
                    .error(function(data, status, headers, config) {
                        $scope.successMessage = null;
                        $scope.errorMessage = data.message;
                    });
            } else {    // not logged in, therefore save character
                charGenFactory.storeCharacter();
                $scope.storedCharacter = charGenFactory.returnStoredCharacter();    // update stored character
                $scope.successMessage = "Character saved locally. Warning: you will lose your character if you clear your cache.";
                $location.hash('wrap');
                $anchorScroll();
            }
        }
    };

    $scope.filterByName = function(value) {
        if (value.name) {
            return value.name.toLowerCase().indexOf($scope.searchText.toLowerCase()) !== -1;
        }
    };

    function openDialog(size) {
        var localOpts = angular.copy(opts);
        if (size) {
            localOpts.size = size;
        }
        $modal.open(localOpts);
        //dialog.open();
    }

    $scope.openNewCharDialog = function() {
        opts.templateUrl = 'dialog/newChar';
        opts.controller = DialogNewCharController;
        openDialog('sm');
    };

    $scope.openRaceDialog = function() {
        opts.templateUrl = 'dialog/raceDialog'; //'raceModal.html';
        opts.controller = DialogRaceController;
        opts.resolve = {
            raceData: function() { return angular.copy($scope.raceData); }
        };
        openDialog();
    };

    $scope.openBackgroundDialog = function() {
        opts.templateUrl = 'dialog/background';  //'backgroundModal.html';
        opts.controller = DialogBackgroundController;
        opts.resolve = {
            backgroundData: function() { return angular.copy($scope.backgroundData); }
        };
        openDialog();
    };

    $scope.openClassDialog = function() {
        opts.templateUrl = 'dialog/classDialog'; //'classModal.html';
        opts.controller = DialogClassController;
        opts.resolve = {
            classData: function() { return angular.copy($scope.classData); }
        };
        openDialog();
    };

    $scope.openSubclassDialog = function() {
        opts.templateUrl = 'dialog/classDialog'; //'classModal.html';
        opts.controller = DialogSubclassController;
        opts.resolve = {
            subclasses: function() { return angular.copy($scope.character.classObj.subclasses); }
        };
        openDialog();
    };

    $scope.openFeatureDialog = function() {
        var that = this;
        var featureName = this.selectedFeature.name;
        opts.templateUrl = 'dialog/classDialog';
        opts.controller = DialogFeatureController;
        opts.resolve = {
            features: function() { return angular.copy($scope.character.classObj.featureChoices); },
            index: function() { return that.$index; },
            selectedFeatures: function() { return $scope.selectedFeatures; },
            featureName: function() { return featureName; }
        };
        openDialog();
    };

    $scope.openCantripDialog = function() {
        opts.templateUrl = 'dialog/spellDialog';
        opts.controller = DialogCantripController;
        opts.resolve = {
            cantrips: function() { return angular.copy($scope.character.classObj.cantrips); },
            numCantrips: function() { return $scope.character.classObj.numCantrips; }
        };
        openDialog();
    };

    $scope.openSummary = function() {
        opts.templateUrl = 'dialog/summary';
        opts.controller = DialogSummaryController;
        opts.resolve = {
            character: function() { return angular.copy($scope.character); }
        };
        openDialog('lg');
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
    /*$scope.$watch('character.level', function(newValue) {
        if (!isNaN(newValue)) {

        }
    });*/
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
           var languageList = [];
           for (var i=0, ilen=LANGUAGE_LIST.length; i<ilen; i++) {
                var language = LANGUAGE_LIST[i];
                if ($scope.character.raceObj.languages.indexOf(language) === -1) {    // the race language is found in the race list
                    languageList.push(language);
                }
           }
           $scope.availableLanguages = languageList;
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

    var LANGUAGE_LIST = [];
    $scope.init = function() {
        $scope.storedCharacter = charGenFactory.returnStoredCharacter();
        charGenFactory.getLanguages().success(function(data) {
            for (var i=0, ilen=data.length; i<ilen; i++) {
                LANGUAGE_LIST.push(data[i].name);
            }
            $scope.availableLanguages = angular.copy(LANGUAGE_LIST);
        });
        charGenFactory.getRaces().success(function(data) {
            //$scope.raceData = data;
            var races = [], subraces = [],  subrace, raceObj, outerIndex = 0;
            for (var i=0; i<data.length; i++) {
                if (!data[i].subraces) {
                    data[i].subraces = [];
                    subrace = {
                        name: data[i].name,
                        desc: data[i].desc
                    };
                    data[i].subraces.push(subrace);
                }
                races.push(data[i]);
            }
            for (var j=0; j<races.length; j++) {
                //subraces = subraces.concat(races[j].subraces);
                for (var k=0; k<races[j].subraces.length; k++) {
                    raceObj = angular.copy(races[j]);
                    subraces = subraces.concat(raceObj);   // include race properties
                    //delete subraces[k].subraces;
                    subraces[outerIndex].subrace = races[j].subraces[k];    // include subrace properties
                    outerIndex++;
                }
            }
            $scope.raceData = subraces;
        });
        charGenFactory.getBackgrounds().success(function(data) {
            $scope.backgroundData = data;
        });
        charGenFactory.getClasses().success(function(data) {
            $scope.classData = data;
        });
        $scope.openNewCharDialog();
    };

    $scope.broadcastObj = function(arr, name, prop, index) {
        var obj = {};
        if (name) {
            for (var i=0, ilen=arr.length; i<ilen; i++) {
                if ((prop === 'race' && arr[i].subrace.name === name) || arr[i].name === name) { // assuming arr has a name property
                    obj.checked = true;
                    obj[prop] = angular.copy(arr[i]);
                    if (!isNaN(index)) {
                        obj.featureIndex = index;
                    }
                    $scope.$broadcast('handleBroadcast', obj);
                    break;
                }
            }
        }
    };

    $scope.$on('handleBroadcast', function(event, args) {
        var features = {};  // reset
        if (args.checked) {
            /*if (!args.subclass && !args.selectedFeature) {
                $scope.character = charGenFactory.resetCharacter();   // no longer needed
            }*/
            if (args.level) {
                $scope.character = charGenFactory.getNewCharacter(args.level);
                $scope.race = null;
                $scope.clazz = null;
                $scope.background = null;
                $scope.validating = false;  // reset validation
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
                $scope.character.raceObj.racialTraits = []; // reset
                if ($scope.race.traits) {
                    $scope.race.traits.forEach(function(trait) {
                        if (trait.benefit_desc) {
                            $scope.character.raceObj.racialTraits.push(new KeyValue(trait.id, trait.name, trait.benefit_desc));
                        }
                        if (trait.benefit_stat) {
                            features[trait.benefit_stat] = trait.benefit_value;
                        }
                    });
                }
                if ($scope.race.subrace.traits) {
                    $scope.race.subrace.traits.forEach(function(trait) {
                        if (trait.benefit_desc) {
                            $scope.character.raceObj.racialTraits.push(new KeyValue(trait.id, trait.name, trait.benefit_desc));
                        }
                        if (trait.benefit_stat) {
                            features[trait.benefit_stat] = trait.benefit_value;
                        }
                    });
                }

                $scope.character.determineRace();   // now takes care of languages
                //$scope.character.racialTraitIds = args.racialTraitIds;
            }
            //if ((args.clazz || $scope.clazz) && !args.subclass && !args.selectedFeature) {
            if (args.clazz || $scope.clazz) {
                $scope.clazz = args.clazz || $scope.clazz;
                if (args.clazz) {
                    $scope.character.classObj = $scope.clazz;   // used to determine if class contains subclasses
                    $scope.selectedFeature = null;    // reset
                    $scope.selectedFeatures = [];       // reset // for featureChoices
                    $scope.character.determineClass();
                }
                //$scope.character.className = args.clazz.name;
                if ($scope.clazz.features) {
                    $scope.character.classObj.classFeatures = [];    // reset    //$scope.character.classFeatures = [];    // reset
                    //$scope.character.featureIds = [];  // reset
                    $scope.character.classObj.subclasses = [];    // reset
                    $scope.clazz.features.forEach(function(featureObj, idx) {
                        if (featureObj.level <= $scope.character.level) {
                            var tempBenefit = '', classFeatureId = '';
                            if (featureObj.subclasses) {
                                $scope.character.classObj.subclasses = featureObj.subclasses;
                            }
                            if (featureObj.benefit) {
                                featureObj.benefit.forEach(function(benefitObj) {
                                    if (benefitObj.level <= $scope.character.level) {
                                        classFeatureId = benefitObj.id;
                                        tempBenefit = benefitObj.description;
                                    }
                                });
                                $scope.character.classObj.classFeatures.push(new KeyValue(classFeatureId, featureObj.name, tempBenefit));
                                tempBenefit = '';   // reset
                            }
                            // if parent feature is a choice, set $scope.featureChoices for dialog, else set subfeatures
                            if (featureObj.benefit_stat === 'feature_choice') {
                                $scope.character.classObj.featureChoices = featureObj.subfeatures;
                                if (args.clazz) {
                                    $scope.selectedFeatures[idx] = {'name': null};    // ng-repeat depends on this array
                                }
                            } else if (featureObj.subfeatures) {
                                featureObj.subfeatures.forEach(function(subfeature) {

                                    if (subfeature.cantrips) {
                                        $scope.character.classObj.cantrips = subfeature.cantrips;   // populate cantrips list
                                    }
                                    if (subfeature.benefit) {
                                        subfeature.benefit.forEach(function(benefitObj, idx) {
                                            if (benefitObj.level <= $scope.character.level) {
                                                classFeatureId = benefitObj.id;
                                                tempBenefit = benefitObj.description;
                                                if (subfeature.cantrips) {
                                                    $scope.character.classObj.numCantrips = parseInt(subfeature.benefit_value.split(', ')[idx]);
                                                }
                                            }
                                        });
                                        $scope.character.classObj.classFeatures.push(new KeyValue(classFeatureId, subfeature.name, tempBenefit));
                                        tempBenefit = '';   // reset
                                    }
                                });
                            }
                            if (featureObj.benefit_stat) {
                                features[featureObj.benefit_stat] = featureObj.benefit_value;
                            }
                        }
                    });
                    $scope.currentClassFeatures = $scope.character.classObj.classFeatures;
                }
            }
            if (args.subclass || $scope.subclass) {
                $scope.subclass = args.subclass || $scope.subclass;
                if (args.subclass) {
                    $scope.character.classObj.subclassObj = args.subclass;
                }
                var subclassFeatures = [];
                // TODO: change subclass property from benefit to features
                if ($scope.subclass.benefit) {
                    //for (var subclassProp in args.subclass.benefit) {
                    $scope.subclass.benefit.forEach(function(feature) {
                        if (feature.level <= $scope.character.level) {
                            var tempSubclassBenefit = '';
                            if (args.subclass && feature.benefit_stat === 'feature_choice') {    // if subclass is selected...
                                $scope.selectedFeatures.push({'name': null});
                            }
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
                    $scope.character.classObj.classFeatures = $scope.currentClassFeatures.concat(subclassFeatures);
                }
            }
            if (args.selectedFeature || !isNaN($scope.currentFeatureIdx)) {
                if (args.selectedFeature) { // for displaying feature name in button
                    $scope.currentFeatureIdx = !isNaN(args.featureIndex) ? args.featureIndex : $scope.currentFeatureIdx;
                    //$scope.selectedFeatures[$scope.currentFeatureIdx].name = args.selectedFeature.name;
                    //$scope.selectedFeatures[$scope.currentFeatureIdx].benefit = args.selectedFeature.benefit;

                    $scope.selectedFeatures[$scope.currentFeatureIdx] = {
                        name: args.selectedFeature.name,
                        benefit: args.selectedFeature.benefit
                    };
                }
                $scope.selectedFeatures.forEach(function(selectedFeature) {
                    if (selectedFeature.benefit) {
                        var tempFeatureChoice = '';
                        selectedFeature.benefit.forEach(function(feature) {
                            if (feature.level <= $scope.character.level) {
                                tempFeatureChoice = feature;
                            }
                        });
                        $scope.character.classObj.classFeatures.push(new KeyValue(tempFeatureChoice.id, selectedFeature.name, tempFeatureChoice.description));
                    }
                });
            }
            if (args.background || $scope.background) {
                $scope.background = args.background || $scope.background;
                $scope.character.background = $scope.background;
            }
            if (args.selectedCantrips) {
                $scope.character.classObj.selectedCantrips = args.selectedCantrips.split(', ');
            }

            $scope.character.handleTools();
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

function DialogNewCharController($scope, $modalInstance) {
    $scope.character = {
        level: 1
    };
    $scope.done = function() {
        var level = parseInt($scope.character.level);
        $scope.$emit('handleEmit', {level: level, checked: true});
        $modalInstance.close();
    };
    $scope.close = function() {
        $modalInstance.dismiss('cancel');
    };
}

function DialogRaceController($scope, $modalInstance, raceData) {
    $scope.title = 'Select Race';
    $scope.races = raceData;
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempClass = '';
    $scope.disabled = true;

    $scope.showDescription = function(selectobj) {
        $scope.raceObj = selectobj.race;
        $scope.selectedIndex = selectobj.$index;
        var subrace;
        $scope.tempRace = '';
        $scope.featureType = 'Race Traits';
        $scope.size = '';
        $scope.speed = '';
        $scope.traits = [];
        $scope.features = [];
        for (var i=0; i<$scope.races.length; i++) {
            subrace = $scope.races[i].subrace;
            /*if (!subrace) {
                subrace = race;
            }*/
            if (subrace.name === $scope.raceObj.subrace.name) {   // selected the right subrace
                $scope.disabled = false;
                $scope.tempRace = $scope.raceObj.subrace;
                $scope.description = subrace.desc;
                //$scope.race_aba = raceObj.ability_score_adjustment;   // was for dialog UI
                //$scope.subrace_aba = subrace.ability_score_adjustment;
                $scope.size = $scope.raceObj.size;
                $scope.speed = $scope.raceObj.speed;
                //$scope.languages = raceObj.languages;
                $scope.racialTraitIds = [];
                for (var trait in $scope.raceObj.traits) {    // race traits
                    if ($scope.raceObj.traits.hasOwnProperty(trait)) {
                        var raceTrait = $scope.raceObj.traits[trait];
                        $scope.traits.push(new KeyValue(raceTrait.id, raceTrait.name, raceTrait.description,
                            raceTrait.benefit, raceTrait.benefit_value, raceTrait.per_level));
                        $scope.racialTraitIds.push($scope.raceObj.traits[trait].id);
                    }
                }
                for (var subRaceTrait in subrace.traits) { // subrace traits
                    if (subrace.traits.hasOwnProperty(subRaceTrait)) {
                        var subraceTrait = subrace.traits[subRaceTrait];
                        $scope.traits.push(new KeyValue(subraceTrait.id, subraceTrait.name, subraceTrait.description,
                            subraceTrait.benefit, subraceTrait.benefit_value, subraceTrait.per_level));
                        $scope.racialTraitIds.push(subrace.traits[subRaceTrait].id);
                    }
                }
                break;
            }
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

function DialogBackgroundController($scope, $modalInstance, backgroundData) {
    $scope.title = 'Select Background';
    var data = backgroundData;
    $scope.values = [];     // clears the data
    for (var i=0; i<data.length; i++) {
        $scope.values.push(data[i]);
    }
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.disabled = true;
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
                $scope.disabled = false;
                $scope.tempBackground = selectobj.value;
                value = $scope.values[i];
                $scope.description = value.desc;
                $scope.traitName = value.trait_name;
                $scope.traitDesc = value.trait_desc;
                $scope.skills = value.skills;
                $scope.tools = value.tools;
                $scope.languages = value.language_desc;
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

// the dialog is injected in the specified controller
function DialogClassController($scope, $modalInstance, classData){
    var data = classData;
    $scope.values = [];     // clears the data
    for (var i=0; i<data.length; i++) {
        $scope.values.push(data[i]);
    }
    $scope.title = 'Select Class';
    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempClass = '';
    $scope.disabled = true;

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        $scope.tempClass = null;
        $scope.featureType = 'Class Features';
        $scope.traits = [], $scope.traits2 = [];
        $scope.features = [];
        //for (var i=0; i<$scope.values.length; i++) {
        $scope.values.forEach(function(value) {
            if (value.name === selectobj.value.name) {
                $scope.disabled = false;
                $scope.tempClass = selectobj.value; // object
                $scope.description = value.desc;
                $scope.traitsTitle = "Hit Points";
                $scope.traits.push(new NameDesc("Hit Dice", "1d" + value.hit_dice + " per " + value.name + " level"),
                    new NameDesc("Hit Points at 1st Level", value.hit_dice + " + your Constitution modifier"),
                    new NameDesc("Hit Points at Higher Levels", "1d" + value.hit_dice + " + your Constitution modifier per " +
                        value.name + " level after 1st"));
                $scope.traits2Title = "Proficiencies";
                $scope.traits2.push(new NameDesc("Armor", value.armor_prof), new NameDesc("Weapons", value.weapon_prof),
                    new NameDesc("Tools", value.tools), new NameDesc("Saving Throws", value.saving_throws),
                    new NameDesc("Skills", value.avail_skills_desc));
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
    $scope.disabled = true;

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        var value;
        $scope.tempSubclass = '';
        $scope.featureType = 'Class Features';
        $scope.traits = [];
        $scope.features = [];
        for (var i=0; i<$scope.values.length; i++) {
            if ($scope.values[i].name === selectobj.value.name) {
                $scope.disabled = false;
                $scope.tempSubclass = selectobj.value;
                value = $scope.values[i];
                $scope.description = value.desc;
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
// TODO: move somewhere else
Array.prototype.getIndexBy = function (name, value) {
    for (var i = 0; i < this.length; i++) {
        if (this[i][name] == value) {
            return i;
        }
    }
    return -1;
};
// TODO: support multiple selections
function DialogFeatureController($scope, $modalInstance, features, index, selectedFeatures, featureName) {
    $scope.title = 'Select Feature';
    $scope.values = features;

    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempFeature = '';
    $scope.index = index;
    $scope.disabled = true;
    var takenFeatures = angular.copy(selectedFeatures);
    takenFeatures.splice(selectedFeatures.getIndexBy('name', featureName), 1);

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        var value;
        $scope.tempFeature = '';
        $scope.features = [];
        for (var i=0; i<$scope.values.length; i++) {
            value = angular.copy($scope.values[i]);
            if (value.name === selectobj.value.name) {
                $scope.tempFeature = selectobj.value;
                if (takenFeatures.getIndexBy('name', value.name) !== -1) {
                    $scope.disabled = true;
                    $scope.tempFeature.alreadyTaken = true;
                    value.desc += '<div class="text-danger">Already Taken</div>';
                } else {
                    $scope.disabled = false;
                }
                $scope.features.push(new KeyValue(value.id, value.name, value.desc));
                break;
            }
        }
    };

    $scope.done = function() {
        if ($scope.tempFeature && !$scope.tempFeature.alreadyTaken) {
            $scope.$emit('handleEmit', {selectedFeature: $scope.tempFeature, checked: true, featureIndex: $scope.index});
            $modalInstance.close();
        } else if ($scope.tempFeature.alreadyTaken) {
            alert("This feature is already taken");
        } else {
            alert("Please select a feature");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogCantripController($scope, $modalInstance, cantrips, numCantrips) {
    $scope.title = 'Select Cantrips';
    $scope.values = cantrips;

    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.tempCantrips = [];   // ex: ['Ray of Frost', 'Shocking Grasp']
    $scope.disabled = true;
    $scope.spellsLeft = angular.copy(numCantrips);
    //$scope.selectedCantrip = {};

    $scope.showDescription = function(selectobj) {
        $scope.selectedIndex = selectobj.$index;
        $scope.selectedCantrip = selectobj.spell;
        var value;
        for (var i=0; i<$scope.values.length; i++) {
            value = angular.copy($scope.values[i]);
            if (value.name === $scope.selectedCantrip.name) {
                if (!$scope.selectedCantrip.active && $scope.spellsLeft - $scope.tempCantrips.length > 0) {
                    $scope.tempCantrips.push($scope.selectedCantrip.name);
                    $scope.selectedCantrip.active = true;
                } else if ($scope.selectedCantrip.active) {
                    $scope.tempCantrips.splice($scope.tempCantrips.indexOf($scope.selectedCantrip.name), 1); // remove cantrip
                    $scope.selectedCantrip.active = false;
                }
                break;
            }
        }
        $scope.disabled = $scope.spellsLeft - $scope.tempCantrips.length !== 0; // disabled is true if there are still cantrips left to choose
    };
    $scope.done = function() {
        var tempCantrips = '';
        if ($scope.tempCantrips) {
            // convert tempCantrips to sorted string
            $scope.tempCantrips.sort();
            tempCantrips = $scope.tempCantrips.join(', ');
            $scope.$emit('handleEmit', {selectedCantrips: tempCantrips, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a feature");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogSummaryController($scope, $modalInstance, character) {
    var ABILITIES = ['str', 'dex', 'con', 'int', 'wis', 'cha'];
    var ABILITY_MAPPER = {'str':'Strength', 'dex':'Dexterity', 'con':'Constitution', 'int':'Intelligence', 'wis':'Wisdom', 'cha':'Charisma'};
    function addPlusSign(val) {
        if (val >= 0) {
            val = '+' + val;
        }
        return val;
    }
    character.hitPoints = character.classObj ? character.hitPoints + ' (' + character.level + 'd' + character.classObj.hit_dice + ')' : '';
    character.initiative = addPlusSign(character.initiative);
    character.speed = character.speed ? character.speed + ' feet' : '';
    character.profBonus = addPlusSign(character.profBonus);
    ABILITIES.forEach(function(ability) {
        character.ability[ability].mod = addPlusSign(character.ability[ability].mod);
        character.ability[ability].savingThrow = addPlusSign(character.ability[ability].savingThrow);
    });
    character.skillsArr = [];
    character.skills.forEach(function(skill) {
        character.skillsArr.push(skill.name + ' ' + (addPlusSign(skill.val)));
    });
    if (character.classObj && character.classObj.spellcasting) {
        character.classObj.spellcasting.spellAbility = ABILITY_MAPPER[character.classObj.spellcasting.spellAbility];
        if (character.classObj.selectedCantrips) {
            character.classObj.selectedCantrips = character.classObj.selectedCantrips.join(', ');
        }
    }

    $scope.character = character;
    $scope.close = function() {
        $modalInstance.dismiss('cancel');
    }
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
