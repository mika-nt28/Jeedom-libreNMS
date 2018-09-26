<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
function libreNMS_install(){
}
function libreNMS_update(){
	log::add('libreNMS','debug','Lancement du script de mise a jours'); 
	foreach(eqLogic::byType('libreNMS') as $libreNMS){
		$cmd=$libreNMS->getCmd(null,'local_ver');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'local_ver');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'local_sha');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'local_date');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'local_branch');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'db_schema');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'php_ver');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'mysql_ver');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'rrdtool_ver');
		if(is_object($cmd))
			$cmd->remove();
		$cmd=$libreNMS->getCmd(null,'netsnmp_ver');
		if(is_object($cmd))
			$cmd->remove();
		$libreNMS->save();
	}
	log::add('libreNMS','debug','Fin du script de mise a jours');
}
function libreNMS_remove(){
}
?>
