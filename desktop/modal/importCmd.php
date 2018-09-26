<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}

?>
  
  
<div class="row">  	
	<div class="col-md-12"> 
		<p>Cette option du plugin permet d'importer automatiquement votre device sous Jeedom.</p>
        <input type="text" class="eqLogicAttr form-control" data-l1key="name" />
        <a class="btn btn-primary deviceHealth"><i class="fa fa-minus-circle"></i> {{start}}</a>
	
	</div>  
</div>

<script>
$('.deviceHealth').on('click',function(){
	$.ajax({
		async: false,
		type: 'POST',
		url: 'plugins/libreNMS/core/ajax/libreNMS.ajax.php',
		data:{
			action: 'getDeviceHealth',
			id: $('.eqLogicAttr[data-l1key=id]').val()
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
