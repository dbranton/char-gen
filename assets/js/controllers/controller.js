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
    //$scope.selectedSpells = [];
    //$scope.bonusSelectedSpells = [];
    var opts = {
        backdrop: true,
        keyboard: true,
        backdropClick: true,
        templateUrl: 'dialog'
    };
    $scope.numLanguagesLeft = 0;

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
                        $scope.errorMessage = data; // html string
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

    $scope.openFeatureDialog = function(selectedFeature, type) {
        opts.templateUrl = 'dialog/multipleDialog'; //'dialog/classDialog';
        opts.controller = DialogFeatureController;
        opts.resolve = {
            features: function() { return angular.copy(selectedFeature.choices); },    //$scope.character.classObj.featureChoices
            index: function() { return selectedFeature.index; },
            type: function() { return type; },
            //selectedFeatures: function() { return $scope.character.selectedFeatures; },
            max: function() { return selectedFeature.max; }
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

    $scope.openSubclassCantripDialog = function() {
        opts.templateUrl = 'dialog/spellDialog';
        opts.controller = DialogCantripController;
        opts.resolve = {
            cantrips: function() { return angular.copy($scope.character.classObj.subclassObj.cantrips); },
            numCantrips: function() { return $scope.character.classObj.numCantrips; }
        };
        openDialog();
    };

    $scope.openBonusCantripDialog = function() {
        opts.templateUrl = 'dialog/spellDialog';
        opts.controller = DialogCantripController;
        opts.resolve = {
            cantrips: function() { return angular.copy($scope.character.raceObj.cantrips); },
            numCantrips: function() { return 1; }
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

    /*$scope.$watch('isMobile', function(newVal) {
       if (newVal) {
           $('.select2-input').prop('readonly', true);  // doesn't work
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

    $scope.broadcastObj = function(arr, name, prop) {
        var obj = {};
        if (name) {
            for (var i=0, ilen=arr.length; i<ilen; i++) {
                if ((prop === 'race' && arr[i].subrace.name === name) || arr[i].name === name) { // assuming arr has a name property
                    obj.checked = true;
                    obj[prop] = angular.copy(arr[i]);
                    $scope.$broadcast('handleBroadcast', obj);
                    break;
                }
            }
        }
    };
    $scope.broadcastArray = function(selectedObj, prop, type) { // for selecting (multiple) feature choices
        var selectedArr = [], broadcastObj = {checked: true};
        if (angular.isArray(selectedObj.name) && angular.isArray(selectedObj.choices)) {
            angular.forEach(selectedObj.choices, function(obj) {
                angular.forEach(selectedObj.name, function(item) {
                    if (item === obj.name) {
                        selectedArr.push(obj);
                    }
                });
            });
            broadcastObj[prop] = selectedArr;
            broadcastObj.featureIndex = selectedObj.index;
            broadcastObj.type = type;
            $scope.$broadcast('handleBroadcast', broadcastObj);
        }
    };
    $scope.broadcastNonObj = function(name, prop) {  // for selecting cantrips (and maybe languages)
        var string = name;  // for cantrips, its an array, for bonus cantrip, its a string
        var obj = {
            checked: true
        };
        obj[prop] = string;
        $scope.$broadcast('handleBroadcast', obj);
    };

    var getFeatureChoices = function(choices, level, type, index) {  // type is 'classArr' or 'subclassArr'
        var tempArr = [];
        angular.forEach(choices, function(feature) {
            if (feature.level <= level && feature.benefit_stat !== 'bonus_feature') {
                tempArr.push(feature);
            } else if (feature.benefit_stat === 'bonus_feature') {   // ex: Elemental Attunement
                var broadcastObj = {checked: true}; // simulate selecting a bonus feature
                broadcastObj.selectedFeatures = [feature];
                broadcastObj.featureIndex = index;
                broadcastObj.type = type;
                $scope.$broadcast('handleBroadcast', broadcastObj);
                $scope.character.classObj.subclassObj.bonusFeature = feature;
                $scope.character.classObj.subclassObj.bonusFeature.type = type;
                $scope.character.classObj.subclassObj.bonusFeature.index = index;
            }
        });
        return tempArr;
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
                    var tempName;
                    angular.forEach($scope.race.traits, function(trait) {
                        if (trait.benefit_desc && trait.level <= $scope.character.level) {
                            if (!tempName || tempName !== trait.name) {
                                $scope.character.raceObj.racialTraits.push(new KeyValue(trait.id, trait.name, trait.benefit_desc));
                                tempName = trait.name;
                            } else {
                                $scope.character.raceObj.racialTraits[$scope.character.raceObj.racialTraits.getIndexBy('name', tempName)] = new KeyValue(trait.id, trait.name, trait.benefit_desc);
                            }
                        }
                        if (trait.benefit_stat) {
                            features[trait.benefit_stat] = trait.benefit_value;
                        }
                        /*if (trait.cantrip) {
                            $scope.character.raceObj.cantrip = trait.cantrip;
                        }*/
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
                        if (trait.cantrip) {
                            $scope.character.raceObj.cantrip = trait.cantrip;
                        }
                        if (trait.cantrips && !args.selectedBonusCantrip) {    // trait.cantrips is the cantrip list
                            $scope.character.raceObj.cantrips = angular.copy(trait.cantrips);   // populate cantrips list
                        }
                    });
                }

                // account for spellcaster's cantrips coinciding with High Elf's bonus cantrip
                if (args.selectedCantrips && $scope.character.classObj.selectedCantrips &&
                        $scope.character.raceObj.cantrips) {
                    for (var i=0, ilen=$scope.character.classObj.selectedCantrips.length; i<ilen; i++) {
                        if ($scope.character.raceObj.cantrips.getIndexBy('name', $scope.character.classObj.selectedCantrips[i]) !== -1) {
                            $scope.character.raceObj.cantrips.splice($scope.character.raceObj.cantrips.getIndexBy('name', $scope.character.classObj.selectedCantrips[i]), 1);  // filter out selected cantrips (if any) from bonus cantrips list
                        }
                    }
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
                    //$scope.character.selectedFeatures = [];       // reset // for featureChoices
                    $scope.character.classObj.selectedFeatures = [];    // reset
                    $scope.selectedChoices = {classArr: [], subclassArr: []}; //[];
                    $scope.character.determineClass();
                }
                if (args.subclass) {
                    $scope.character.classObj.expertise = null; // clear expertise if subclass changed (Knowledge Domain)
                }
                //$scope.character.className = args.clazz.name;
                if (angular.isArray($scope.clazz.features)) {
                    $scope.character.classObj.classFeatures = [];    // reset    //$scope.character.classFeatures = [];    // reset
                    //$scope.character.featureIds = [];  // reset
                    $scope.character.classObj.subclasses = [];    // reset
                    var featureChoiceIdx = 0;   // reset
                    angular.forEach($scope.clazz.features, function(featureObj, idx) {
                        if (featureObj.level <= $scope.character.level) {
                            var tempBenefit = '';
                            if (featureObj.subclasses) {
                                $scope.character.classObj.subclassName = featureObj.name;
                                $scope.character.classObj.subclasses = featureObj.subclasses;
                            }
                            if (angular.isArray(featureObj.benefit)) {
                                angular.forEach(featureObj.benefit, function(benefitObj) {
                                    if (benefitObj.level <= $scope.character.level) {
                                        tempBenefit = benefitObj;
                                    }
                                });
                                if (tempBenefit.description !== '') {   // adds benefits that have descriptions to classFeatures list
                                    $scope.character.classObj.classFeatures.push(new KeyValue(tempBenefit.id, featureObj.name, tempBenefit.description));
                                }
                                if (tempBenefit.benefit_stat) {
                                    features[tempBenefit.benefit_stat] = tempBenefit.benefit_value;
                                    // give dynamic name for expertise label
                                    if ((args.clazz || args.subclass) && tempBenefit.benefit_stat === 'expertise') {
                                        $scope.character.classObj.expertise = {};
                                        $scope.character.classObj.expertise.label = featureObj.name;
                                    }
                                }
                                // handle features that provide choices
                                if (tempBenefit.benefit_stat === 'feature_choice') {
                                    if (args.clazz) {
                                        $scope.character.classObj.selectedFeatures[featureChoiceIdx] = {'name': [], 'max': tempBenefit.benefit_value, 'label': featureObj.name, 'choices': getFeatureChoices(featureObj.subfeatures, $scope.character.level, 'classArr', featureChoiceIdx), 'index': featureChoiceIdx}
                                        featureChoiceIdx++;
                                    } else if ($scope.clazz) {  //to account for additional fighting style changing classObj.selectedFeatures
                                        $scope.character.classObj.selectedFeatures[featureChoiceIdx].max =
                                            $scope.character.classObj.selectedFeatures[featureChoiceIdx].name.length = tempBenefit.benefit_value;
                                        if ($scope.selectedChoices['classArr'].length > 0) {
                                            $scope.selectedChoices['classArr'][featureChoiceIdx].length = tempBenefit.benefit_value;
                                        }
                                    }
                                } else if (angular.isArray(featureObj.subfeatures)) {    // ignore feature_choices
                                    featureObj.subfeatures.forEach(function(subfeature) {
                                        if (subfeature.cantrips && !args.selectedCantrips) {    // make sure to not execute when selecting cantrips
                                            $scope.character.classObj.cantrips = angular.copy(subfeature.cantrips);   // populate cantrips list
                                        }
                                        if (angular.isArray(subfeature.benefit)) {
                                            angular.forEach(subfeature.benefit, function(benefitObj, idx) {
                                                if (benefitObj.level <= $scope.character.level) {
                                                    tempBenefit = benefitObj;
                                                }
                                            });
                                            if (tempBenefit.description !== '') {   // adds benefits that have descriptions to classFeatures list
                                                $scope.character.classObj.classFeatures.push(new KeyValue(tempBenefit.id, subfeature.name, tempBenefit.description));
                                            }
                                            if (tempBenefit.benefit_stat) {
                                                features[tempBenefit.benefit_stat] = tempBenefit.benefit_value;
                                            }
                                            tempBenefit = {};   // reset
                                        }
                                    });
                                }
                                tempBenefit = {};   // reset
                            }
                        }
                    });
                    $scope.currentClassFeatures = $scope.character.classObj.classFeatures;
                }
                // keeps high elf's bonus cantrip from coinciding with spellcaster's cantrips
                if ($scope.character.raceObj.cantrip &&
                    ($scope.character.classObj.cantrips && $scope.character.classObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip) !== -1)) {
                    $scope.character.classObj.cantrips.splice($scope.character.classObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip), 1);  // remove bonus cantrip (if any) from cantrips list
                } else if ($scope.character.classObj.subclassObj && $scope.character.classObj.subclassObj.cantrips &&
                        $scope.character.classObj.subclassObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip) !== -1) {
                    $scope.character.classObj.subclassObj.cantrips.splice($scope.character.classObj.subclassObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip), 1);  // remove bonus cantrip (if any) from subclass cantrips list
                }
            }
            if (args.subclass || $scope.character.classObj.subclassObj) {
                if (args.subclass) {
                    $scope.character.classObj.subclassObj = args.subclass;
                    $scope.character.classObj.subclassObj.selectedFeatures = [];    // reset
                    $scope.selectedChoices.subclassArr = [];    // reset
                    $scope.character.classObj.spellcasting = null;  //test this; possible flicker effect
                    //$scope.character.resetSkills();
                }
                var subclassFeatures = [];
                // TODO: change subclass property from benefit to features
                if ($scope.character.classObj.subclassObj.benefit) {
                    var subclassFeatureChoiceIdx = 0;   // reset
                    //for (var subclassProp in args.subclass.benefit) {
                    angular.forEach($scope.character.classObj.subclassObj.benefit, function(feature) {
                        if (feature.level <= $scope.character.level) {
                            var tempSubclassBenefit = '';
                            if (angular.isArray(feature.benefit)) {
                                angular.forEach(feature.benefit, function(benefit) {
                                    if (benefit.level <= $scope.character.level) {
                                        tempSubclassBenefit = benefit;
                                    }
                                    if (tempSubclassBenefit.benefit_stat) {
                                        features[tempSubclassBenefit.benefit_stat] = tempSubclassBenefit.benefit_value;
                                        // give dynamic name for expertise label
                                        if (args.subclass && tempSubclassBenefit.benefit_stat.indexOf('selected_expertise') !== -1) {
                                            $scope.character.classObj.expertise = {};
                                            $scope.character.classObj.expertise.label = feature.name;
                                        }
                                    }
                                });
                                if (tempSubclassBenefit.description !== '') {
                                    subclassFeatures.push(new KeyValue(tempSubclassBenefit.id, feature.name, tempSubclassBenefit.description));
                                }
                                /*if (tempSubclassBenefit.benefit_stat) {
                                    features[tempSubclassBenefit.benefit_stat] = tempSubclassBenefit.benefit_value;
                                }*/
                            }
                            if (args.subclass && tempSubclassBenefit.benefit_stat === 'feature_choice') {
                                //$scope.character.classObj.featureChoices = feature.subfeatures;
                                $scope.character.classObj.subclassObj.selectedFeatures[subclassFeatureChoiceIdx] = {'name': [], 'max': tempSubclassBenefit.benefit_value, 'label': feature.name, 'choices': getFeatureChoices(feature.subfeatures, $scope.character.level, 'subclassArr', subclassFeatureChoiceIdx), 'index': subclassFeatureChoiceIdx};    // ng-repeat depends on this array
                                subclassFeatureChoiceIdx++;
                            }
                            // handle subfeatures
                            else if (angular.isArray(feature.subfeatures)) {
                                angular.forEach(feature.subfeatures, function(subfeature) {
                                    if (subfeature.cantrips && !args.selectedCantrips) {    // make sure to not execute when selecting cantrips
                                        $scope.character.classObj.subclassObj.cantrips = angular.copy(subfeature.cantrips);   // populate cantrips list
                                    }
                                    if (angular.isArray(subfeature.benefit)) {
                                        angular.forEach(subfeature.benefit, function(benefit) {
                                            if (benefit.level <= $scope.character.level) {
                                                tempSubclassBenefit = benefit;
                                            }
                                        });
                                        features[tempSubclassBenefit.benefit_stat] = tempSubclassBenefit.benefit_value;
                                        if (tempSubclassBenefit.description !== '') {
                                            subclassFeatures.push(new KeyValue(tempSubclassBenefit.id, subfeature.name, tempSubclassBenefit.description));
                                        }
                                    }
                                    // filter out coinciding cantrips between high elf's bonus cantrip and cantrip list
                                    if ($scope.character.raceObj.cantrip &&
                                            $scope.character.classObj.subclassObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip) !== -1) {
                                        $scope.character.classObj.subclassObj.cantrips.splice($scope.character.classObj.subclassObj.cantrips.getIndexBy('name', $scope.character.raceObj.cantrip), 1);
                                    }
                                });
                            }
                        }
                    });

                }
            }

            // Handle feature choices
            if (args.selectedFeatures && !isNaN(args.featureIndex)) {
                if ($scope.character.classObj.subclassObj && $scope.character.classObj.subclassObj.bonusFeature &&
                    args.type === $scope.character.classObj.subclassObj.bonusFeature.type &&
                    args.featureIndex === $scope.character.classObj.subclassObj.bonusFeature.index) {
                    args.selectedFeatures.unshift($scope.character.classObj.subclassObj.bonusFeature);
                }
                $scope.selectedChoices[args.type][args.featureIndex] = args.selectedFeatures;
            }
            if ($scope.character.classObj.classFeatures) {
                addFeatures($scope, 'classArr', args.featureIndex);
                if ($scope.character.classObj.subclassObj) {
                    addFeatures($scope, 'subclassArr', args.featureIndex);
                    $scope.character.classObj.classFeatures = $scope.currentClassFeatures.concat(subclassFeatures);
                }
            }
            if (args.background || $scope.background) {
                $scope.background = args.background || $scope.background;
                $scope.character.background = $scope.background;
            }
            if (args.selectedCantrips) {
                $scope.character.classObj.selectedCantrips = args.selectedCantrips; //.split(', ');
                $scope.character.classObj.selectedClassCantrips = angular.copy($scope.character.classObj.selectedCantrips);
            }
            if (args.selectedBonusCantrip) {
                $scope.character.raceObj.cantrip = args.selectedBonusCantrip;
            }

            $scope.character.handleTools();
            $scope.character.numLanguages = $scope.character.background ? parseInt($scope.character.background.languages) : 0;
            $scope.character.calculateModifiers(); // update ability modifiers

            // needs to be at the very end to alter existing properties
            if (!args.level && !args.selectedFeatures && !args.selectedCantrips && !args.selectedBonusCantrip) {
                $scope.character.resetSkills();
                $scope.character.handleSkills();
            }
            $scope.character.handleFeatureBonuses(features);
            args.checked = false;
        }
    });
}

