
requirejs.config({
    shim: {
        'Libraries/jQuery.Validate/additional-methods': ['Libraries/jQuery.Validate/jquery.validate'],
    }
});

define(
	[
	 'jquery', 
	 'Libraries/jQuery.Validate/jquery.validate', 
	 'Libraries/jQuery.Validate/additional-methods'
	],
		
	function($){

		return function(jquery_selector, popovers){
			var errorStates = [];
			
			if(typeof popovers == 'undefined'){ popovers = []; }
			
			$(jquery_selector).validate({
				errorClass:'error',
				validClass:'success',
				errorElement:'span',
				highlight: function (element, errorClass) {
					if($.inArray(element, errorStates) == -1){
						errorStates[errorStates.length] = element;
						$.each(errorStates,function(index,value){
							$(value).popover('hide');
						});
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
		};
	}
);