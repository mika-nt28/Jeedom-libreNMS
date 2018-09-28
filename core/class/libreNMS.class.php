<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class libreNMS extends eqLogic {
	/*     * *************************Attributs****************************** */
	public static $_TypesInfo = array('ARP','Services','LAN');
	private $_collectDate = '';
	public static $_widgetPossibility = array('custom' => true);

	/*     * ***********************Methode static*************************** */
	public static function cron() {	
		foreach(eqLogic::byType('libreNMS') as $libreNMS){
			/*if($libreNMS->getConfiguration('Système'))
				$libreNMS->getSystem();*/
			if($libreNMS->getConfiguration('ARP'))
				$libreNMS->getARP();
			if($libreNMS->getConfiguration('Services'))
				$libreNMS->getServices();
			if($libreNMS->getConfiguration('LAN'))
				$libreNMS->getLAN();
		}
	}
	public static function Request($Complement,$Type='GET',$Parameter=''){		
		$ch = curl_init();
		$Url=config::byKey('Host','libreNMS').$Complement;
		if($Type == 'GET' && $Parameter != '')
			$Url = $Url . '?' . self::ArrayToUrl($Parameter);
		log::add('libreNMS','debug',$Url);
		curl_setopt($ch, CURLOPT_URL, $Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-Auth-Token: '.config::byKey('Tokens','libreNMS')
		));
		if($Type == 'POST' && $Parameter != '')
			curl_setopt($ch, CURLOPT_POSTFIELDS, $Parameter);
		$rsp = curl_exec($ch);
		curl_close($ch);
		log::add('libreNMS','debug',$rsp);
		return json_decode($rsp,true);
	}
	public static function ArrayToUrl($UrlArray) {
		ksort($UrlArray);
		$url='';
		foreach($UrlArray as $Parameter => $Value){
			if($url != '')
				$url.='&';
			$url.=$Parameter.'='.$Value;
		}
		return $url;
	}	
	public static function getDevice() {
		$result=self::Request('/api/v0/devices');
		foreach($result['devices'] as $device){
			$eqLogic = eqLogic::byLogicalId($device['ip'],'libreNMS');
			if (!is_object($eqLogic) && $device['disabled'] == '0') {
				$eqLogic = new libreNMS();
				$eqLogic->setName($device['hostname']);
				$eqLogic->setEqType_name('libreNMS');
				$eqLogic->setLogicalId($device['ip']);
				$eqLogic->setComment($device['sysDescr']);
				$eqLogic->setConfiguration('location',$device['location']);
				$eqLogic->setConfiguration('type',$device['type']);
				$eqLogic->setConfiguration('lat',$device['lat']);
				$eqLogic->setConfiguration('lng',$device['lng']);
				$eqLogic->setConfiguration('snmpver',$device['snmpver']);
				$eqLogic->setConfiguration('port',$device['port']);
				$eqLogic->setConfiguration('transport',$device['transport']);
				$eqLogic->setConfiguration('uptime',$device['uptime']);
				$eqLogic->setConfiguration('last_ping',$device['last_ping']);
				$eqLogic->setConfiguration('last_ping_timetaken',$device['last_ping_timetaken']);
				$eqLogic->setConfiguration('last_polled',$device['last_polled']);
				$eqLogic->setConfiguration('last_polled_timetaken',$device['last_polled_timetaken']);
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->save();
				log::add('libreNMS','debug','Le device: '.$device['hostname'].'est importé');
			}
		}
	}
	public static function getSystem() {
		return self::Request('/api/v0/system');
		/*if($Result["status"] == "ok"){
			foreach($Result["system"][0] as $cmd => $value)
				$this->checkAndUpdateCmd($cmd,$value);
		}*/
	}
	/*     * *********************Methode d'instance************************* */
  	public function getDeviceHealth() {
		$Graph=array();
		$result=self::Request('/api/v0/devices/'.$this->getName().'/health');
		foreach($result['graphs'] as $graphs){
			log::add('libreNMS','debug','commande: '.$graphs['name'].'est trouvée');
			$Graph[$graphs["desc"]]=self::Request('/api/v0/devices/'.$this->getName().'/health/'.$graphs["name"]);
		} 
		return $Graph;
	}
	public function getARP() {
		$Result=self::Request('/api/v0/resources/ip/arp/'.$this->getLogicalId());
		if($Result["status"] == "ok"){
			foreach($Result["arp"][0] as $cmd => $value)
				$this->checkAndUpdateCmd($cmd,$value);
		}
	}
	public function getServices() {
		$Result=self::Request('/api/v0/services/'.$this->getName());
		if($Result["status"] == "ok"){
			foreach($Result["services"][0] as $cmd => $value)
				$this->checkAndUpdateCmd($cmd,$value);
		}
	}
	public function getLAN() {
		$Result=self::Request('/api/v0/devices/'.$this->getName().'/vlans');
		if($Result["status"] == "ok"){
			foreach($Result["services"][0] as $cmd => $value)
				$this->checkAndUpdateCmd($cmd,$value);
		}
	}
	public function postSave() {
		if($this->getConfiguration('ARP')){
			$this->AddCommande('Port','port_id',"info", 'string','','ARP');
			$this->AddCommande('MAC','mac_address',"info", 'string','','ARP');
			$this->AddCommande('IPv4','ipv4_address',"info", 'string','','ARP');
			$this->AddCommande('Nom de contexte','context_name',"info", 'string','','ARP');
		}
		if($this->getConfiguration('Services')){
			$this->AddCommande('id','service_id',"info", 'string','','Services');
			$this->AddCommande('IP','service_ip',"info", 'string','','Services');
			$this->AddCommande('Type','service_type',"info", 'string','','Services');
			$this->AddCommande('Descendant','service_desc',"info", 'string','','Services');
			$this->AddCommande('Parametre','service_param',"info", 'string','','Services');
			$this->AddCommande('Ignore','service_ignore',"info", 'string','','Services');
			$this->AddCommande('Status','service_status',"info", 'string','','Services');
			$this->AddCommande('Change','service_changed',"info", 'string','','Services');
			$this->AddCommande('Message','service_message',"info", 'string','','Services');
			$this->AddCommande('Activation','service_disabled',"info", 'string','','Services');
			$this->AddCommande('DS','service_ds',"info", 'string','','Services');
		}
		if($this->getConfiguration('LAN')){
			$this->AddCommande('VLAN','vlan_vlan',"info", 'string','','LAN');
			$this->AddCommande('Domaine','vlan_domain',"info", 'string','','LAN');
			$this->AddCommande('Nom du lan','vlan_name',"info", 'string','','LAN');
			$this->AddCommande('Type de lan','vlan_type',"info", 'string','','LAN');
			$this->AddCommande('MTU','vlan_mtu',"info", 'string','','LAN');
		}
		

		//$this->AddCommande('Nom de la commande','name',"info", 'numeric','');
	}
	public function CreateCron($Name,$Schedule) {
		$cron =cron::byClassAndFunction('libreNMS', $Name, array('id' => $this->getId()));
		if (!is_object($cron)) {
			$cron = new cron();
			$cron->setClass('libreNMS');
			$cron->setFunction($Name);
			$cron->setOption(array('id' => $this->getId()));
			$cron->setEnable(1);
		}
		$cron->setSchedule($Schedule);
		$cron->save();
	}
	public function AddCommande($Name,$LogicalId,$Type="info", $SousType='numeric',$Unite='',$Categorie='') {
		$Commande = $this->getCmd(null, $LogicalId);
		if (!is_object($Commande)) {
			$Commande = new libreNMSCmd();
			$Commande->setEqLogic_id($this->getId());
			$Commande->setLogicalId($LogicalId);
			$Commande->setIsVisible(1);
			$Commande->setIsHistorized(1);
			$Commande->setType($Type);
			$Commande->setSubType($SousType);
		}
		$Commande->setName($Name);
		$Commande->setUnite($Unite);
		$Commande->setConfiguration('Categorie',$Categorie);
		$Commande->save();
		return $Commande;
	}
	/*     * **********************Getteur Setteur*************************** */

}

class libreNMSCmd extends cmd {
	/*     * *************************Attributs****************************** */

	public static $_widgetPossibility = array('custom' => false);

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */

	public function execute($_options = null) {
		if ($this->getType() == '') {
			return '';
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}
