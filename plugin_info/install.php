<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function libreNMS_install(){
}
function libreNMS_update(){
	log::add('libreNMS','debug','Lancement du script de mise a jours'); 
	foreach(eqLogic::byType('libreNMS') as $libreNMS){
		$libreNMS->save();
	}
	log::add('libreNMS','debug','Fin du script de mise a jours');
}
function Volets_remove(){
}
?>
