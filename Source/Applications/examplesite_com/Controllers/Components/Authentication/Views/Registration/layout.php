<?php 
$Page = ComponentController::cast($this);
$jump = $Page->getParentComponent()->getConfig()->get('hybrid_auth_request_page_url');
$provider_collection = array_keys(AuthenticationComponent::cast($Page->getParentComponent())->getProviderList());
?>

<?php foreach($Page->getErrors() as $error): ?>

	<div class="alert alert-error"><span class="label label-important">&nbsp;!&nbsp;</span> <?= $error; ?></div>

<?php endforeach; ?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="RegistrationForm" name="Registration" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Sign up</h2>
			<input 
				type="text" 
				id="Registration_unique_identifier"
				name="Registration[unique_identifier]"
				class="input-block-level required email" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid email address." 
				placeholder="Email address"
				value="<?= $Page->getInput('Registration')->getStringAsHTMLEntities('unique_identifier') ?>"
			>
			<input 
				type="password" 
				id="Registration_password"
				name="Registration[password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a password of at least 6 characters in length." 
				placeholder="Password" 
				minlength="6">
			<button class="btn btn-large btn-primary" type="submit">Sign up</button>
		</div>
		</form>
	</div>
	
	<div class="span5"> 
		
		<p>Or log in with:</p> 
		
		<div>
			
			<?php foreach($provider_collection as $provider): ?>
			
			<?php if($provider == 'Live'): ?>
			
			<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial windows"><?= $provider ?></a> 
			
			<?php else: ?>
			
			<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial <?= strtolower($provider) ?>"><?= $provider ?></a> 
			
			<?php endif; ?>
			
			<?php endforeach; ?>
			
			<!--  
			<a href="<?= $jump ?>?provider=Facebook" class="zocial facebook">Facebook</a>
			<a href="<?= $jump ?>?provider=Twitter" class="zocial twitter">Twitter</a>  
			<a href="<?= $jump ?>?provider=Yahoo" class="zocial yahoo">Yahoo!</a> 
			<a href="<?= $jump ?>?provider=MySpace" class="zocial myspace">Myspace</a>  
			<a href="<?= $jump ?>?provider=Live" class="zocial windows">Windows Live</a> 
			<a href="<?= $jump ?>?provider=LinkedIn" class="zocial linkedin">LinkedIn</a>
			<a href="<?= $jump ?>?provider=Foursquare" class="zocial foursquare">Foursquare</a>  
			<a href="<?= $jump ?>?provider=AOL" class="zocial aol">AOL</a>  
	 		
			<a href="<?= $jump ?>?provider=Github" class="zocial github">Github</a>  
			<a href="<?= $jump ?>?provider=Gowalla" class="zocial gowalla">Gowalla</a>  
			<a href="<?= $jump ?>?provider=Lastfm" class="zocial lastfm">Last.fm</a>  
			<a href="<?= $jump ?>?provider=Vimeo" class="zocial vimeo">Vimeo</a>  
			<a href="<?= $jump ?>?provider=Tumblr" class="zocial tumblr">Tumblr</a>  
			<a href="<?= $jump ?>?provider=Viadeo" class="zocial viadeo">Viadeo</a>   
			-->
		</div> 
	</div>
</div>