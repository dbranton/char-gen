'use strict';

var charModule = angular.module('generator', ['ngSanitize', 'ui.bootstrap.modal', 'charGenService', 'ui.select2']);

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
function CharGen($scope, $modal, $http, $sanitize, charGenFactory) {
    $scope.Math = window.Math;
    $scope.character = {
        level: 1,
        name: null,
        raceObj: null,
        classObj: null, // contains subclasses property
        background: null,   // & background.skills
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
    };
    $scope.racialBonus = {};
    $scope.character.profBonus = charGenFactory.determineProficiencyBonus($scope.character.level);
    var minAbility = 8;
    var maxAbility = 20;

    $scope.searchText = '';

    $scope.filterByName = function(value) {
        if (value.name) {
            return value.name.toLowerCase().indexOf($scope.searchText.toLowerCase()) !== -1;
        }
    };

    // Initialize variables
    $scope.character.ability = {
        str: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        dex: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        con: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        int: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        wis: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        cha: {
            score: 10,
            mod: 0,
            bonus: false,
            raceBonus: 0
        },
        bonusAbility: null,
        pointsLeft: 18
    };

    $scope.opts = {
        backdrop: true,
        keyboard: true,
        backdropClick: true,
        templateUrl: 'dialog'
    };

    $scope.openDialog = function() {
        var dialog = $modal.open($scope.opts);
        //dialog.open();

        /*dialog.result.then(function (selectedItem) {
           console.log(selectedItem);
        }, function() {
            alert('test');
        });*/
    };

    $scope.openRaceDialog = function() {
        $scope.opts.templateUrl = 'raceModal.html'; //'dialog/raceDialog';
        $scope.opts.controller = DialogRaceController;
        $scope.opts.resolve = {
            raceData: function() { return angular.copy($scope.raceData); }
        };
        $scope.openDialog();
    };

    $scope.openClassDialog = function() {
        $scope.opts.templateUrl = 'classModal.html';
        $scope.opts.controller = DialogClassController;
        $scope.opts.resolve = {
            classData: function() { return angular.copy($scope.classData); }
        };
        $scope.openDialog();
    };

    $scope.openSubclassDialog = function() {
        $scope.opts.templateUrl = 'classModal.html';
        $scope.opts.controller = DialogSubclassController;
        $scope.opts.resolve = {
            character: function() { return angular.copy($scope.character); }
        };
        $scope.openDialog();
    };

    $scope.openBackgroundDialog = function() {
        $scope.opts.templateUrl = 'backgroundModal.html';
        $scope.opts.controller = DialogBackgroundController;
        $scope.opts.resolve = {
            backgroundData: function() { return angular.copy($scope.backgroundData); }
        };
        $scope.openDialog();
    };

    $scope.selectedSkills = '';
    //$scope.numLanguages = 0;

    // Deprecated
    $scope.openAbilityScoreDialog = function() {
        $scope.opts.templateUrl = 'abilityModal.html';  //'dialog/abilityscore';
        $scope.opts.controller = DialogAbilityController;
        $scope.opts.resolve = {
            ability: function() { return angular.copy($scope.character.ability); }
        }
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
    $scope.$watch('selectedSkills', function(newValue, oldValue) {
        if (newValue) {
            var numSkills = parseInt($scope.character.classObj.num_skills), skillsLeft;
            if (newValue.length <= numSkills) {
                if ($scope.character.background) {
                    handleSkills(); // newValue
                } else {
                    $scope.character.skills = newValue.join(", ");
                }
            }
        }
    });
    $scope.$watch('selectedLanguages', function(newValue, oldValue) {
       if ((newValue || oldValue) && $scope.character.raceObj) {
            handleLanguages();
       }
    });
    $scope.$watch('character.raceObj.name', function(newValue, oldValue) {
       if (newValue) {
           $scope.selectedLanguages = []; // reset
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
    $scope.$watch('character.ability.dex.mod', function(newValue, oldValue) {
        if (!isNaN(newValue)) {
            $scope.character.initiative = newValue;
            $scope.character.armorClass = 10 + newValue;
        }
    });
    $scope.$watch('character.numLanguages', function(newValue, oldValue) {
        if (angular.isDefined(newValue) && newValue >= 0 && $scope.character.background) {
            $scope.select2Languages = newValue;
            $scope.selectedLanguages.length = newValue; //determineNumItems('#chosenLanguages', newValue);
            handleLanguages(); //TEST THIS SOME MORE
        }
    })

    // determines points left
    $scope.incrementAbility = function(ability, value) {    // value can only be 1 or -1
        var currValue = $scope.character.ability[ability].score, abilityCost,
            pointsLeft = $scope.character.ability.pointsLeft;
        if (pointsLeft > 0 && (value > 0 && currValue < maxAbility) || (value < 0 && currValue > minAbility)) {
            if ((value > 0 && currValue >= 13 && pointsLeft <= 1) ||
                (value > 0 && currValue >= 15 && pointsLeft <= 2)) {
                // do nothing
            } else {
                if (value > 0) {    // value == 1
                    if (currValue >= 15) {
                        abilityCost = 3;
                    } else if (currValue >= 13) {
                        abilityCost = 2
                    } else if (currValue < 13) {
                        abilityCost = 1;
                    }
                    $scope.character.ability.pointsLeft -= abilityCost;
                } else {    // value == -1
                    if (currValue > 15) {
                        abilityCost = 3;
                    } else if (currValue > 13) {
                        abilityCost = 2;
                    } else if (currValue <= 13) {
                        abilityCost = 1;
                    }
                    $scope.character.ability.pointsLeft += abilityCost;
                }
                $scope.character.ability[ability].score += value;
                $scope.character.ability[ability].mod = Math.floor(($scope.character.ability[ability].score-10)/2);
            }
        }
    };

    // Handles combining background and class skills
    // TODO: Refactor
    function handleSkills() {   // newValue == $scope.selectedSkills
        $scope.character.skills = $scope.character.background.skills.split(', ');
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
        $scope.character.skills = $scope.character.skills.join(', ');
    }

    // Handles combining background and tool skills
    // TODO: Refactor
    function handleTools() {
        var classTools, backgroundTools;
        $scope.character.tools = [];
        if ($scope.character.classObj && $scope.character.classObj.tools.indexOf('None') === -1) {
            classTools = $scope.character.classObj.tools.split(', ');
            $scope.character.tools = $scope.character.tools.concat(classTools);
        }
        if ($scope.character.background && $scope.character.background.tools.indexOf('None') === -1) {
            backgroundTools = $scope.character.background.tools.split(', ');
            $scope.character.tools = $scope.character.tools.concat(backgroundTools);
        }
        $scope.character.tools = $.unique($scope.character.tools);  // remove potential duplicates
        $scope.character.tools.sort();
        $scope.character.tools = $scope.character.tools.join(', '); // return to a string
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
        //return;
    }

    function handleLanguages() {
        $scope.character.languages = $scope.character.raceObj.languages.split(', ');
        if ($scope.selectedLanguages) {
            for (var i=0; i<$scope.selectedLanguages.length; i++) {
                if ($scope.selectedLanguages[i]) {
                    $scope.character.languages.push($scope.selectedLanguages[i]);
                }
            }
        }
        $scope.character.languages.sort();
        $scope.character.languages = $scope.character.languages.join(', ');
    }

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

    }

    $scope.$on('handleBroadcast', function(event, args) {
        var currentClassFeatures;
        if (args.checked) {
            if (args.race) { // && args.racialTraits) { // args.race is a string of the race's name
                $scope.racialBonus = {};    // reset racial bonuses
                $scope.character.raceObj = args.race;
                $scope.character.racialTraits = args.racialTraits;
                $scope.character = charGenFactory.determineRace($scope.character, false);
                $scope.character.languages = $scope.character.raceObj.languages;
                $scope.character.racialTraitIds = args.racialTraitIds;
                for (var k=0; k<$scope.character.racialTraits.length; k++) {
                    var racialTrait = $scope.character.racialTraits[k];
                    if (racialTrait.benefit_stat) {
                        $scope.racialBonus[racialTrait.benefit_stat] = racialTrait.benefit_value;
                        /*if ($scope.character[racialTrait.benefit_stat] !== null) {
                            $scope.character[racialTrait.benefit_stat] += parseInt(racialTrait.benefit_value);
                        } else {    // wait until the property becomes defined
                            var benefitStat = 'character.' + racialTrait.benefit_stat;
                            debugger;
                            // TODO: PRoblem! doesn't trigger when going to Hill Dwarf
                            var watchProperty = $scope.$watch(benefitStat, function(newValue) {
                                if (newValue) {
                                    debugger;
                                    $scope.character[racialTrait.benefit_stat] += parseInt(racialTrait.benefit_value);
                                    watchProperty();
                                }
                            })
                        }*/
                    }
                }
            }
            if (args.class) {
                if (args.class.name !== $scope.character.className) {
                    $scope.character.classObj = args.class;   // used to determine if class contains subclasses
                }
                if ($scope.class && $scope.ability.bonusAbility) {
                    $scope.ability[$scope.ability.bonusAbility].score -= 1;
                    $scope.ability.bonusAbility = null;
                }
                //$scope.character.className = args.class.name;
                $scope.character = charGenFactory.determineClass($scope.character);
                if (args.class.features) {
                    $scope.character.classFeatures = [];    // reset
                    $scope.character.classFeatureIds = [];  // reset
                    for (var prop in args.class.features) {
                        if (args.class.features[prop].level <= $scope.character.level) {
                            $scope.character.classFeatures.push(args.class.features[prop]);
                            $scope.character.classFeatureIds.push(args.class.features[prop].id);
                        }
                    }
                    $scope.currentClassFeatures = $scope.character.classFeatures;
                }
                $scope.character.savingThrows = $scope.character.classObj.saving_throws;
                $scope.availableSkills = [];
                $scope.selectedSkills = []; // clear selected skills ng-model
                $scope.select2Skills = parseInt($scope.character.classObj.num_skills);  //numberSkills
                //determineNumItems('#chosenSkills', numberSkills);
                var skillArray = $scope.character.classObj.avail_skills.split(', ');    // assumes all classes have skills
                var skillObj;
                for (var i=0; i<skillArray.length; i++) {
                    skillObj = { name: skillArray[i] };
                    if ($scope.character.background && $scope.character.background.skills.indexOf(skillArray[i]) !== -1) {
                        skillObj.disabled = true;
                    } else if ($scope.character.background && $scope.character.background.skills.indexOf(skillArray[i]) === -1) {
                        skillObj.disabled = false;
                    }
                    $scope.availableSkills.push(skillObj);
                }
                if ($scope.character.background) {
                    $scope.character.skills = $scope.character.background.skills;   // show only background skills
                }
                handleTools();
            }
            if (args.subclass) {
                //$scope.subclass = $scope.classObj.subclasses[args.subclass.name];    // object
                $scope.character.classObj.subclassObj = $scope.character.classObj.subclasses[args.subclass.name];
                var subclassFeatures = [];
                if (args.subclass.benefit) {
                    for (var prop in args.subclass.benefit) {
                        // TODO: change subclass property from benefit to features
                        if (!args.subclass.benefit[prop].level || args.subclass.benefit[prop].level <= $scope.character.level) {
                            subclassFeatures.push(new KeyValue(prop, args.subclass.benefit[prop].benefit));
                        }
                    }
                    $scope.character.classFeatures = $scope.currentClassFeatures.concat(subclassFeatures);
                }
            }
            /*if (args.html) {
                $scope.html = args.html;
            }*/
            if (args.background) {
                $scope.character.background = args.background
                handleTools();
                if ($scope.availableSkills) {
                    for (var j=0; j<$scope.availableSkills.length; j++) {
                        if ($scope.character.background.skills.indexOf($scope.availableSkills[j].name) !== -1) {
                            $scope.availableSkills[j].disabled = true;
                        } else {
                            $scope.availableSkills[j].disabled = false;
                        }
                    }
                }
                if (!$scope.selectedSkills) {
                    $scope.character.skills = $scope.character.background.skills;   // TODO: include selected skills if available
                } else {    // class and background selected
                    handleSkills($scope.selectedSkills);
                }
                //$scope.selectedSkills = ''; // clear selected skills
            }
            if (args.ability) {
                $scope.character.ability = args.ability;
            }

            $scope.character.numLanguages = $scope.character.background ? parseInt($scope.character.background.languages) : 0;
            $scope.character.ability = charGenFactory.calculateModifiers($scope.character.ability);
            if ($scope.character.ability['int'].mod > 0) {  // needs to come after
                $scope.character.numLanguages += $scope.character.ability['int'].mod;
            }
            $scope.character.hitPoints = $scope.character.classHP + $scope.character.ability.con.mod;

            // needs to be at the very end to alter existing properties
            for (var prop in $scope.racialBonus) {
                if ($scope.character[prop] !== null) {
                    $scope.character[prop] += parseInt($scope.racialBonus[prop]);
                }
            }
            args.checked = false;
        }
    });
}

function DialogRaceController($scope, $http, $modalInstance, raceData) {
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
            }
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
        var race, subrace;
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
                        $scope.race_aba = raceObj.ability_score_adjustment;
                        $scope.subrace_aba = subrace.ability_score_adjustment;
                        $scope.size = raceObj.size;
                        $scope.speed = raceObj.speed;
                        $scope.languages = raceObj.languages;
                        $scope.racialTraitIds = [];
                        for (var trait in raceObj.traits) {    // race traits
                            if (raceObj.traits.hasOwnProperty(trait)) {
                                var raceTrait = raceObj.traits[trait];
                                $scope.traits.push(new KeyValue(raceTrait.name, raceTrait.description,
                                    raceTrait.benefit, raceTrait.benefit_value, raceTrait.per_level));
                                $scope.racialTraitIds.push(raceObj.traits[trait].id);
                            }
                        }
                        for (var trait in subrace.traits) { // subrace traits
                            if (subrace.traits.hasOwnProperty(trait)) {
                                var subraceTrait = subrace.traits[trait];
                                $scope.traits.push(new KeyValue(subraceTrait.name, subraceTrait.description,
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
            $scope.$emit('handleEmit', {race: $scope.raceObj, racialTraits: $scope.traits, racialTraitIds: $scope.racialTraitIds, checked: true});
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
function DialogClassController($scope, $http, $modalInstance, classData){

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
        var value;
        $scope.tempClass = null;
        $scope.featureType = 'Class Features';
        $scope.traits = [], $scope.traits2 = [];
        $scope.features = [];
        for (var i=0; i<$scope.values.length; i++) {
            if ($scope.values[i].name === selectobj.value.name) {
                $scope.tempClass = selectobj.value; // object
                value = $scope.values[i];
                $scope.description = value.desc;
                $scope.traitsTitle = "Hit Points";
                $scope.traits.push(new KeyValue("Hit Dice", "1d" + value.hit_dice + " per " + value.name + " level"),
                    new KeyValue("Hit Points at 1st Level", value.hit_dice + " + your Constitution modifier"),
                    new KeyValue("Hit Points at Higher Levels", "1d" + value.hit_dice + " + your Constitution modifier per " +
                        value.name + " level after 1st"));
                $scope.traits2Title = "Proficiencies";
                $scope.traits2.push(new KeyValue("Armor", value.armor_prof), new KeyValue("Weapons", value.weapon_prof),
                    new KeyValue("Tools", value.tools), new KeyValue("Saving Throws", value.saving_throws),
                    new KeyValue("Skills", value.avail_skills));
                for (var feature in value.features) {
                    if (value.features.hasOwnProperty(feature)) {
                        $scope.features.push(new KeyValue(feature, value.features[feature].benefit));
                    }
                }
                break;
            }
        }
    };

    $scope.done = function() {
        if ($scope.tempClass) {
            $scope.$emit('handleEmit', {class: $scope.tempClass, checked: true});
            $modalInstance.close();
        } else {
            alert("Please select a class");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

function DialogSubclassController($scope, $http, $modalInstance, character){
    $scope.class = character.classObj.name;
    $scope.title = 'Select Subclass';   // change later

    $http({
        url: window.location.pathname + '/json_get_classes/' + $scope.class,
        method: "GET",
        cache: true
    }).success(function(data) {
        $scope.values = [];     // clears the data
        for (var subclass in data.subclasses) {
            $scope.values.push(data.subclasses[subclass]);
        }

    });

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
                    $scope.features.push(new KeyValue("Alignment", value.align));
                }*/
                if (value.armor_prof) {
                    $scope.features.push(new KeyValue("Armor and Shield Proficiencies", value.armor_prof));
                }
                for (var feature in value.benefit) {
                    if (value.benefit.hasOwnProperty(feature)) {
                        $scope.features.push(new KeyValue(value.benefit[feature].name, value.benefit[feature].benefit));
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

function DialogBackgroundController($scope, $http, $modalInstance, backgroundData) {
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

function DialogAbilityController($scope, $modalInstance, ability) {
    $scope.title = 'Select Abilities';
    $scope.tempAbility = ability;
    //$scope.pointsLeft = $scope.tempAbility.pointsLeft;
    var minAbility = 8;
    var maxAbility = 20;
    var bonusAbility;

    $scope.Math = window.Math;
    $scope.bonusAbility = ability.bonusAbility;

    $scope.selectBonusAbility = function(selectobj) {
        if (ability.bonusAbility) {
            $scope.tempAbility[ability.bonusAbility].score -= 1;
        }
        bonusAbility = selectobj.bonusAbility;
        $scope.tempAbility[selectobj.bonusAbility].score += 1;
        $scope.tempAbility.bonusAbility = bonusAbility;
    };
    var incDec;
    $scope.determineAbilitiesLeft = function(ability, newValue, oldValue) {
        var value = newValue - oldValue,
            currValue = oldValue;
        incDec = value > 0 ? 1 : -1;
        for (var i=0; i<Math.abs(value); i++) {
            currValue += incDec;
            $scope.incrementAbility(ability, incDec, currValue);
        }
        $scope.tempAbility[ability].mod = Math.floor((newValue-10)/2);
    };

    // determines points left
    $scope.incrementAbility = function(ability, value) {    // value can only be 1 or -1
        var currValue = $scope.tempAbility[ability].score, abilityCost;
        if ($scope.tempAbility.pointsLeft > 0 && (value > 0 && currValue < maxAbility) || (value < 0 && currValue > minAbility)) {
            if ((value > 0 && currValue >= 13 && $scope.tempAbility.pointsLeft <= 1) ||
                (value > 0 && currValue >= 15 && $scope.tempAbility.pointsLeft <= 2)) {
                // do nothing
            } else {
                if (value > 0) {    // value == 1
                    if (currValue >= 15) {
                        abilityCost = 3;
                    } else if (currValue >= 13) {
                        abilityCost = 2
                    } else if (currValue < 13) {
                        abilityCost = 1;
                    }
                    $scope.tempAbility.pointsLeft -= abilityCost;
                } else {    // value == -1
                    if (currValue > 15) {
                        abilityCost = 3;
                    } else if (currValue > 13) {
                        abilityCost = 2;
                    } else if (currValue <= 13) {
                        abilityCost = 1;
                    }
                    $scope.tempAbility.pointsLeft += abilityCost;
                }
                $scope.tempAbility[ability].score += value;
            }
        }
    }

    $scope.done = function() {
        if ($scope.tempAbility.pointsLeft === 0) {
            $scope.tempAbility.bonusAbility = bonusAbility;
            $scope.$emit('handleEmit', {ability: $scope.tempAbility, checked: true});
            $modalInstance.close();
        } else {
            alert("Please set your ability scores.");
        }
    };

    $scope.close = function(){
        $modalInstance.dismiss('cancel');
    };
}

// might not be needed
/*CharGen.$inject = ['$scope', '$modal'];
DialogRaceController.$inject = ['$scope', '$http', 'dialog'];
DialogClassController.$inject = ['$scope', '$http', 'dialog'];
DialogSubclassController.$inject = ['$scope', '$http', 'dialog'];
DialogBackgroundController.$inject = ['$scope', '$http', 'dialog', 'ability'];*/

function KeyValue(name, benefit, benefit_stat, benefit_value, per_level) {
    this.name = name;
    this.benefit = benefit;
    this.benefit_stat = benefit_stat;
    this.benefit_value = benefit_value;
    this.per_level = per_level;
}
