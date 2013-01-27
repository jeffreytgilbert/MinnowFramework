
require(
	['jquery', 'Modules/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#RegistrationForm', ['#Registration_unique_identifier','#Registration_password']);
	}
);

