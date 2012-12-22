<?php 
$Page = ComponentController::cast($this);
$jump = $Page->getParentComponent()->getConfig()->get('hybrid_auth_request_page_url');
$provider_collection = array_keys(AuthenticationComponent::cast($Page->getParentComponent())->getProviderList());
?>

<div>
	<form action="?" method="post" name="LoginForm" class="form-signin">
	<div>
		<h2 class="form-signin-heading">Please sign in</h2>
        <input type="text" class="input-block-level" placeholder="Email address">
        <input type="password" class="input-block-level" placeholder="Password">
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Remember me
        </label>
        <button class="btn btn-large btn-primary" type="submit">Sign in</button>
	</div>
	</form>
</div>

<div> 
	
	<p>Or log in with:</p> 
	
	<div>
		
		<?php foreach($provider_collection as $provider): ?>
		
		<?php if($provider == 'Live'): ?>
		
		<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial windows"><?= $provider ?></a> 
		
		<?php else: ?>
		
		<a href="<?= $jump ?>?provider=<?= $provider ?>" class="zocial <?= strtolower($provider) ?>"><?= $provider ?></a> 
		
		<?php endif; ?>
		
		<?php endforeach; ?>
		
		<br><br>
		Login functionality provided by HybridAuth. Logo font by Zocial.
		
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