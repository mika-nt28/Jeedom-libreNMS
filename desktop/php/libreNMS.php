<?php
	if (!isConnect('admin')) {
		throw new Exception('{{401 - Accès non autorisé}}');
	}
	$plugin = plugin::byId('libreNMS');
	sendVarToJS('eqType', $plugin->getId());
	$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">    
   	<div class="col-xs-12 eqLogicThumbnailDisplay">
  		<legend><i class="fas fa-cog"></i>  {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor libreNMSAction logoPrimary" data-action="import">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Importer}}</span>
			</div>
      			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
      				<i class="fas fa-wrench"></i>
    				<br>
    				<span>{{Configuration}}</span>
  			</div>
      			<div class="cursor eqLogicAction logoSecondary" data-action="healthlibreNMS">
      				<i class="fas fa-medkit"></i>
    				<br>
    				<span>{{Santé}}</span>
  			</div>
  		</div>
  		<legend><i class="fas fa-table"></i> {{Mes appareils}}</legend>
	   	<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div class="eqLogicThumbnailContainer">
    		<?php
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				if ($eqLogic->getConfiguration('type') != '') {
					echo '<img src="plugins/libreNMS/core/config/devices/'.$eqLogic->getConfiguration('type').'.png">';
				} else echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
		?>
		</div>
	</div>
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure">
					<i class="fa fa-cogs"></i>
					 {{Configuration avancée}}
				</a>
				<a class="btn btn-default btn-sm eqLogicAction" data-action="copy">
					<i class="fas fa-copy"></i>
					 {{Dupliquer}}
				</a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save">
					<i class="fas fa-check-circle"></i>
					 {{Sauvegarder}}
				</a>
				<a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove">
					<i class="fas fa-minus-circle"></i>
					 {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
    			<li role="presentation">
				<a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay">
					<i class="fa fa-arrow-circle-left"></i>
				</a>
			</li>
    			<li role="presentation" class="active">
				<a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab">
				<i class="fas fa-tachometer-alt"></i> 
					{{Equipement}}
				</a>
			</li>
    			<li role="presentation">
				<a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab">
					<i class="fa fa-list-alt"></i> 
					{{Commandes}}
				</a>
			</li>
  		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
      				<br/>
    				<form class="form-horizontal">
					<fieldset>
						<div class="form-group ">
							<label class="col-sm-3 control-label">{{Nom du serveur}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du serveur}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label" >{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
										foreach (jeeObject::all() as $object) 
											echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline">
									<input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
									{{Activer}}
								</label>
								<label class="checkbox-inline">
									<input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
									{{Visible}}
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{IP du serveur}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="logicalId" placeholder="{{IP du serveur}}"/>
							</div>
						</div>
						<div class="form-group">
						    <label class="col-sm-3 control-label">{{Type}}</label>
						    <div class="col-sm-3">
						    <select id="sel_icon" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="type">
							<option value="">{{Aucun}}</option>
							<option value="server">{{Serveur}}</option>
							<option value="network">{{Réseau}}</option>
							<option value="wireless">{{Réseau sans fil}}</option>
							<option value="firewall">{{Pare feu}}</option>
							<option value="power">{{Alimentation}}</option>
							<option value="environment">{{Environnement}}</option>
							<option value="loadbalancer">{{Equilibreur de charge}}</option>
							<option value="storage">{{Disque}}</option>
							<option value="printer">{{Imprimante}}</option>
							<option value="appliance">{{Appliance}}</option>
							<option value="collaboration">{{Collaboration}}</option>
							<option value="workstation">{{Station de travail}}</option>
						    </select>
						    </div>
						</div>
 						<div class="form-group">
							<label class="col-sm-3 control-label">{{Lieu}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="location" placeholder="{{location}}"/>
							</div>
						</div> 

						<div class="form-group">
							<label class="col-sm-3 control-label">{{Version SNMP}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="snmpver" placeholder="{{Version SNMP}}"/>
							</div>
						</div>     
               					<div class="form-group">
							<label class="col-sm-3 control-label">{{port}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="port" placeholder="{{port}}"/>
							</div>
						</div>   
                				<div class="form-group">
							<label class="col-sm-3 control-label">{{transport}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="transport" placeholder="{{transport}}"/>
							</div>
						</div>   
                				<div class="form-group">
							<label class="col-sm-3 control-label">{{sysObjectID}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="sysObjectID" placeholder="{{sysObjectID}}"/>
							</div>
						</div>  
						<div class="form-group">
						    <label class="col-sm-3 control-label">{{Import}}</label>
						    <div class="col-sm-3">
						      <a class="btn btn-default" id="bt_import"><i class="fa fa-cogs"></i> Importer les commandes</a>
						    </div>
						</div>   
						<div class="form-group">
							<label class="col-sm-3 control-label" >
								{{Type d'information}}
								<sup>
									<i class="fa fa-question-circle tooltips" title="{{Choisir le type d'information que vous souhaité remonter}}" style="font-size : 1em;color:grey;"></i>
								</sup>
							</label>
							<div class="col-sm-8">
								<?php
									foreach (libreNMS::$_TypesInfo as $TypeInfo) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="' . $TypeInfo . '" />' . $TypeInfo;
										echo '</label>';
									}
								?>
							</div>
						</div>	
					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">	
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Nom}}</th>
							<th>{{Options}}</th>
							<th>{{Type}}</th>
							<th>{{Unité}}</th>
							<th>{{Action}}</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php include_file('desktop', 'libreNMS', 'js', 'libreNMS');?>
<?php include_file('core', 'plugin.template', 'js');?>
