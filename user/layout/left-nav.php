<?php
//	if(isset($_REQUEST['langu'])) {$langu = strtolower(rawurlencode(getVariableFromQueryStringOrSession('langu')));}
 	$llang = strtolower(rawurlencode(getVariableFromQueryStringOrSession('llang')));
	if($llang == "") $llang = "en";
	require_once("models/lang/".$llang.".php"); 
	$_SESSION["lang"] = strtoupper($llang);
?>
<body bgcolor=lightyellow>
	<?php if(!isUserLoggedIn()) { ?>
		<ul>
			<li><a href="../Main.php"><?php echo $lang["HOME"]?></a></li>
			<li><a href="login.php"><?php echo $lang["LOGIN"]?></a></li>
			<li><a href="register.php"><?php echo $lang["REGISTER"]?></a></li>
			<li><a href="forgot-password.php"><?php echo $lang["FORGOT_PASSWORD"]?></a></li>
			<li><a href="resend-activation.php"><?php echo $lang["RESEND_ACTIVATION_EMAIL"]?></a></li>
	<?php } else { ?>
		<ul>
<?php //	<li><a href="../Manage.php?back=Main.php"><?php echo $lang["HOME"]?></a></li>
            <li><a href="../Main.php"><?php echo $lang["HOME"]?></a></li>
			<li><a href="change-password.php"><?php echo $lang["CHANGE_PASSWORD"]?></a></li>
			<li><a href="update-email-address.php"><?php echo $lang["UPDATE_EMAIL_ADDRESS"]?></a></li>
			<li><a href="change-name.php"><?php echo $lang["CHANGE_NAME"]?></a></li>
			<li><a href="change-login-name.php"><?php echo $lang["CHANGE_LOGIN_NAME"]?></a></li>
			<li><a href="../Main.php?action=logout"><?php echo $lang["LOGOUT"]?></a></li>
	<?php } ?>
	<br>
	<form name='frmLang' method='get'>
		<select style='font-size:12' name='llang' onchange='submit()'>
			<option value='' >Select Language</option>
			<option value='de' >--------------</option>
			<option value='bg' >Bulgarian</option>
			<option value='cs' >Czech</option>
			<option value='da' >Dansk</option>
			<option value='de' >Deutsch</option>
			<option value='en' >English</option>
			<option value='es' >Espanol</option>
			<option value='et' >Estonian</option>
			<option value='fr' >Francais</option>
			<option value='el' >Greek</option>
			<option value='hu' >Hungarian</option>
			<option value='it' >Italiano</option>
			<option value='lt' >Latvian</option>
			<option value='lv' >Lithuanian</option>
			<option value='nl' >Nederlands</option>
			<option value='no' >Norsk</option>
			<option value='pl' >Polski</option>
			<option value='pt' >Portugues</option>
			<option value='ro' >Rumanian</option>
			<option value='ru' >Russian</option>
			<option value='sv' >Svenska</option>
			<option value='sk' >Slovak</option>
			<option value='sl' >Slovene</option>
			<option value='fi' >Suomeksi</option>
		</select>
	</form>
	<br><br>
	<div id="build">
		<a href="http://www.fremo-net.eu"><span>FREMO</span></a>
	</div>
	<br>
</ul>
