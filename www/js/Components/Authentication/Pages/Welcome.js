
require(
	['jquery', 'Modules/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#SubRegistrationForm', ['#SubRegistration_unique_identifier','#SubRegistration_password']);
	}
);

