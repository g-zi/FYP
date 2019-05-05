<?php
/*
	UserCake Version: 1.4
	modified g.zi@gmx.de
	http://usercake.com
	Developed by: Adam Davis
*/

class loggedInUser {

	public $email = NULL;
	public $hash_pw = NULL;
	public $user_id = NULL;
	public $clean_username = NULL;
	public $display_username = NULL;
	
	//Simple function to update the last sign in of a user
	public function updateLastSignIn()
	{
		global $db ;
		
		$sql = "UPDATE Users
			    SET
				LastSignIn = '".time()."'
				WHERE
				User_ID = '".$db->sql_escape($this->user_id)."'";
		
		return ($db->sql_query($sql));
	}
	
	//Return the timestamp when the user registered
	public function signupTimeStamp()
	{
		global $db ;
		
		$sql = "SELECT
				SignUpDate
				FROM
				Users
				WHERE
				User_ID = '".$db->sql_escape($this->user_id)."'";
		
		$result = $db->sql_query($sql);
		
		$row = $db->sql_fetchrow($result);
		
		return ($row['SignUpDate']);
	}
	
	//Update a users password
	public function updatePassword($pass)
	{
		global $db ;
		
		$secure_pass = generateHash($pass);
		
		$this->hash_pw = $secure_pass;
		
		$sql = "UPDATE Users
		       SET
			   Password = '".$db->sql_escape($secure_pass)."' 
			   WHERE
			   User_ID = '".$db->sql_escape($this->user_id)."'";
	
		return ($db->sql_query($sql));
	}
	
	//Update a users email
	public function updateEmail($email)
	{
		global $db ;
		
		$this->email = $email;
		
		$sql = "UPDATE Users
				SET Email = '".$email."'
				WHERE
				User_ID = '".$db->sql_escape($this->user_id)."'";
		
		return ($db->sql_query($sql));
	}
	
	//Update a users name
	public function updateUsername($username)
	{
		global $db ;
		
		$this->display_username = $username;
		
		$sql = "UPDATE Users
				SET Username = '".$username."'
				WHERE
				User_ID = '".$db->sql_escape($this->user_id)."'";
		
		return ($db->sql_query($sql));
	}
	
	//Update a users login name
	public function updateUsername_Clean($login_username)
	{
		global $db ;
		
		$this->clean_username = $login_username;
		
		$sql = "UPDATE Users
				SET Username_Clean = '".$login_username."'
				WHERE
				User_ID = '".$db->sql_escape($this->user_id)."'";
		
		return ($db->sql_query($sql));
	}
	

	//Fetch all user group information
	public function groupID()
	{
		global $db ;
		
		$sql = "SELECT Users.Group_ID, 
			   Groups.* 
			   FROM Users
			   INNER JOIN Groups ON Users.Group_ID = Groups.Group_ID 
			   WHERE
			   User_ID  = '".$db->sql_escape($this->user_id)."'";
		
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		return($row);
	}
	
	//Is a user member of a group
	public function isGroupMember($id)
	{
		global $db ;
	
		$sql = "SELECT Users.Group_ID, 
				Groups.* FROM Users 
				INNER JOIN Groups ON Users.Group_ID = Groups.Group_ID
				WHERE User_ID  = '".$db->sql_escape($this->user_id)."'
				AND
				Users.Group_ID = '".$db->sql_escape($db->sql_escape($id))."'
				LIMIT 1
				";
		
		if(returns_result($sql))
			return true;
		else
			return false;
		
	}
	
	//Logout
	function userLogOut()
	{
		destorySession("userCakeUser");
	}

}
?>