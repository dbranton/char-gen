angular.module('charGenDirective', [])
    .directive('skills', function() {
        return {
            restrict: 'E',
            template: '<div class="skillUI" ng-repeat="skill in character.skills">' +
                '<label><input type="checkbox" name="skill[]" value="{{skill.name}}" ng-checked="skill.proficient" ng-disabled="skill.disabled" ng-model="skill.proficient" ng-change="selectSkill(skill)" /> <span ng-show="skill.val >= 0">+</span>{{skill.val}} {{skill.name}} ({{skill.ability}})</label>' +
                '</div>',
            link: function(scope, element, attrs) {
                scope.selectSkill = function(skill) {
                    scope.character.updateSkillProficiency(skill.name, skill.proficient);
                };
            }
        };
    })
    .directive('expertise', function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                scope.$watch(attrs.ngModel, function(newVal, oldVal) {
                    var item, isAdded;
                    if (angular.isDefined(newVal)) {
                        if (!oldVal) {
                            isAdded = true;
                            item = newVal[0];
                        } else if (newVal.length < oldVal.length) {
                            isAdded = false;    // remove
                            item = oldVal.diff(newVal)[0];
                        } else if (newVal.length > oldVal.length) {
                            isAdded = true;
                            item = newVal.diff(oldVal)[0];
                        }
                        scope.character.updateSkillScore(item, isAdded);

                    }
                });
            }
        }
    })
    .directive('languages', function(charGenFactory) {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                var LANGUAGE_LIST = [];
                charGenFactory.getLanguages().success(function(data) {
                    for (var i=0, ilen=data.length; i<ilen; i++) {
                        LANGUAGE_LIST.push(data[i].name);
                    }
                    scope.availableLanguages = angular.copy(LANGUAGE_LIST);
                });
                scope.$watch(attrs.languages, function(newValue) {  // watches numLanguages
                    if (angular.isDefined(newValue)) {//} && $scope.character.background) {
                        scope.character.selectedLanguages = scope.character.selectedLanguages || [];
                        scope.select2Languages = newValue; // represents the 'max' attribute for select2
                        scope.numLanguagesLeft = scope.character.numLanguages - scope.character.selectedLanguages.length;
                        //$scope.selectedLanguages.length = newValue; //determineNumItems('#chosenLanguages', newValue);
                    }
                });
                // move to language directive
                scope.$watch('character.selectedLanguages', function(newValue, oldValue) {   // triggered whenever a language is selected
                    if ((angular.isArray(newValue) && angular.isArray(oldValue) && newValue.length !== oldValue.length) && scope.character.raceObj.name) {   // requires race
                        scope.character.selectedLanguages = scope.character.selectedLanguages || oldValue;
                        var selectedLanguages = newValue || oldValue;
                        var languages = scope.character.defaultLanguages ? scope.character.defaultLanguages.split(', ') : [];
                        languages = languages.concat(selectedLanguages);
                        languages = scope.character.classObj.bonusLanguages ? languages.concat(scope.character.classObj.bonusLanguages) : languages;
                        languages.sort();
                        scope.character.languages = languages.join(', ');
                        scope.numLanguagesLeft = scope.character.numLanguages - selectedLanguages.length;
                    }
                });
                // move to language directive
                scope.$watch('character.raceObj.subrace.name', function(newValue) {
                    if (newValue) {
                        var languageList = [];
                        for (var i=0, ilen=LANGUAGE_LIST.length; i<ilen; i++) {
                            var language = LANGUAGE_LIST[i];
                            if (scope.character.raceObj.languages.indexOf(language) === -1) {
                                languageList.push(language);    // only show languages that aren't already taken
                            }
                        }
                        scope.availableLanguages = languageList;
                    }
                });

                //bonus language support
                scope.$watch(attrs.bonusLanguages, function(newValue, oldValue) {
                    if (angular.isArray(newValue)) {
                        var languageList = [];
                        var oldLanguageList = angular.copy(scope.availableLanguages);
                        angular.forEach(oldLanguageList, function(language) {
                            if (!scope.character.classObj.bonusLanguages || scope.character.classObj.bonusLanguages.indexOf(language) === -1) {
                                languageList.push(language);    // only show languages that aren't already taken
                            }
                        });
                        scope.availableLanguages = languageList;
                    }
                });

            }
        }
    })
    .directive('select2Spellcasting', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs, ngModel) {
                function formatSelection(item) {
                    return item.name;
                }
                scope[attrs.uiSelect2] = {
                    ajax: {
                        url: window.location.pathname + '/json_get_spells',
                        dataType: 'json',
                        data: function (term, page) {},
                        quietMillis: 300,   // default is 100
                        results: function (data, page) {
                            // parse the results into the format expected by Select2
                            var arr = [], spellArr = [], selectedSpells = '', level;
                            angular.forEach(data, function(subarray, idx) {
                                level = idx + 1;
                                spellArr = subarray;
                                if (scope.character.classObj.selectedSpells) {    //attrs.parent) {
                                    spellArr = [];
                                    selectedSpells = scope.character.classObj.selectedSpells;   //scope.$eval(attrs.parent);
                                    angular.forEach(subarray, function(spell) {
                                        if (selectedSpells.getIndexBy('name', spell.name) === -1) {
                                            spellArr.push(spell);
                                        }
                                    });
                                }
                                arr.push({name: 'Level ' + level, children: spellArr});
                            });
                            return {
                                results: arr    //data
                            };
                        }
                    },
                    multiple: true,
                    formatResult: formatSelection,
                    formatSelection: formatSelection,
                    initSelection: function(element, callback) {
                        var data = [], ids = $(element).val();
                        angular.forEach(scope.character.classObj.selectedSpells, function(spell) {
                            if (ids.indexOf(spell.id) !== -1) {
                                data.push(spell);
                            }
                        });
                        callback(data);
                    }
                };
                scope.$watch(attrs.select2Spellcasting, function(newVal, oldVal) {
                    if (newVal) {   // can be null
                        var spellcastingObj = newVal;
                        scope[attrs.uiSelect2].ajax.data =  function (term, page) {
                            var paramObj = {
                                class_id: spellcastingObj.class_id,
                                max_spell_level: spellcastingObj.max_spell_level,
                                term: term
                            };
                            if (spellcastingObj.restricted_schools) {
                                paramObj.restricted_school_1 = spellcastingObj.restricted_schools[0];
                                paramObj.restricted_school_2 = spellcastingObj.restricted_schools[1];
                            }
                            return paramObj;
                        };
                    }
                });
                scope.$watch(attrs.ngModel, function(newVal, oldVal) {
                    if (angular.isArray(newVal)) {
                        var primarySpells = null, bonusSpells = null;
                        if (attrs.bonus) {
                            bonusSpells = scope.$eval(attrs.bonus) || [];
                            scope.character.classObj.selectedSpells = bonusSpells.concat(newVal);
                        } else if (attrs.primary) {
                            primarySpells = scope.$eval(attrs.primary) || [];
                            scope.character.classObj.selectedSpells = primarySpells.concat(newVal);

                        } else {
                            throw new Error('this element has no child or parent');
                        }
                    }
                });
            }
        }
    })
    .directive('isEmpty', function() {
        return {
            require: 'ngModel',
            restrict: 'A',
            link: function(scope, element, attrs, ngModel) {
                // for DOM -> model validation
                ngModel.$parsers.unshift(function(value) {
                    var valid = value === 0;
                    ngModel.$setValidity('notEmpty', valid);
                    return valid ? valid : undefined;
                });
                // for model -> DOM validation
                ngModel.$formatters.unshift(function(value) {
                    ngModel.$setValidity('notEmpty', value === 0);
                    return value;
                });
            }
        };
    })
    .directive('classFeatures', function($compile) {
        return {
            restrict: 'A',
            link: function(scope, elem, attrs) {
                scope.$watch(attrs.classFeatures, function(classObj, oldClassObj) {
                    if (classObj && (!oldClassObj || !oldClassObj.featureChoices) && angular.isArray(classObj.featureChoices) && classObj.featureChoices.length > 0) {
                        var html = $('#featureUI').html(),
                            el = angular.element(html),
                            compiled = $compile(el);
                        elem.html(el);
                        compiled(scope);
                    } else if (classObj && !classObj.featureChoices && oldClassObj && angular.isArray(oldClassObj.featureChoices) && oldClassObj.featureChoices.length > 0) {
                        elem.empty();
                    }
                });
            }
        }
    });