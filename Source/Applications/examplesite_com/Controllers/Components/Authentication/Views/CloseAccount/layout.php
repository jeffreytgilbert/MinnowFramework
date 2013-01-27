<?php 
$Page = ComponentController::cast($this);
?>
		
<div class="row show-grid">
	
	<div class="span5">
		<form action="?" method="post" id="CloseAccountForm" name="CloseAccount" class="form-signin">
		
		<div>
			<h2 class="form-signin-heading">Close Account</h2>
			<p>Please provide your password to close this account.</p>
			<input 
				type="password" 
				id="CloseAccount_password"
				name="CloseAccount[password]"
				class="input-block-level required" 
				required="required" 
				rel="popover" 
				data-content="Please enter your current password." 
				placeholder="Current Password"
				minlength="6"
			>
						
			<button class="btn btn-large btn-primary" type="submit">Close My Account</button>
		</div>
		</form>
	</div>
</div>


