function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="name"></td>';
    tr += '<td>';
    tr += '<span><input type="checkbox" class="cmdAttr" data-l1key="isVisible" checked/>{{Afficher}}</span> ';
    tr += '</td>';
    tr += '<td>';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="type" style="display : none;">';
    tr += '<input class="cmdAttr form-control input-sm" data-l1key="subType" style="display : none;">';
    if (is_numeric(_cmd.id)) {
        tr += '<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure"><i class="fa fa-cogs"></i></a> ';
        tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
    }
    tr += '<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove"></i></td>';
    tr += '</tr>';
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
$('.libreNMSAction[data-action=import]').on('click', function () {
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
			location.reload();
		}
	});
});
$('#bt_import').on('click', function () {
	$('#md_modal').dialog({
		title: "{{Import des différentes commandes du device}}",
		resizable: true,
		height: 700,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=importCmd&plugin=libreNMS').dialog('open');
});
$('.eqLogicAction[data-action=healthlibreNMS]').on('click', function () {
	$('#md_modal').dialog({
		title: "{{Santé du plugin}}",
		resizable: true,
		height: 700,
		width: 850});
	$('#md_modal').load('index.php?v=d&modal=health&plugin=libreNMS').dialog('open');
});
