<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

require_once("user/models/config.php");
  
class Autologin
{
    public function GetLoggedInExistingOrNewUserFromSsoData($username, $email)
    {
        if(!usernameExists($username)) {
            Autologin::createUser($username, $email);
        }

        $userdetails = fetchUserDetails($username);
				$loggedInUser = new loggedInUser();
				$loggedInUser->email = $userdetails["Email"];
				$loggedInUser->user_id = $userdetails["User_ID"];
				$loggedInUser->hash_pw = $userdetails["Password"];
				$loggedInUser->display_username = $userdetails["Username"];
        $loggedInUser->clean_username = $userdetails["Username_Clean"];
        $loggedInUser->updateLastSignIn();

        $_SESSION["userCakeUser"] = $loggedInUser;

        // Geaenderte e-mailadresse speichern
        if ($loggedInUser->email != $email && isValidEmail($email)) {
            $loggedInUser->updateEmail($email);
        }

        return $loggedInUser;
    }

    private function createUser($username, $email)
    {
        $user = new User($username, 'Do not need password with Fremo SSO ;-)', $email);

        global $emailActivation;
        $emailActivation = false;

        if (!$user->status) {
            echo 'Error creating a user!';
            echo $user->username_taken ? "Username taken" : "";
            echo $user->email_taken ? "email taken" : "";
            die();
        }

        if (!$user->userCakeAddUser()) {
            echo 'Error saving new user!';
            die();
        }
    }
}
