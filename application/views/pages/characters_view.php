<div ng-app="user" ng-controller="User" ng-init="init()" ng-cloak>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Created</th>
            <th>Level</th>
            <th>Race</th>
            <th>Class</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            <tr ng-if="!characters"><td colspan="5">Loading characters...</td></tr>
            <tr ng-if="characters.length == 0"><td colspan="5">You have no characters yet</td></tr>
            <tr ng-repeat="character in characters">
                <td><a ng-href="character/{{character.id}}">{{character.name}}</a></td>
                <td>{{character.date_added}}</td>
                <td>{{character.level}}</td>
                <td>{{character.race}}</td>
                <td>{{character.class}}</td>
                <td><a href="" ng-click="deleteCharacter(character.id)"><i class="fa fa-trash-o"></i><a/></td>
            </tr>
        </tbody>
    </table>
    <?php echo anchor('character_generator', '<i class="fa fa-plus"></i> Add Character', array('class'=>'btn btn-default')); ?>
</div>