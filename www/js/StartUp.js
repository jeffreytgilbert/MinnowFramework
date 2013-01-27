
// Set some global config/path settings here for other scripts/modules to benefit from

requirejs.config({
	baseUrl: '/js/', // This happens by default anyway since this folder is the one where StartUp resides
	paths: {
		'jquery':':empty',
		'bootstrap': '/js/Libraries/Bootstrap/bootstrap.min' // Bootstrap minified
	}
});

require( 
	['jquery','bootstrap'],
	
	// This method will be called when all the dependencies listed in the first parameter for the require call are loaded. 
	function($) {
		// Write all your scripts that handle UI/UX for the main template here.
		
	}
);