// TODO: possibly move to service
function addFeatures($scope, type, featureIndex) {    // classArr or subclassArr
    if (angular.isArray($scope.selectedChoices[type])) {
        var selectedFeaturesArray = []; // expected outcome ex: [Distant Spell, Heighten Spell]
        var featureChoiceName = '';
        angular.forEach($scope.selectedChoices[type], function(selectedFeatures, idx) {
            angular.forEach(selectedFeatures, function(selectedFeature) {
                if (selectedFeature.benefit) {
                    var tempFeatureChoice = '';
                    angular.forEach(selectedFeature.benefit, function(feature) {
                        if (feature.level <= $scope.character.level) {
                            tempFeatureChoice = feature;
                        }
                    });
                    if (idx === featureIndex) {
                        selectedFeaturesArray.push(selectedFeature.name);
                    }
                    if (selectedFeature.parent_name) {
                        featureChoiceName = selectedFeature.parent_name + ' (' + selectedFeature.name + ')';
                    } else {
                        featureChoiceName = selectedFeature.name;
                    }
                    $scope.character.classObj.classFeatures.push(new KeyValue(tempFeatureChoice.id, featureChoiceName, tempFeatureChoice.description));
                }
            });
        });
        if (!isNaN(featureIndex)) {
            if (type === 'classArr' && $scope.character.classObj.selectedFeatures[featureIndex]) {
                $scope.character.classObj.selectedFeatures[featureIndex].name = selectedFeaturesArray;
            } else if (type === 'subclassArr' && $scope.character.classObj.subclassObj.selectedFeatures[featureIndex]) {
                $scope.character.classObj.subclassObj.selectedFeatures[featureIndex].name = selectedFeaturesArray;
            }
        }
    }
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
// TODO: support multiple selections
function DialogFeatureController($scope, $modalInstance, features, index, type, max) {    // selectedFeatures
    $scope.title = 'Select Feature';
    $scope.values = features;

    $scope.description = 'Click a list item to view more information';
    $scope.featureType = '';
    $scope.features = [];
    $scope.tempFeatures = [];
    $scope.disabled = true;
    $scope.max = parseInt(max);
    //var takenFeatures = angular.copy(selectedFeatures);
    //takenFeatures.splice(selectedFeatures.getIndexBy('name', featureName), 1);

    $scope.showDescription = function(selectobj) {
        $scope.selectedFeature = selectobj.value;
        var value;
        for (var i=0; i<$scope.values.length; i++) {
            value = angular.copy($scope.values[i]);
            if (value.name === selectobj.value.name) {
                if (!$scope.selectedFeature.active && $scope.max - $scope.tempFeatures.length > 0) {
                    $scope.tempFeatures.push($scope.selectedFeature);
                    $scope.selectedFeature.active = true;
                } else if ($scope.selectedFeature.active) {
                    $scope.tempFeatures.splice($scope.tempFeatures.getIndexBy('name', $scope.selectedFeature.name), 1); // remove cantrip
                    $scope.selectedFeature.active = false;
                }
                break;
            }
        }
        $scope.disabled = $scope.max - $scope.tempFeatures.length !== 0; // disabled is true if there are still features left to choose
    };

    $scope.done = function() {
        var tempFeatures = '';
        if (angular.isArray($scope.tempFeatures)) {
            // convert tempCantrips to sorted string
            $scope.tempFeatures.sort();
            //tempFeatures = $scope.tempFeatures.join(', ');
            $scope.$emit('handleEmit', {selectedFeatures: $scope.tempFeatures, featureIndex: index, type: type, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select your features");
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
        if (angular.isArray($scope.tempCantrips)) {
            // convert tempCantrips to sorted string
            $scope.tempCantrips.sort();
            tempCantrips = $scope.tempCantrips.join(', ');
            if (numCantrips === 1) {    // assume if numCantrips is 1, then it is for bonus cantrip
                $scope.$emit('handleEmit', {selectedBonusCantrip: tempCantrips, checked: true});
            } else {
                $scope.$emit('handleEmit', {selectedCantrips: $scope.tempCantrips, checked: true});
            }
            $modalInstance.close();
        } else {
            alert("Please select your cantrips");   // should be impossible to get here since button is disabled
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogSummaryController($scope, $modalInstance, character) {
    var ABILITIES = ['str', 'dex', 'con', 'int', 'wis', 'cha'];
    var ABILITY_MAPPER = {'str':'Strength', 'dex':'Dexterity', 'con':'Constitution', 'int':'Intelligence', 'wis':'Wisdom', 'cha':'Charisma'};
    var index = 0;
    function addPlusSign(val) {
        if (val >= 0) {
            val = '+' + val;
        }
        return val;
    }
    var bonusHP = character.bonusHP >= 0 ? '+' + character.bonusHP : character.bonusHP;
    character.hitPointsDesc = character.classObj.hit_dice ? character.hitPoints + ' (' + character.level + 'd' + character.classObj.hit_dice + bonusHP + ')' : '';
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
    if (character.raceObj.spellcasting) {
        character.raceObj.spellcasting.spellAbility = ABILITY_MAPPER[character.raceObj.spellcasting.spellAbility];
        character.raceObj.spellcasting.spellAttkBonus = '+' + character.raceObj.spellcasting.spellAttkBonus;
    }
    if (character.classObj.spellcasting) {
        character.classObj.spellcasting.spellAbility = ABILITY_MAPPER[character.classObj.spellcasting.spellAbility];
        character.classObj.spellcasting.spellAttkBonus = '+' + character.classObj.spellcasting.spellAttkBonus;
        /*if (!character.classObj.selectedCantrips) {
             character.classObj.selectedCantrips = [];
        }
        if (character.raceObj.cantrip) {
            character.classObj.selectedCantrips.push(character.raceObj.cantrip);
            character.classObj.selectedCantrips.sort();
        }*/
        if (character.classObj.selectedCantrips) {
            character.classObj.selectedCantrips = character.classObj.selectedCantrips.join(', ');   // convert to string
        }
        if (character.classObj.selectedSpells) {
            character.classObj.selectedSpellsByLevel = [];
            angular.forEach(character.classObj.selectedSpells, function(spell) {
                index = parseInt(spell.level) - 1;
                if (character.classObj.selectedSpellsByLevel[index]) {
                    character.classObj.selectedSpellsByLevel[index].push(spell.name)
                } else {
                    character.classObj.selectedSpellsByLevel[index] = [spell.name];
                }
            });
            angular.forEach(character.classObj.selectedSpellsByLevel, function(spells, idx, arr) {
                spells.sort();
                arr[idx] = spells.join(', ');
            });
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
