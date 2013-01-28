
require(
	['jquery', 'Modules/Minnow/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#RequestForm', ['#Request_email']);
		ValidateForm('#ResetForm', ['#Reset_reset_code','#Reset_password']);
	}
);

