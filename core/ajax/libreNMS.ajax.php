<?php
try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');
	if (!isConnect('admin')) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}
	if (init('action') == 'getDevice') {
		libreNMS::getDevice();
		ajax::success("L'import de device a ete executé");
	}
	if (init('action') == 'getSystem') {
		ajax::success(libreNMS::getSystem(););
	}
	if (init('action') == 'getDeviceHealth') {
		$eqLogic = eqLogic::byId(init('id'));
		if(is_object($eqLogic))
			ajax::success($eqLogic->getDeviceHealth());
	}
	throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayExeption($e), $e->getCode());
}
?>
