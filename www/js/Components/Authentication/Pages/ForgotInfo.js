////////////////////////////////////////////////////////////////
// This is the Login.js for the Authentication Component
////////////////////////////////////////////////////////////////

$(document).ready(function(){
	
	ValidateForm('#RequestForm', ['#Request_email']);
	ValidateForm('#ResetForm', ['#Reset_reset_code','#Reset_password']);
	
});