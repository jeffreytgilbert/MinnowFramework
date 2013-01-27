<?php 
$Page = ComponentController::cast($this);
?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="RequestForm" name="Request" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Request reset code</h2>
			<p>Please enter the e-mail address associated with your account in the field below and you will be sent a new password:</p>
			<input 
				type="text" 
				id="Request_email"
				name="Request[email]"
				class="input-block-level required email" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid email address." 
				placeholder="Email address"
				value="<?= $Page->getInput('Request')->getStringAsHTMLEntities('email') ?>"
			>
						
			<button class="btn btn-large btn-primary" type="submit">Sign up</button>
		</div>
		</form>
	</div>
</div>
