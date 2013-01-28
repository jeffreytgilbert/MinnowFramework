
require(
	['jquery', 'Modules/Minnow/ValidateForm'],
	function($, ValidateForm){
		ValidateForm('#SubRegistrationForm', ['#SubRegistration_unique_identifier','#SubRegistration_password']);
	}
);

