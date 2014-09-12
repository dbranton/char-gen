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
    .directive('languages', function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                scope.$watch(attrs.languages, function(newValue) {
                    if (angular.isDefined(newValue)) {//} && $scope.character.background) {
                        scope.character.selectedLanguages = scope.character.selectedLanguages || [];
                        scope.select2Languages = newValue; // represents the 'max' attribute for select2
                        scope.numLanguagesLeft = scope.character.numLanguages - scope.character.selectedLanguages.length;
                        //$scope.selectedLanguages.length = newValue; //determineNumItems('#chosenLanguages', newValue);
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