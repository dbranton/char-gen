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