<?php 

// part of FREMO Yellow Pages @ g-zi.de/FYP

//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);

	header ('Content-type: text/html; charset=utf8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");

//====================================================================================================

	if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $loggedInUser->email) and isUserLoggedIn())
	{ header("Location: user/update-email-address.php"); die(); }

	if(@$_REQUEST['action']=="logout")
	{
		if(isUserLoggedIn()) $loggedInUser->userLogOut();
		header("Location: Main.php");  
	}

	@$_SESSION['bck'] = array('Main.php');

/*/ test for db abstraction
$DeinBahnhof = $db->sql_fetchrow($db->sql_query("SELECT Bhf_ID FROM manage WHERE User = '$loggedInUser->display_username'"));
$row = $db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$DeinBahnhof[Bhf_ID]'"));
$Bhf_ID = $row['id'];
echo "X".$Bhf_ID;

$DeinBahnhof = mysql_fetch_array(mysql_query("SELECT Bhf_ID FROM manage WHERE User = '$loggedInUser->display_username'"));
$row = mysql_fetch_array(mysql_query("SELECT * FROM bahnhof WHERE id = '$DeinBahnhof[Bhf_ID]'"));
$Bhf_ID = $row['id'];
echo " Y".$Bhf_ID;

echo sql_error();

$result = getGleisliste($Bhf_ID);
while( $row=$db->sql_fetchrow($result) )
{
  echo $row['Gleisname']."\n";
}


	$result = $db->sql_query("SHOW TABLES");
  while($row = $db->sql_fetchrow($result))
  {
    $table = $row['Tables_in_'.$db_name];
    echo $table, '<br>';

    $result1 = $db->sql_query("SELECT * from ".$table);
    while($row1 = $db->sql_fetchrow($result1))
    {
      //echo count($row1);
      //echo var_dump($row1);
    }
  }


$table = "treffen";

	$result = $db->sql_query("SELECT * from ".$table);
  $row = $db->sql_fetchrow($result);
	echo "ROWS=".$db->sql_affectedrows(), '<br>';
  echo "COLS=".count($row), '<br>'; // reihen

	$result = $db->sql_query("SELECT COUNT(*) FROM ".$table);
  $row = $db->sql_fetchrow($result);
  echo $row['COUNT(*)'], '<br>'; // reihen
  
  $cnt = $db->sql_fetchrow($db->sql_query("SELECT COUNT(*) FROM ".$table));
  echo $cnt['COUNT(*)'];

//-----------------------------------------------------------

$table = "Groups";
  $result = $db->sql_query("SHOW COLUMNS from ".$table);
  while($row = $db->sql_fetchrow($result))
  {
//    echo var_dump($row);
    echo $row['Field'], ' - ';
		echo $row['Type'], ' = ';
    echo strpos($row['Type'],'int'), '<br>';
					if(strpos($row['Type'],'int') > 0)
						echo "XXXXX";
  }

 	$result=$db->sql_query("SELECT * from ".$table);
	while ($meta = mysqli_fetch_field($result))
	{
		echo $meta->name." - ";
		echo $meta->type, '<br>';
	}


      $result = $db->sql_query("SHOW TABLES");
      while($row = $db->sql_fetchrow($result))
      {
        $table = $row['Tables_in_'.$db_name];
        echo $table, '<br>';
      }


//-----------------------------------------------------------


			$field_type = array();
			$field_name = array();

$table = "Groups";

      $result = $db->sql_query("SELECT * from ".$table);
      while ($meta = mysqli_fetch_field($result))
      {
//				$meta = mysql_fetch_field($result, $i);
//        array_push($field_type, $meta->type);
//				$i++;
//        echo var_dump($meta);
//          echo $meta->type ;
//          echo $meta->name, '<br>';
          array_push($field_type, $meta->type);
          array_push($field_name, $meta->name);
      }    

    echo $field_type[0];
    echo $field_name[0];

$num_fields = 2;

		$result = $db->sql_query("SELECT * from ".$table);

		while($row = $db->sql_fetchrow($result))
		{
			for( $i=0; $i < $num_fields; $i++)
			{
				echo $row[$field_name[$i]], '<br>';
			}
		}

		echo '<br> Affected Rows = ', $db->sql_affectedrows(), '<br>';

*/// test for db abstraction


?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
<title>Yellow Pages</title>
</head>
<body bgcolor=lightyellow>

