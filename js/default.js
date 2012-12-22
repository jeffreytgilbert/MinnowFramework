/* Default theme javascript */

function ValidateForm(jquery_selector, popovers){
	var errorStates = [];
	
	if(typeof popovers == 'undefined'){ popovers = []; }
	
	$(jquery_selector).validate({
		errorClass:'error',
		validClass:'success',
		errorElement:'span',
		highlight: function (element, errorClass) {
			if($.inArray(element, errorStates) == -1){
				errorStates[errorStates.length] = element;
				$(element).popover('show');
			}
		},
		unhighlight: function (element, errorClass, validClass) {
			if($.inArray(element, errorStates) != -1){
				this.errorStates = $.grep(errorStates, function(value) {
				  return value != errorStates;
				});
				$(element).popover('hide');
			}
		},
		errorPlacement: function(err, element) {
			err.hide();
		}
	});
	
	$.each(popovers,function(index,value){
		$(value).popover({
			placement: 'right',
			offset: 20,
			trigger: 'manual'
		});
	});
}