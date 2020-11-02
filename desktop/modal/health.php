<?php
if (!isConnect('admin')) {
	throw new Exception('401 Unauthorized');
}
$eqLogics = libreNMS::byType('libreNMS');

$Result=libreNMS::Request('/api/v0/system');
foreach($Result["system"][0] as $cmd => $value){
?>
<div class="form-group">
	<label class="col-lg-2 control-label"><?php  $name_cmd = str_replace('_ver', ' version',$cmd) ; echo $name_cmd ?></label>
	<div class="col-lg-2">
		<input class="form-control" style="margin-top:-5px" placeholder="" readonly="" value="<?php  echo $value; ?>">
    	</div>
</div>
<?php
}
?>
<table class="table table-condensed tablesorter" id="table_healthneato">
	<thead>
		<tr>
			<th>{{Module}}</th>
			<th>{{ID}}</th>
			<th>{{Status}}</th>
			<th>{{Dernière communication}}</th>
			<th>{{Date création}}</th>
		</tr>
	</thead>
	<tbody>
	 <?php
		foreach ($eqLogics as $eqLogic) {
			echo '<tr><td><a href="' . $eqLogic->getLinkToConfiguration() . '" style="text-decoration: none;">' . $eqLogic->getHumanName(true) . '</a></td>';
			echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getId() . '</span></td>';
	$status = '<span class="label label-success" style="font-size : 1em; cursor : default;">{{OK}}</span>';
	if ($eqLogic->getStatus('state') == 'nok') {
		$status = '<span class="label label-danger" style="font-size : 1em; cursor : default;">{{NOK}}</span>';
	}
	echo '<td>' . $status . '</td>';
			echo '<td><span class="label label-info" style="font-size : 1em;cursor:default;">' . $eqLogic->getStatus('lastCommunication') . '</span></td>';
			echo '<td><span class="label label-info" style="font-size : 1em;">' . $eqLogic->getConfiguration('createtime') . '</span></td></tr>';
		}
	?>
	</tbody>
</table>
