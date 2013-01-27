
require(
	['jquery', 'Modules/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#LoginForm', ['#Login_unique_identifier','#Login_password']);
	}
);

