<?php 
$Page = ComponentController::cast($this);
?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="ResendConfirmationEmailForm" name="ResendConfirmationEmail" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Resend email</h2>
			<p>Please provide your password to resend your confirmation email.</p>
			<input 
				type="password" 
				id="ResendConfirmationEmail_password"
				name="ResendConfirmationEmail[password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter your current password." 
				placeholder="Current Password"
				value=""
			>
						
			<button class="btn btn-large btn-primary" type="submit">Resend</button>
		</div>
		</form>
	</div>
</div>


