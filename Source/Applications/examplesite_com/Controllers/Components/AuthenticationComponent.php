<?php 

class AuthenticationComponent extends Component{
	// this returns an object of type "user" or "UserAccount" and then the rest of the apps call it in the normal way. 
	// upon login, this thing has the data in it that will be cached in a system cache at the end of the page request,
	// and it just builds and builds until you logout or it expires. so now we can have caches of any sort stuck in this object
	// for anything related to the user and we dont have to update them until the end of the page request, but all that data is 
	// still available if the page needs it so there should be less query logic and page logic for this system cache idea i was going to do.
	// 
	// Also, because this is the login component here, it will have the functions / methods to set, unset, whatever cookies and sessions
	// and nothing will need to get cached immediately. This Authentication component should remain pretty easy to edit but
	// at the same time it should try to keep as much of the additional functionality it can outside itself
	
}