
require(
	['jquery', 'Modules/Minnow/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#LoginForm', ['#Login_unique_identifier','#Login_password']);
	}
);

