<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserAccount extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'first_name'=>DataType::TEXT,
			'middle_name'=>DataType::TEXT,
			'last_name'=>DataType::TEXT,
			'alternative_name'=>DataType::TEXT,
			'account_status_id'=>DataType::NUMBER,
			'AccountStatus'=>DataType::OBJECT,
			'thumbnail_id'=>DataType::NUMBER,
			'avatar_path'=>DataType::TEXT,
			'last_online'=>DataType::DATETIME,
			'latitude'=>DataType::NUMBER,
			'longitude'=>DataType::NUMBER,
			'gmt_offset'=>DataType::NUMBER,
			'is_email_validated'=>DataType::BOOLEAN,
			'is_online'=>DataType::BOOLEAN,
			'is_closed'=>DataType::BOOLEAN,
			'pass_code'=>DataType::SECRET,
			'unread_messages'=>DataType::NUMBER,
				
			'AppPermissionCollection'=>DataType::COLLECTION, // all permissions this user allows the current 3rd party application to have
			'AvatarPartCollection'=>DataType::COLLECTION, // Parts and pieces of the avatar builder
			'UserAchievementCollection'=>DataType::COLLECTION, // unlocked achievements for doing stuff on a site
			'UserHistoryCollection'=>DataType::COLLECTION, // where you've been on the site
			'UserLoginHistoryCollection'=>DataType::COLLECTION, // login attempts, good or bad, contained here
			'UnreadMessageCollection'=>DataType::COLLECTION, // all unread messages
			'UserInviteCollection'=>DataType::COLLECTION, // all invitation codes available
			'UserRoleCollection'=>DataType::COLLECTION, // all roles applied to this member
			'UserSettingCollection'=>DataType::COLLECTION, // all user specified settings
			'UserPowerCollection'=>DataType::COLLECTION, // all permissions allowed this user
			'FriendshipCollection'=>DataType::COLLECTION, // all friends
			'BlockCollection'=>DataType::COLLECTION // all blocked users
		),true);
		parent::__construct($data);
	}
	
	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserAccount)?$DataObject:new UserAccount($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	public function getProfile(){
		return ($this->getObject('Profile') instanceof UserProfile)
			?$this->_data['Profile']
			:new UserProfile();
	}
		
	public function getTheme(){
		return ($this->getObject('Theme') instanceof Theme)
			?$this->_data['Theme']
			:new Theme();
	}
	
	public function getLocation(){
		return ($this->getObject('Location') instanceof Location)
			?$this->_data['Location']
			:new Location();
	}
	
	public function getNetworkAddress(){
		return ($this->getObject('NetworkAddress') instanceof NetworkAddress)
			?$this->_data['NetworkAddress']
			:new NetworkAddress();
	}	
	
	public function getAccountStatus(){
		return ($this->getObject('AccountStatus') instanceof AccountStatus)
			?$this->_data['AccountStatus']
			:new AccountStatus();
	}
	
	private $_thumbnail;
	public function getThumbnail(){
		if(isset($this->_thumbnail)){
			return $this->_thumbnail;
		} else {
			if( isset($this->_data['avatar_path']) && !empty($this->_data['avatar_path']) ){
				$this->_thumbnail = RuntimeInfo::instance()->getS3URL().$this->_data['avatar_path'];
				return $this->_thumbnail;
			} else {
				return '/img/avatar_builder/Male/default.png';
			}
		}
	}
	
	public function getUserAchievementCollection(){
		if(!$this->getCollection('UserAchievementCollection') instanceof UserAchievementCollection){
			$this->set('UserAchievementCollection', UserAchievementActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserAchievementCollection') instanceof UserAchievementCollection)
			?$this->_data['UserAchievementCollection']
			:new UserAchievementCollection();
	}
	
	public function getUserHistoryCollection(){
		if(!$this->getCollection('UserHistoryCollection') instanceof UserHistoryCollection){
			$this->set('UserHistoryCollection', UserHistoryActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserHistoryCollection') instanceof UserHistoryCollection)
			?$this->_data['UserHistoryCollection']
			:new UserHistoryCollection();
	}
	
	public function getUserLoginHistoryCollection(){
		if(!$this->getCollection('UserLoginHistoryCollection') instanceof UserLoginHistoryCollection){
			$this->set('UserLoginHistoryCollection', UserLoginHistoryActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserLoginHistoryCollection') instanceof UserLoginHistoryCollection)
			?$this->_data['UserLoginHistoryCollection']
			:new UserLoginHistoryCollection();
	}
	
	public function getBlockCollection(){
		if(!$this->getCollection('BlockCollection') instanceof BlockCollection){
			$this->set('BlockCollection', BlockActions::selectUsersBlockListByTheirUserId($this->_data['user_id']));
		}
		return ($this->getCollection('BlockCollection') instanceof BlockCollection)
			?$this->_data['BlockCollection']
			:new BlockCollection();
	}
	
	public function getUnreadMessageCollection(){
		if(!$this->getCollection('UnreadMessageCollection') instanceof MessageCollection){
			$this->set('UnreadMessageCollection', MessageActions::selectMyUnreadMessageCollection());
		}
		return ($this->getCollection('UnreadMessageCollection') instanceof MessageCollection)
			?$this->getCollection('UnreadMessageCollection')
			:new MessageCollection();
	}
	
	public function getUserInviteCollection(){
		if(!$this->getCollection('UserInviteCollection') instanceof MessageCollection){
			$this->set('UserInviteCollection', UserInviteActions::selectByUserInviteId($this->_data['user_id']));
		}
		return ($this->getCollection('UserInviteCollection') instanceof UserInviteCollection)
			?$this->_data['UserInviteCollection']
			:new UserInviteCollection();
	}
	
	public function getUserRoleCollection(){
		if(!$this->getCollection('UserRoleCollection') instanceof UserRoleCollection){
			$this->set('UserRoleCollection', UserRoleActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserRoleCollection') instanceof UserRoleCollection)
			?$this->_data['UserRoleCollection']
			:new UserRoleCollection();
	}
	
	public function getUserSettingCollection(){
		if(!$this->getCollection('UserSettingCollection') instanceof UserSettingCollection){
			$this->set('UserSettingCollection', UserSettingActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserSettingCollection') instanceof UserSettingCollection)
			?$this->_data['UserSettingCollection']
			:new UserSettingCollection();
	}
	
	public function getUserPowerCollection(){
		if(!$this->getCollection('UserPowerCollection') instanceof UserPowerCollection){
			$this->set('UserPowerCollection', UserPowerActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('UserPowerCollection') instanceof UserPowerCollection)
			?$this->_data['UserPowerCollection']
			:new UserPowerCollection();
	}
//	
//	public function getAppPermissionCollection(){
//		if(isset($this->_data['AchievementCollection'])){
//			UserAchievementActions::selectByUserId($user_id);
//		}
//		return (isset($this->_data['AppPermissionCollection']) && $this->_data['AppPermissionCollection'] instanceof AppPermissionCollection)
//			?$this->_data['AppPermissionCollection']
//			:new AppPermissionCollection();
//	}
	
	public function getAvatarPartCollection(){
		if(!$this->getCollection('AvatarPartCollection') instanceof AvatarPartCollection){
			$this->set('AvatarPartCollection', UserAvatarActions::selectByUserId($this->_data['user_id']));
		}
		return ($this->getCollection('AvatarPartCollection') instanceof AvatarPartCollection)
			?$this->_data['AvatarPartCollection']
			:new AvatarPartCollection();
	}
	
}

class UserAccountCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserAccount');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserAccountByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserAccount)?$return:new UserAccount($return->toArray());
	}
	
	public function getUserAccountByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserAccount)?$return:new UserAccount($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}