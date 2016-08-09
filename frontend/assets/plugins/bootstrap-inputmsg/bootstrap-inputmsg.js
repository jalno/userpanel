jQuery.fn.inputMsg = function(options){
	if(typeof options == 'object'){
		var settings = $.extend({
			type: "error"
		}, options );

		this.each(function(){
			var $parent = $(this).parent();
			$parent.removeClass('has-error');
			$parent.removeClass('has-success');
			$parent.removeClass('has-warning');
			$parent.removeClass('has-info');
			$parent.addClass("has-"+settings.type);
			$('.help-block',$parent).remove();
			if(settings.hasOwnProperty('message')){
				$(this).after('<span class="help-block">'+settings.message+'</span>');
			}
		});
	}else if(typeof options == 'string' && options == 'reset'){
		this.each(function(){
			var $parent = $(this).parent();
			$parent.removeClass('has-error');
			$parent.removeClass('has-success');
			$parent.removeClass('has-warning');
			$parent.removeClass('has-info');
			$('.help-block',$parent).remove();
		});
	}
}
