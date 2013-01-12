<?php 

final class EmailActions extends Actions
{
	public static function send($to, $from_title, $subject, $body, $template_name=null, $from_address=null)
	{
		// These are the addresses we've whitelisted. Don't send from anyplace else.
		
		if(isset($template_name))
		{
			switch($template_name)
			{
				case 'default': $template_name='default'; break;
				case 'newsletter': $template_name='newsletter'; break;
				default: $template_name='default'; break;
			}
		}
		else { $template_name='default'; }
		
		$template = new TemplateParser();
		$template->load('Emails/Templates/'.$template_name.'.htm');
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'mailer_contents' => $body
			)
		);
		
		if(1){ // toggle this on when you want to use postmark (which should be almost always since NOBODY accepts emails from randoms anymore)
			$RuntimeInfo = RuntimeInfo::instance();
			$Postmark = RuntimeInfo::instance()->getConnections()->Postmark();
			$Postmark->to($to)->subject($from_title.': '.$subject)->html_message($body)->send();
			return true;
		} else {
			$Emailer = RuntimeInfo::instance()->getHelpers()->Email();
			
			$Emailer->SetFrom($Emailer->getConfig()->getString('webmaster_email'),$from_title);
			$Emailer->AddAddress($to);
			$Emailer->Subject = $subject;
			$Emailer->MsgHTML($body);
			$Emailer->AltBody = "To view the message, please use an HTML compatible email viewer!";
			return $Emailer->Send();
		}
	}
	
	public static function sendRegistrationNotice($email, $user_id, $login_name){
		// Email someone a registration notice to let them know they're now part of the family
		$template = new TemplateParser();
		$template->load('Emails/registration.htm');
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'login_name' => strip_tags($login_name),
				'user_id' => intval($user_id)
			)
		);
		
		return EmailActions::send(
			$email,
			'Registration',
			'Confirmation: Your account has been created for '.RuntimeInfo::instance()->appSettings()->get('site_domain').'.',
			$body
		);
	}
	
	public static function sendEmailValidationRequest($email, $user_id, $first_name, $last_name)
	{
		$Result = new EmailValidation(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT *
			FROM email_validation 
			WHERE user_id=:user_id',
			array(
				':user_id'=>$user_id
			),
			array(
				':user_id'
			)
		));
		
		if($Result->getString('code') != '') { $registration_code=$Result->getString('code'); }
		else
		{
			mt_srand(microtime(true));
			$registration_code=self::createRandomCode();
			
			parent::MySQLCreateAction('
				INSERT INTO email_validation 
					(code, email_address, user_id, created_datetime)
				VALUES
					(:code, :email_address, :user_id, :right_now_gmt)',
				array(
					':code'=>$registration_code,
					':user_id'=>$user_id,
					':email_address'=>$email,
					':right_now_gmt'=>RuntimeInfo::instance()->now()->getMySQLFormat('datetime')
				),
				array(
					':user_id'
				)
			);
		}

		// Email someone a request to have them confirm their email address
		$template = new TemplateParser();
		$template->load('Emails/email_validation.htm');
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'first_name' => strip_tags($first_name),
				'last_name' => strip_tags($last_name),
				'user_id' => intval($user_id),
				'email_code' => $registration_code
			)
		);
		
		return EmailActions::send(
			$email,
			'Validation',
			'Please verify your email address to unlock your account.',
			$body
		);
	}

// 	public static function sendEmailInvitation($email, $user_id, $login_name, $invitation)
// 	{
// 		$Result = parent::MySQLReadAction('
// 			SELECT user_id 
// 			FROM reservation 
// 			WHERE user_id=:user_id AND email LIKE :email',
// 			array(
// 				':user_id'=>$user_id,
// 				':email'=>$email
// 			),
// 			array(
// 				':user_id'
// 			)
// 		)->getItemAt(0);
		
// 		if(isset($Result) && $Result->getData('user_id','Is::set')) { return false; } // already reserved
		
// 		parent::MySQLUpdateAction('
// 			UPDATE user_registry 
// 			SET sent_invitations=IFNULL(sent_invitations,0)+1 
// 			WHERE user_id=:user_id',
// 			array(
// 				':user_id'=>$user_id
// 			),
// 			array(
// 				':user_id'
// 			)
// 		);
		
// 		$Result = parent::MySQLReadAction('
// 			SELECT user_id 
// 			FROM reservation 
// 			WHERE user_id=:user_id AND email LIKE :email',
// 			array(
// 				':user_id'=>$user_id,
// 				':email'=>$email
// 			),
// 			array(
// 				':user_id'
// 			)
// 		)->getItemAt(0);
		
