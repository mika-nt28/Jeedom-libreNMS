<?php
try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    	include_file('core', 'authentification', 'php');
	include_file('core', 'dpt', 'class', 'eibd');
    	if (!isConnect('admin')) {
        	throw new Exception(__('401 - Accès non autorisé', __FILE__));
    	}
	if (init('action') == 'getLog') {
  
				$eqLogic = new libreNMS();
        $eqLogic->getDevice();
		    ajax::success("L'import de device a ete executé");
	}
   throw new Exception(__('Aucune methode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}
?>