
                <dl ng-hide="!tempBackground">
                    <div>
                        <dt>{{traitName}}</dt>
                        <dd>{{traitDesc}}</dd>
                    </div>
                    <div>
                        <dt>Skills</dt>
                        <dd>
                            {{skills}}
                            <!--<ul>
                                <li ng-repeat="skill in skills">{{skill}}</li>
                            </ul>-->
                        </dd>
                    </div>
                    <div ng-show="tools">
                        <dt>Tools</dt>
                        <dd>
                            {{tools}}
                        </dd>
                    </div>
                    <div ng-show="languages">
                        <dt>Languages</dt>
                        <dd>
                            {{languages}}
                        </dd>
                    </div>
                </dl>