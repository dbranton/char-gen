<script type="text/ng-template" id="abilityModal.html">
    <div class="modal-header">
        <h3>{{title}}</h3>
    </div>
    <div class="modal-body row">
        <div class="col-md-7">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Ability</th>
                        <th>Base</th>
                        <!--<th>Bonus</th>-->
                        <th>Mod</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">{{tempAbility.pointsLeft}} Points Remaining</td>
                    </tr>
                    <tr>
                        <td>Strength</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('str', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.str.score + (bonusAbility.str)}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('str', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="str" ng-show="tempAbility.str.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.str.score-10)/2)}}</td>
                    </tr>
                    <tr>
                        <td>Dexterity</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.dex.score}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('dex', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="dex" ng-show="tempAbility.dex.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.dex.score-10)/2)}}</td>
                    </tr>
                    <tr>
                        <td>Constitution</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('con', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.con.score}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('con', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="con" ng-show="tempAbility.con.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.con.score-10)/2)}}</td>
                    </tr>
                    <tr>
                        <td>Intelligence</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('int', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.int.score}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('int', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="int" ng-show="tempAbility.int.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.int.score-10)/2)}}</td>
                    </tr>
                    <tr>
                        <td>Wisdom</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.wis.score}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('wis', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="wis" ng-show="tempAbility.wis.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.wis.score-10)/2)}}</td>
                    </tr>
                    <tr>
                        <td>Charisma</td>
                        <td>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', -1)"><i class="fa fa-minus"></i></button>
                            <span class="abilityText">{{tempAbility.cha.score}}</span>
                            <button type="button" class="btn btn-default" ng-click="incrementAbility('cha', 1)"><i class="fa fa-plus"></i></button>
                        </td>
                        <!--<td><input type="radio" ng-change="selectBonusAbility(this)" name="bonusAbility" ng-model="bonusAbility" value="cha" ng-show="tempAbility.cha.bonus" /></td>-->
                        <td>{{Math.floor((tempAbility.cha.score-10)/2)}}</td>
                    </tr>
                </tbody>
            </table>
        </div>	<!-- end col-md -->
        <!--<div class="col-md-3">
            <p>{{description}}</p>
            <h4>{{featureType}}</h4>
            <dl>
                <div ng-repeat="trait in traits">
                    <dt>{{trait.name}}</dt>
                    <dd>{{trait.desc}}</dd>
                </div>
            </dl>
            <dl>
                <div ng-repeat="feature in features">
                    <dt>{{feature.name}}</dt>
                    <dd>{{feature.desc}}</dd>
                </div>
            </dl>
        </div>-->
    </div>
    <div class="modal-footer">
        <button ng-click="done()" class="btn btn-primary">Done</button>
        <button ng-click="close()" class="btn btn-default">Cancel</button>
    </div>
</script>