<table align='center' width='100%' height='100%' border='0' cellpadding='0' cellspacing='10' >
  <tr>
    <td align='center' width='35%' height='40%' bgcolor='yellow' onClick=self.location.href='FYP.php?Bhf_ID=<?php echo $Bhf_ID?>'><img width='128' src='img/Palet_R.png'><br>
		<div style='font-size:100%; color:brown; font-family:Verdana'>
	    	<?php echo $TEXT['lang-head']?>
		</div>
	</td>
    <td align='center' width='35%' bgcolor='lightblue' onClick=self.location.href='Treffen.php?Bhf_ID=<?php echo $Bhf_ID?>'><img src='img/Meeting.png'><br>
		<div style='font-size:100%; color:blue; font-family:Verdana'>
    		<?php echo $TEXT['lang-meet']?>
    	</div>
    </td>
    <td align='center' bgcolor='lightgrey' onClick=self.location.href='Frachtzettel.php'><img width='128' src='img/Cargo_Y.png'>
    <div style='font-size:100%; color:black; font-family:Verdana'><?php echo $TEXT['lang-waybills']?></div>			
    </td>
  </tr>
  <tr>
    <td align='right'>
	<b><font size='5'><?php echo $TEXT['lang-head']?></font></b><br>
		<?php echo $TEXT['lang-subhead']?><br>
		<font size='4'><?php if(isUserLoggedIn()) echo $TEXT['lang-welcome'] .'<b>'.$loggedInUser->display_username;?></b></font>
    </td>
<?php	if(!isUserLoggedIn()) 
        {   echo"<td align='center' valign='bottom'><img src='img/Lock.png' onClick=self.location.href='user/login.php?llang=".strtolower($lang)."'><br>
        	<div style='font-size:90%; color:gray; font-family:Verdana'>Login</div>";}
		else
		{/*	BENUTZERKONTO
			echo"<td align='center' valign='middle'><img height='90px' src='img/tool.png' onClick=self.location.href='user/account.php?llang=".strtolower($lang)."'><br>            
			<div style='font-size:80%; color:gray; font-family:Verdana'>".$TEXT['lang-account']."</div>";  
		  */	
			echo"<td align='center' valign='middle'><img src='user/layout/FREMO-Logo.gif' onClick=self.location.href='http://www.fremo-net.eu'>";
        }?>

    </td>
	<td>	
		<table align='right' width='100%'>
			<td align='left' valign='middle'>
			    	<?php selectLanguage($languages[$lang],$translate)?>
					<?php if($lang=="SV") echo "<a href='http://g-zi.de/FYP/Lathund_Yellow_Pages.pdf' target='_blank'>Lathund Yellow Pages</a><br>" ?>
					<a href='Doku/FYPages_PPT.pdf' target='_blank'>Presentation Rheine 2019 (27MB)</a><br>
					<a href='Doku/FYPages.pdf' target='_blank'>Manual</a>&nbsp&nbsp&nbsp
					Mail to: <a href='mailto:g.zi@gmx.de?subject=YellowPages&body=YellowPages,'>g.zi@gmx.de</a><br>
					<a href='http://www.disclaimer.de/disclaimer.htm#2' target='_blank'>Haftungsausschluss / Disclaimer</a>
			</td>
			<td align='right' valign='middle'>
		<?php  	if(isUserLoggedIn()) echo"<img src='img/Logout.png' height='64px' onClick=self.location.href='Main.php?action=logout'><br>
				<div style='font-size:90%; color:gray; font-family:Verdana'><p>Logout&nbsp</p></div>"?>
			</td>
		</table>
	</td>
  </tr>
  <tr>
    <td align='center' bgcolor='lightgreen' onClick=self.location.href='Bahnhof.php?Bhf_ID=<?php echo $Bhf_ID?>&Spur='><img src='img/Tools.png'><br>
		<div style='font-size:100%; color:blue; font-family:Verdana'>
		    <?php echo $TEXT['lang-station']?>
		</div>
	</td>

<?php	// if(isUserLoggedIn() and $_SESSION["sso"]==0) { ?>
<?php	if(isUserLoggedIn()) { ?>
<?php /* MODULE
    	<td align='center' bgcolor='#F0E0B0' onClick=self.location.href='Module.php'><img width='128' src='img/puzzle.png'>
    	<div style='font-size:100%; color:black; font-family:Verdana'><?php echo $TEXT['lang-module']?></div>			
*/?>
    	<td align='center' bgcolor='#F0E0B0' onClick=self.location.href='user/account.php?llang=<?php echo strtolower($lang)?>'><img width='128' src='img/tool.png'>
    	<div style='font-size:100%; color:brown; font-family:Verdana'><?php echo $TEXT['lang-account']?></div>			
<?php 	}
		else
		{
			echo "<td align='center' onClick=self.location.href='http://www.fremo-net.eu'><img src='user/layout/FREMO-Logo.gif'><br>";
		}
?>
    </td>

    <td align='center' height='40%' bgcolor='pink' onClick=self.location.href='Manage.php?Bhf_ID=<?php echo $Bhf_ID?>'><img src='img/Manage.png'><br>
		<div style='font-size:100%; color:darkmagenta; font-family:Verdana'>
		    <?php echo $TEXT['lang-bvw']?>
		</div>
	</td>
  </tr>

<noscript>
	<br><font size='5' color='red'><b>Javascript is disabled,<br> please activate Javascript</b></font>
</noscript>

</table>

</body>
</html>
