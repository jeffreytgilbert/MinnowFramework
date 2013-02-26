<?php 
$Page = ComponentController::cast($this);
?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="ChangePasswordForm" name="ChangePassword" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Change my password</h2>
			<p>Please enter the e-mail address associated with your account in the field below and you will be sent a new password:</p>
			<input 
				type="password" 
				id="ChangePassword_current_password"
				name="ChangePassword[current_password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter your current password." 
				placeholder="Current Password"
				minlength="6"
			>
			<input 
				type="password" 
				id="ChangePassword_new_password"
				name="ChangePassword[new_password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter a valid password." 
				placeholder="New Password"
				minlength="6"
			>
						
			<button class="btn btn-large btn-primary" type="submit">Change Password</button>
		</div>
		</form>
	</div>
</div>


