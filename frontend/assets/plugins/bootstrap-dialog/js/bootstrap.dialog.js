(function($) {
	$.extend({
		confirmation: function(options){
			console.log("Salam");
			var $html  = "<div class=\"modal fade\" id=\"modal-confrim\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">";
					$html += "<div class=\"modal-dialog\">";
						$html += "<div class=\"modal-content\">";
							$html += "<div class=\"modal-header\">";
								$html += "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>";
								$html += "<h4 class=\"modal-title\">"+options.title+"</h4>";
							$html += "</div>";
							$html += "<div class=\"modal-body\">";
								$html += "<p>"+options.text+"</p>";
							$html += "</div>";
							$html += "<div class=\"modal-footer\">";
								$html += " <button class=\"btn btn-warning btn-confirm\" data-dismiss=\"modal\">تائید</button>";
								$html += " <button aria-hidden=\"true\" data-dismiss=\"modal\" class=\"btn btn-default\">انصراف</button>";
							$html += "</div>";
						$html += "</div>";
					$html += "</div>";
				$html += "</div>";
			var dfd = $.Deferred();
			var $modal = $($html).appendTo('body');
			$('.btn-confirm').on('click', function(){
				dfd.resolve();
				$modal.modal('hide');
			});
			$modal.on('hidden.bs.modal', function(){
				dfd.reject();
			});

			$modal.modal('show');
			return dfd.promise();
		}
	});
})(jQuery);