// 		if(isset($Result) && $Result->getData('user_id','Is::set'))
// 		{
// 			$target_id=$Result->get('user_id');
// 			$target_name=$Result->getData('login_name');
// 			FriendActions::insert($target_id,null,null,null,$user_id);
// //				'[img left]http://thedilly.com/public/small/'
// //				.user_path($ID->get('user_id')).'/'.$ID->getData('avatar').'.jpg[/img]'

// 			MessengerActions::insertNotice($target_id,
// 				'[bold]You have a new admirer![/bold]'."\n\n"
// 				.'A friend of yours recently attempted '
// 				.'to invite you by referencing the email address '
// 				.'linked with this account. '."\n\n"
// 				.'If you\'d like to remove yourself from '
// 				.'[url=http://thedilly.com/view.user.profile.wtd?id='.(int)$user_id.']'.$login_name.'\'s[/url]'
// 				.' friend list you can do that on under '
// 				.'[url=http://thedilly.com/my.account.wtd]Edit[/url] > '
// 				.'[url=http://thedilly.com/my.contacts.wtd]Contacts[/url] > '
// 				.'[url=http://thedilly.com/my.contacts.admirers.wtd]Admirers[/url].');
				
// 			MessengerActions::insertNotice($user_id,
// 				'[bold]We found your friend![/bold]'."\n\n"
// 				.'It turns out your friend is already a member on theDilly. '."\n\n"
// 				.'If you\'d like to remove '
// 				.'[url=http://thedilly.com/view.user.profile.wtd?id='.(int)$target_id.']'.$target_name.'[/url]'
// 				.' from your friends list you can do that on under '
// 				.'[url=http://thedilly.com/my.account.wtd]Edit[/url] > '
// 				.'[url=http://thedilly.com/my.contacts.wtd]Contacts[/url] > '
// 				.'[url=http://thedilly.com/my.contacts.friends.wtd]Friends[/url].');
// 			return true;
// 		}
// 		else
// 		{
// 			parent::MySQLCreateAction('
// 				INSERT INTO reservation 
// 				SET 
// 					user_id=:user_id,
// 					created_datetime=:created_datetime,
// 					email=:email',
// 				array(
// 					':user_id'=>$user_id,
// 					':created_datetime'=>RIGHT_NOW_GMT,
// 					':email'=>$email
// 				),
// 				array(
// 					':user_id'
// 				)
// 			);
			
// 			// Email someone a request to have them confirm their email address
// 			$template = new TemplateParser();
// 			$template->load('Emails/invitation.htm');
// 			$body=$template->parse(
// 				array(
// 					'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
// 					'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
// 					'login_name' => strip_tags($login_name),
// 					'user_id' => intval($user_id),
// 					'body' => $invitation
// 				)
// 			);
			
// 			return EmailActions::send($email, 'Invitation', 'no-reply@mail'.RuntimeInfo::instance()->appSettings()->get('site_domain'), $login_name.' sent you an invitation.', $body);
// 		}
// 	}
		
	public static function sendPasswordResetRequest($email, $user_id, $login_name, $pass_code){
		$template = new TemplateParser();
		$template->load('Emails/retrieve_info.htm');
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'email' => $email,
				'user_id' => $user_id,
				'login_name' => strip_tags($login_name),
				'pass_code' => $pass_code
			)
		);
		EmailActions::send(
			$email,
			'Password', 
			'Here\'s the password reset code you requested.',
			$body
		);
	}
	
	public static function sendContactUs($email, $note){
		$template = new TemplateParser();
		$template->load('Emails/feedback.htm');
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'email' => $email,
				'note' => htmlentities(strip_tags($note))
			)
		);
		EmailActions::send(
			RuntimeInfo::instance()->appSettings()->get('webmaster_email'),
			'Contact', 
			'Feedback from '.$email.'.',
			$body
		);
	}
	
	public static function sendUnsubscribeReqest($email){
		$template = new TemplateParser();
		$template->load('Emails/unsubscribe_request.htm');
		$str = base64_encode($email);
		$str = strrev($str);
		$code = base64_encode($str);
		$body=$template->parse(
			array(
				'site_name'=>RuntimeInfo::instance()->appSettings()->get('site_name'),
				'site_url'=>RuntimeInfo::instance()->appSettings()->get('site_domain'),
				'email' => $email,
				'code' => $code
			)
		);
		EmailActions::send(
			$email,
			'Unsubscribe', 
			'Here\'s the unsubscribe code you requested.',
			$body
		);
	}
	
	public static function unsubscribe($code, AccessRequest $ID){
		$str = base64_decode($code);
		$str = strrev($str);
		$email = base64_decode($str);
		
		return parent::MySQLCreateAction('INSERT IGNORE unsubscribe_requests SET created_datetime=NOW(), email=:email, ip=:ip', array(':email'=>$email,':ip'=>$ID->getIp()));
	}
}