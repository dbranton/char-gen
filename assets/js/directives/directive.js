angular.module('charGenDirective', [])
    .directive('skills', function($http) {
        return {
            restrict: 'E',
            template: '<div class="skillUI" ng-repeat="skill in character.skills">' +
                '<label><input type="checkbox" name="skill[]" value="{{skill.name}}" ng-checked="skill.proficient" ng-disabled="skill.disabled" ng-model="skill.proficient" ng-change="selectSkill(skill)" /> <span ng-show="skill.val >= 0">+</span>{{skill.val}} {{skill.name}} ({{skill.ability}})</label>' +
                '</div>',
            link: function(scope, element, attrs) {
                var enabledSkills = [];
                scope.selectSkill = function(skill) {
                    scope.character.updateSkillScore(skill.name);
                    if (skill.proficient) {
                        scope.character.getProficientSkills();
                        scope.character.selectedSkills.push(skill.name);  // add skill
                        scope.character.selectedSkills.sort();
                        scope.character.numSkillsLeft--;
                        if (scope.character.numSkillsLeft === 0) {
                            enabledSkills = [];
                            scope.character.skills.forEach(function(skill, i, skills) {
                                if (skill.disabled === false) {
                                    enabledSkills.push(skill.name);   // save currently enabled skills for later
                                }
                            });
                            scope.character.enableSkills(false);  // disable all skills except the checked ones
                        }
                    } else {
                        scope.character.selectedSkills.splice(scope.character.selectedSkills.indexOf(skill.name), 1);   // remove skill
                        scope.character.numSkillsLeft++;
                        if (scope.character.numSkillsLeft === 1) {
                            scope.character.enableSkills(enabledSkills);
                        }
                    }
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
    });