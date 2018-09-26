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
			<label class="col-lg-4 control-label">{{Url du serveur LibreNMS :}}</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Host" placeholder="{{Saisir l'url de votre serveur (avec le http:// ou https://)}}"/>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-4 control-label">{{Tokens du serveur LibreNMS :}}</label>
			<div class="col-lg-4">
				<input class="configKey form-control" data-l1key="Tokens" placeholder="{{Saisir le token de connexion de votre serveur}}"/>
			</div>
		</div>
	</fieldset>
</form>
