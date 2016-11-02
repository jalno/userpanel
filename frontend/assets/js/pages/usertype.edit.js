jQuery(function($){
	$(".priorityedit").click(function(){
		var priority = $(this).parents("tr").data("priority");
		$("#priorityedit").val(priority);
		$("#priorityEditForm").attr("action", "/fa/userpanel/tools/children/edit/"+priority);
	});
	$(".permissionedit").click(function(){
		var permission = $(this).parents("tr").data("permission");
		$("input[name=name]").val(permission);
		$("#permissionEditForm").attr("action", "/fa/userpanel/tools/permissions/edit/"+permission);
	});
})
