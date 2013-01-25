<?php 
$Page = ComponentController::cast($this);
$jump = 'http://minnow.badpxl.com/Account/-/HybridAuthAddConnection';
$provider_array = array_keys(AuthenticationComponent::cast($Page->getParentComponent())->getProviderList());
$connected_provider_array = AuthenticationComponent::cast($Page->getParentComponent())->getConnectedProviderList();
$non_connected_provider_array = array();
foreach($provider_array as $provider){
	if(!in($provider,$connected_provider_array)){
		$non_connected_provider_array[] = $provider;
	}
}

$ID = AuthenticationComponent::cast($Page->getParentComponent())->identifyUser();
?>

<div class="row show-grid">
	
	<div class="span5">
		<h1>Hello! </h1>
		<p>You've just created an account. Thanks for testing the Minnow Framework. Please download a copy and try it out for yourself. </p>
		
		<?php if( $ID->getUserAccount()->getString('password_hash') == '' ): ?>
		
		<h2>Secure your account!</h2>
		<p>
			Looks like you haven't secured your account yet. 
			To make sure you can still access your account if your linked account goes rogue, 
			please suply us with an email address and a password to fall back on. 
		</p>
		
		<div style="clear:both">&nbsp;</div>
		
		<form action="SecureAccount?" method="post" id="SubRegistrationForm" name="SubRegistration" class="form-signin">
		
		<div>
			<input 
				type="text" 
				id="SubRegistration_first_name"
				name="SubRegistration[first_name]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid name." 
				placeholder="First name"
				value="<?= $Page->getInput('SubRegistration')->getStringAsHTMLEntities('first_name') ?>"
			>
			<input 
				type="text" 
				id="SubRegistration_last_name"
				name="SubRegistration[last_name]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid name." 
				placeholder="Last name"
				value="<?= $Page->getInput('SubRegistration')->getStringAsHTMLEntities('last_name') ?>"
			>
			<input 
				type="text" 
				id="SubRegistration_unique_identifier"
				name="SubRegistration[unique_identifier]"
				class="input-block-level required email" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid email address." 
				placeholder="Email address"
				value="<?= $Page->getInput('SubRegistration')->getStringAsHTMLEntities('unique_identifier') ?>"
			>
			<input 
				type="password" 
				id="SubRegistration_password"
				name="SubRegistration[password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a password of at least 6 characters in length." 
				placeholder="Password" 
				minlength="6"
			>
			<button class="btn btn-large btn-primary" type="submit">Secure My Account</button>
		</div>
		</form>
		
		<?php endif; ?>
		
	</div>
	
	<div class="span4">
		<h3>Connected Providers:</h3>
		<div>
		
			<?php foreach($connected_provider_array as $provider): ?>
			
			<?php if($provider == 'Live'): ?>
			
			<a href="/" class="zocial windows"><?= $provider ?></a> 
			
			<?php else: ?>
			
			<a href="/" class="zocial <?= strtolower($provider) ?>"><?= $provider ?></a> 
			
			<?php endif; ?>
			
			<?php endforeach; ?>
			
		</div>
		
		<?php if(count($non_connected_provider_array) > 0){ ?>
			
		<div>
			<h3>Link Accounts:</h3>
		
			<?php foreach($non_connected_provider_array as $provider): ?>
			
			<?php if($provider == 'Live'): ?>
			
			<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial windows"><?= $provider ?></a> 
			
			<?php else: ?>
			
			<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial <?= strtolower($provider) ?>"><?= $provider ?></a> 
			
			<?php endif; ?>
			
			<?php endforeach; ?>
			
		</div>
		
		<?php } ?>
		
	</div>
</div>