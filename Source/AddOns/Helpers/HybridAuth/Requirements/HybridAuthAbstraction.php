<?php 

class HybridAuthAbstraction{
	
	private 
		$_config,
		$_hybridAuth;
	
	public function __construct($config){
		$this->_config = $config;
		$this->_hybridAuth = new Hybrid_Auth( $config );
	}
	
	public function endpoint(){
		require_once( "Hybrid/Endpoint.php" );
		
		Hybrid_Endpoint::process();
	}
	
	public function authenticate(){
		ob_start();
		try{
			// create an instance for Hybridauth with the configuration file path as parameter
				
			// set selected provider name
			//$provider = @ trim( strip_tags( $_GET["provider"] ) );
				
			$provider = 'Facebook';
				
			// try to authenticate the selected $provider
			$adapter = $this->_hybridAuth->authenticate( $provider );

			// if okay, we will redirect to user profile page
			$this->_hybridAuth->redirect( "/" );
		} catch( Exception $e ){
		// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to
		// let hybridauth forget all about the user so we can try to authenticate again.
			
		// Display the recived error,
		// to know more please refer to Exceptions handling section on the userguide
		switch( $e->getCode() ){
		case 0 : $error = "Unspecified error."; break;
				case 1 : $error = "Hybriauth configuration error."; break;
				case 2 : $error = "Provider not properly configured."; break;
				case 3 : $error = "Unknown or disabled provider."; break;
				case 4 : $error = "Missing provider application credentials."; break;
				case 5 : $error = "Authentification failed. The user has canceled the authentication or the provider refused the connection."; break;
				case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
				$adapter->logout();
		break;
		case 7 : $error = "User not connected to the provider.";
				$adapter->logout();
				break;
		}
			
			// well, basically your should not display this to the end user, just give him a hint and move on..
			$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
			$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
		}
		
		if( $error ){
			echo '<p><h3 style="color:red">Error!</h3>' . $error . '</p>';
			echo "<pre>Session:<br />" . print_r( $_SESSION, true ) . "</pre><hr />";
		}
		
		// try to get already authenticated provider list
		try{
			
			$connected_adapters_list = $this->_hybridAuth->getConnectedProviders();
			
			if( count( $connected_adapters_list ) ){
			?>
		    <td align="left" valign="top">  
				<fieldset>
					<legend>Providers you are logged with</legend>
					<?php
						foreach( $connected_adapters_list as $adapter_id ){
							echo '&nbsp;&nbsp;<a href="profile.php?provider=' . $adapter_id . '">Switch to <b>' . $adapter_id . '</b>  account</a><br />'; 
						}
					?> 
				</fieldset> 
			</td>		
			<?php
			}
		}
		catch( Exception $e ){
			echo "Ooophs, we got an error: " . $e->getMessage();
	
			echo " Error code: " . $e->getCode();
	
			echo "<br /><br />Please try again.";
	
			echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>"; 
		}
		
		$contents = ob_get_contents();
		ob_flush();
		
		return $contents;
	}
	
	
}