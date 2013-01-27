<?php 
$Page = ComponentController::cast($this);
?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="ResetForm" name="Reset" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Reset password</h2>
			<p>Please enter the e-mail address associated with your account in the field below and you will be sent a new password:</p>

			<input 
				type="text" 
				id="Reset_reset_code"
				name="Reset[reset_code]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid reset code." 
				placeholder="Email address"
				value="<?= $Page->getInput('Reset')->getStringAsHTMLEntities('reset_code') ?>"
			>
			<input 
				type="password" 
				id="Reset_password"
				name="Reset[password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a password of at least 6 characters in length." 
				placeholder="Password" 
				minlength="6"
			>
						
			<button class="btn btn-large btn-primary" type="submit">Sign up</button>
		</div>
		</form>
	</div>
</div>

