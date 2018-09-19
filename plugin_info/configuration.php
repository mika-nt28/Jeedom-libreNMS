<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
	<fieldset>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{IP du serveur LibreNMS :}}</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="IP" />
			</div>>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Tokens du serveur LibreNMS :}}</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Tokens" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Devices :}}</label>
			<div class="col-lg-4">
				
<a class="btn btn-primary devices"><i class="fa fa-search"></i>{{Importer les device}}</a>
			</div>
		</div>
	</fieldset>
</form>
<script>
$('.devices').on('click',function(){
	$.ajax({
		async: false,
		type: 'POST',
		url: 'plugins/libreNMS/core/ajax/libreNMS.ajax.php',
		data:{
			action: 'getDevice'
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#div_alert').showAlert({message: data.result, level: 'success'});
		}
	});
});
</script>
