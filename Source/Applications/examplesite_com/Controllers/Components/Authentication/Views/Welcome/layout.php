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
?>

<div class="row show-grid">
	
	<div class="span6">
		<h1>Hello! </h1>
		<p>You've just created an account. Thanks for testing the Minnow Framework. Please download a copy and try it out for yourself. </p>
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