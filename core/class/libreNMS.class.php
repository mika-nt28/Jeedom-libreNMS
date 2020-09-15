<?php
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
class libreNMS extends eqLogic {
	/*     * *************************Attributs****************************** */
	public static $_TypesInfo = array('Système','ARP','Services','LAN','Health');
	private $_collectDate = '';
	public static $_widgetPossibility = array('custom' => true);

	/*     * ***********************Methode static*************************** */
	public static function cron() {	
		foreach(eqLogic::byType('libreNMS') as $libreNMS){
			if($libreNMS->getConfiguration('Système'))
				$libreNMS->getSystem();
			if($libreNMS->getConfiguration('ARP'))
				$libreNMS->getARP();
			if($libreNMS->getConfiguration('Services'))
				$libreNMS->getServices();
			if($libreNMS->getConfiguration('LAN'))
				$libreNMS->getLAN();
			if($libreNMS->getConfiguration('Health'))
				$libreNMS->getDeviceHealth();
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
			if(isset($device['ip']) && $device['ip'] != ''):
				$host=$device['ip'];
			elseif(isset($device['hostname'])):
				$host=$device['hostname'];
			endif;
			$eqLogic = eqLogic::byLogicalId($host,'libreNMS');
			if (!is_object($eqLogic) && $device['disabled'] == '0') {
				$eqLogic = new libreNMS();
				$eqLogic->setName($device['hostname']);
				$eqLogic->setEqType_name('libreNMS');
				$eqLogic->setLogicalId($host);
				if(isset($device['sysDescr']))
					$eqLogic->setComment($device['sysDescr']);
				if(isset($host))
					$eqLogic->setConfiguration('IP',$host);
				if(isset($device['location']))
					$eqLogic->setConfiguration('location',$device['location']);
				if(isset($device['type']))
					$eqLogic->setConfiguration('type',$device['type']);
				if(isset($device['lat']))
					$eqLogic->setConfiguration('lat',$device['lat']);
				if(isset($device['lng']))
					$eqLogic->setConfiguration('lng',$device['lng']);
				if(isset($device['snmpver']))
					$eqLogic->setConfiguration('snmpver',$device['snmpver']);
				if(isset($device['port']))
					$eqLogic->setConfiguration('port',$device['port']);
				if(isset($device['transport']))
					$eqLogic->setConfiguration('transport',$device['transport']);
				if(isset($device['sysObjectID']))
					$eqLogic->setConfiguration('sysObjectID',$device['sysObjectID']);
				if(isset($device['uptime']))
					$eqLogic->setConfiguration('uptime',$device['uptime']);
				if(isset($device['last_ping']))
					$eqLogic->setConfiguration('last_ping',$device['last_ping']);
				if(isset($device['last_ping_timetaken']))
					$eqLogic->setConfiguration('last_ping_timetaken',$device['last_ping_timetaken']);
				if(isset($device['last_polled']))
					$eqLogic->setConfiguration('last_polled',$device['last_polled']);
				if(isset($device['last_polled_timetaken']))
					$eqLogic->setConfiguration('last_polled_timetaken',$device['last_polled_timetaken']);
				$eqLogic->setIsEnable(1);
				$eqLogic->setIsVisible(1);
				$eqLogic->save();
				log::add('libreNMS','debug','Le device : '.$host.' est importé');
			} else log::add('libreNMS','debug','Le device : '.$host.' est déjà présent');

		}
	}
	public static function getSystem() {
		return self::Request('/api/v0/system');
		if($Result["status"] == "ok"){
			foreach($Result["system"][0] as $cmd => $value)
				$this->checkAndUpdateCmd($cmd,$value);
		}
	}
	/*     * *********************Methode d'instance************************* */
  	public function getDeviceHealth() {
		$Configuration=array('Categorie'=>'Health');
		$Graph=array();
		$result=self::Request('/api/v0/devices/'.$this->getName().'/health');
		if($result["status"] == "ok")
		foreach($result['graphs'] as $graphs){
			log::add('libreNMS','debug','commande: '.$graphs['name'].' est trouvée');
			$namegraph=self::Request('/api/v0/devices/'.$this->getName().'/health/'.$graphs["name"]);
			if($namegraph["status"] == "ok")
				foreach ($namegraph['graphs'] as $graph ) {
					$sensorId=$graph['sensor_id'];
					$desc=$graph['desc'];
					$toto=self::Request('/api/v0/devices/'.$this->getName().'/health/'.$graphs["name"].'/'.$sensorId);
					if($toto["status"] == "ok")
						foreach ($toto['graphs'] as $statusgraph ) {
							switch ($statusgraph['sensor_class']) {
									case "airflow":
										$SousType='numeric';
										$Unite='cfm';
										break;
									case "ber":
										$SousType='numeric';
										$Unite='ratio';
										break;
									case "charge":
									case "humidity":
									case "load":
									case "loss":
										$SousType='numeric';
										$Unite='%';
										break;
									case "chromatic_dispersion":
										$SousType='numeric';
										$Unite='ps/nm';
										break;
									case "cooling":
									case "power":
										$SousType='numeric';
										$Unite='W';
										break;
									case "count":
									case "state":
										$SousType='numeric';
										$Unite='#';
										break;
									case "current":
										$SousType='numeric';
										$Unite='A';
										break;
									case "dbm":
									case "signal":
										$SousType='numeric';
										$Unite='dBm';
										break;
									case "delay":
										$SousType='numeric';
										$Unite='s';
										break;
									case "eer":
										$SousType='numeric';
										$Unite='eer';
										break;
									case "fanspeed":
										$SousType='numeric';
										$Unite='tr/min';
										break;
									case "frequency":
										$SousType='numeric';
										$Unite='Hz';
										break;
									case "power_consumed":
										$SousType='numeric';
										$Unite='kWh';
										break;
									case "pressure":
										$SousType='numeric';
										$Unite='kPa';
										break;
									case "quality_factor":
										$SousType='numeric';
										$Unite='dB';
										break;
									case "runtime":
										$SousType='numeric';
										$Unite='Min';
										break;
									case "snr":
										$SousType='numeric';
										$Unite='SNR';
										break;
									case "temperature":
										$SousType='numeric';
										$Unite='°C';
										break;
									case "voltage":
										$SousType='numeric';
										$Unite='V';
										break;
									case "waterflow":
										$SousType='numeric';
										$Unite='I/m';
										break;
									default:
										$SousType='string';
										$Unite='';
                            			}
						$this->AddCommande($statusgraph['sensor_class'].'>'.$graph['desc'],$statusgraph['sensor_class'].'_'.$graph['desc'],"info", $SousType,$Unite,$Configuration);
                          			$this->checkAndUpdateCmd($statusgraph['sensor_class'].'_'.$graph['desc'],$statusgraph['sensor_current']);
					}
				}
		}
		return $Graph;
	}
	public function getARP() {
		$Result=self::Request('/api/v0/resources/ip/arp/'.$this->getName());
			log::add('libreNMS','debug','commande-ARP /api/v0/resources/ip/arp/'.$this->getName());

		if($Result["status"] == "ok"){
			foreach($Result["arp"][0] as $cmd => $value) {
				$this->checkAndUpdateCmd($cmd,$value);
            }
		}
	}
	public function getServices() {
		$Result=self::Request('/api/v0/services/'.$this->getName());
		if($Result["status"] == "ok"){
			foreach($Result["services"][0] as $cmd => $value)
			log::add('libreNMS','debug','commande: getServices' . $cmd . ' value : $value' . $value );
			$this->checkAndUpdateCmd($cmd,$value);
		}
	}
	public function getLAN() {
		$Result=self::Request('/api/v0/devices/'.$this->getName().'/vlans');
		if($Result["status"] == "ok"){
			foreach($Result["services"] as $service){
				$Configuration['Categorie'] = 'LAN';
				$Configuration['Domain'] = $service["vlan_domain"];
				$Configuration['Type'] = $service["vlan_type"];
				$Configuration['MTU'] = $service["vlan_mtu"];
				$this->AddCommande($service["vlan_name"],$service["vlan_vlan"],"info", 'string','',$Configuration);
				foreach($service as $cmd => $value){
					$this->checkAndUpdateCmd($cmd,$value);
				}
			}
		}
	}
	public function postSave() {
		if($this->getConfiguration('ARP')){
			$Configuration=array('Categorie'=>'ARP');
			$this->AddCommande('ARP>Port','port_id',"info", 'string','',$Configuration);
			$this->AddCommande('ARP>MAC','mac_address',"info", 'string','',$Configuration);
			$this->AddCommande('ARP>IP','ipv4_address',"info", 'string','',$Configuration);
			$this->AddCommande('ARP>Nom de contexte','context_name',"info", 'string','',$Configuration);
		}
		if($this->getConfiguration('Services')){
			$Configuration=array('Categorie'=>'Services');
			$this->AddCommande('Services>id','service_id',"info", 'string','',$Configuration);
			$this->AddCommande('Services>IP','service_ip',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Type','service_type',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Descendant','service_desc',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Parametre','service_param',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Ignore','service_ignore',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Status','service_status',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Change','service_changed',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Message','service_message',"info", 'string','',$Configuration);
			$this->AddCommande('Services>Activation','service_disabled',"info", 'string','',$Configuration);
			$this->AddCommande('Services>DS','service_ds',"info", 'string','','Services');
		}
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
	public function AddCommande($Name,$LogicalId,$Type="info", $SousType='numeric',$Unite='',$Configuration=array()) {
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
		foreach($Configuration as $Parameter => $Name)
			$Commande->setConfiguration($Parameter,$Name);
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
