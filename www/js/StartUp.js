
// Set some global config/path settings here for other scripts/modules to benefit from

requirejs.config({
	baseUrl: '/js/', // This happens by default anyway since this folder is the one where StartUp resides
	paths: {
		'jquery':':empty',
		'bootstrap': '/js/Libraries/Bootstrap/bootstrap.min', // Bootstrap minified
		'can': '/js/Libraries/CanJS/can'
	}
});

require( 
	['jquery','can','bootstrap'],
	
	// This method will be called when all the dependencies listed in the first parameter for the require call are loaded. 
	function($, can) {
		// Write all your scripts that handle UI/UX for the main template here.
		
		// Example CanJS code from RequireJS loaded Can module(s)
		
		var Todo = can.Construct({
		  init: function( text ) {
		    this.text = text;
		  },
		  read: function() {
			if(typeof console != 'undefined' && typeof console.log != 'undefined'){
			    console.log( this.text );
			}
		  }
		});
		  
		var todo = new Todo( 'Hello World' );
		todo.read();
		
		// (check your browsers js console for "Hello World" message)
		
	}
);

