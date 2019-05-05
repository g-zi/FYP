<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

//  error_reporting(E_ALL);
//  ini_set("display_errors", 1);

	header ('Content-type: text/html; charset=utf-8');
	require_once("user/models/config.php");
	require_once("lib/shared.inc.php");
	require_once("lib/menu.inc.php");
	require_once("SQL/backup.php");

	//Prevent the user visiting the logged in page if he/she is not logged in
	if(!isUserLoggedIn()) { header("Location: user/login.php"); die(); }

//================================================================================

	$Treffen = getVariableFromQueryStringOrSession('Treffen');

	if(isUserLoggedIn())
	{
		if($Treffen!="")
		{
			if(isset($_POST['alle']))
			{
				foreach($_POST['alle'] as $key => $alleID)
				{
					$result=("SELECT * FROM treffen
					WHERE Bhf_ID = '".escape($alleID)."' and Treffen = '".escape($Treffen)."'");
					if($db->sql_fetchrow($result)=="")
					{
						$alleID = escape($alleID);
						$Bhf=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = ".$alleID.";"));
						$db->sql_query("INSERT INTO treffen (Treffen,Betriebsstelle,Bhf_ID,Trf_Bem)
						VALUES('$Treffen','$Bhf[Haltestelle]','$alleID','[$loggedInUser->user_id]');");
echo sql_error();
					}
				}
			}
		}

		if(@$_REQUEST['action']=="edit")
		{
			$row=$db->sql_fetchrow($result=$db->sql_query("SELECT treffen.*, bahnhof.Spur FROM treffen
			LEFT JOIN bahnhof ON bahnhof.id = treffen.Bhf_ID WHERE treffen.id=".round($_REQUEST['Treffen_ID'])));
echo sql_error();
			$_SESSION['Treffen'] = $row['Treffen'];
			$_SESSION['Telefon'] = $row['Telefon'];
			$_SESSION['Betriebsstelle'] = $row['Betriebsstelle'];
			$_SESSION['Bhf_ID'] = $row['Bhf_ID'];
			$_SESSION['Anschliesser'] = $row['Anschliesser'];
			$_SESSION['An_ID'] = $row['An_ID'];
			$_SESSION['Trf_Bem'] = trim(substr($row['Trf_Bem'],strpos($row['Trf_Bem'],']')+1));
			$_SESSION['Spur'] = $row['Spur'];
			$Spur = $row['Spur'];
		}

		if(@$_REQUEST['action']=="delete")
		{
			$db->sql_query("DELETE FROM treffen WHERE id=".round($_REQUEST['Treffen_ID']));
		}

		$ShowTreffen = getVariableFromQueryStringOrSession('ShowTreffen');

		$Treffen_ID = intval(getVariableFromQueryStringOrSession('Treffen_ID'));
		$Telefon = intval(getVariableFromQueryStringOrSession('Telefon'));
		$Betriebsstelle = getVariableFromQueryStringOrSession('Betriebsstelle');
		$KurzBZ = getVariableFromQueryStringOrSession('KurzBZ');
		$Bhf_ID = intval(getVariableFromQueryStringOrSession('Bhf_ID'));
		$Anschliesser = getVariableFromQueryStringOrSession('Anschliesser');
		$An_ID = intval(getVariableFromQueryStringOrSession('An_ID'));
		$Trf_Bem = getVariableFromQueryStringOrSession('Trf_Bem');
		$Bv = getVariableFromQueryStringOrSession('Bv');
// $Anschliesser = preg_replace("/[]/", "", $_SESSION['Anschliesser']);

		$Spur1 = getVariableFromQueryStringOrSession('Spur1');
		if ($Spur1=="") $Spur1 = $Spur;

		if($Bhf_ID=='0')
		{
			if($Spur=="") $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof
				WHERE Haltestelle LIKE '%".escape($Betriebsstelle)."%'"));
			else $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof
				WHERE Haltestelle LIKE '%".escape($Betriebsstelle)."%' and Spur = '$Spur'"));
			if($row['Haltestelle']!="") $Betriebsstelle = $row['Haltestelle'];
echo sql_error();
			$Bhf_ID=$row['id'];
		}

		if(isset($_REQUEST['Anschliesser']) and $Anschliesser=="" 
		or (isset($_REQUEST['An_ID']) and $_REQUEST['An_ID']=="")
		or (isset($_REQUEST['Bhf_ID']) and $_REQUEST['Bhf_ID'] != ""))
		{
			$An_ID = 0;
			unset($_SESSION['An_ID']);
			unset($_SESSION['Anschliesser']);
			unset($Anschliesser);
		}
		elseif($An_ID > 0 and !isset($_REQUEST['Anschliesser']))
		{
			$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE id = '$An_ID'"));
			$Anschliesser = $row['Haltestelle']." [".$row['Spur']."]";
		}
		else
		{
/*			$row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof WHERE Haltestelle LIKE '%".escape(str_replace(' ['.$Spur.']','',$Anschliesser))."%'"));
			$Anschliesser = $row['Haltestelle']." [".$row['Spur']."]";
			$_SESSION['An_ID'] = $row['id'];
			$An_ID = $row['id'];
*/		}


		if(isset($_REQUEST['KurzBZ']))
		{
			if($Sp=="") $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof
				WHERE Kurzbezeichnung = '$KurzBZ'"));
			else $row=$db->sql_fetchrow($db->sql_query("SELECT * FROM bahnhof
				WHERE Kurzbezeichnung '$KurzBZ' and Spur = '$Sp'"));
			
			$_SESSION['Bhf_ID'] = $row['id'];
			$Bhf_ID=$row['id'];
			$_SESSION['Kbz'] = $row['Kurzbezeichnung'];
			$Kbz = $row['Kurzbezeichnung'];
			$_SESSION['Spur'] = $row['Spur'];
			$Spur = $row['Spur'];
			$_SESSION['Betriebsstelle'] = $row['Betriebsstelle'];
			$Betriebsstelle = $row['Haltestelle'];
		}
	}

	if($_SESSION['Treffen']=="") // if nothing selected the last edited meeting will be selected.
	{
		$row=$db->sql_fetchrow($db->sql_query("SELECT Treffen, Timestamp FROM treffen ORDER BY timestamp DESC LIMIT 1"));
		$_SESSION['Treffen']=$row['Treffen'];
		$Treffen=$row['Treffen'];
	}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Yellow Pages</title>
</head>

<noscript>
	<br><font size='5' color='red'><b>Javascript is disabled,<br> please activate Javascript</b></font>
</noscript>

<script src="lib/inlinepopup/inlinepopup.js" type="text/javascript"></script>

<body bgcolor='#F5F5FF'>

<?php if(isUserLoggedIn()){?>
    <div style='position:absolute; top:0px; left:420; width:150px; text-align:center; font-size:80%; color:darkgreen; font-family:Verdana'>
        <img src="img/xls.png" height='64px' onClick="self.location.href='Treffen.php?Betriebsstelle=<?php echo $Betriebsstelle?>
        &action=getxls&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>'"><br>
        <?php echo $TEXT['lang-xls']?>
    </div>
    <div style='position:absolute; top:0px; left:522; width:250px; text-align:center; font-size:80%; color:darkred; font-family:Verdana'>
		<img src="img/Adobe_PDF.png" height='64px'
		onClick="self.location.href='Treffen.php?Betriebsstelle=<?php echo $Betriebsstelle?>
		&action=getpdf&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>'"><br>
        <?php echo $TEXT['lang-pdf']?>
    </div>
    <div style='position:absolute; top:82px; left:570; width:250px;' >
        <form name='frmSort' method='get' align=right;>
            <select style='font-size:12' name='sort' onchange='submit()'>
            <option value='' >Sort = <?php echo $sorting[$sort]?></option>
            <option value='prd' ><?php echo $TEXT['lang-product']?></option>
            <option value='int' ><?php echo $TEXT['lang-int']?></option>
            <option value='an' ><?php echo $TEXT['lang-an']?></option>
            <option value='rem' ><?php echo $TEXT['lang-ls']?> / <?php echo $TEXT['lang-rem']?></option>
            <option value='station' ><?php echo $TEXT['lang-station']?></option>
            </select>
        </form>
    </div>
<?php } ?>

            <div style='position:absolute; top:60px; left:720; width:150px; text-align:center; font-size:80%; color:darkblue; font-family:Verdana'>
                <img src="img/Phone.png" height='60px' onClick="self.location.href='Treffen.php?action=gettel'"><br>
				<?php echo $TEXT['lang-tellist']?>
			</div>
		<?php if(isUserLoggedIn()){?>
            <div style='position:absolute; top:60px; left:870; width:150px; text-align:center; font-size:80%; color:gray; font-family:Verdana'>
				<img src="img/Calendar.png" height='60px'
					onClick="self.location.href='Treffen.php?Betriebsstelle=<?php echo $Betriebsstelle?>
					&action=xpln&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>'"><br>
					<?php echo $TEXT['lang-xpln']?>
            </div>
		<?php } ?>


<?php /*
<div style='position:absolute; top:5px; left:450px; width:32px; text-align:center; font-size:80%; color:darkred; font-family:Verdana'>
	<img src='img/translate.jpg' height='32px'
	onClick="self.location.href='Treffen.php?action=getpdf&Betriebsstelle=<?php echo $Betriebsstelle?>&Spur=<?php echo $Spur?>&Bhf_ID=<?php echo $Bhf_ID?>
	&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>&translate=<?php echo strtolower($lang)?>'"><br>
	<?php echo $TEXT['lang-translate']?>
</div>
*/?>

<?php selectBack('970',$lang,'Main.php')?>

<div style='position:absolute; top:10px; left:160'>
	<?php selectLanguage($languages[$lang])?>
</div>


<table style='table-layout:fixed' width=<?php if(isUserLoggedIn()) echo"1020px"; else echo"910px"?> border=0 cellpadding=0 cellspacing=0 >
<colgroup>
	<col width='260px'>
	<col width='30px'>
	<col width='230px'>
	<col width='10px'>
</colgroup>
	<tr>
		<b><font size='5'><?php echo $TEXT['lang-head']?></font></b>
		<td colspan=<?php if(isUserLoggedIn()) echo"4"; else echo"2"?> align="right" valign="top"></td>
	</tr>
	<tr>
		<td colspan='2' ><?php echo $TEXT['lang-subhead']?></td>
        <td rowspan='4' align='middle' valign='bottom' ></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td colspan='2' >
			<font size='4' >
<?php			if(isUserLoggedIn()) echo $TEXT['lang-welcome'].'<b>'.$loggedInUser->display_username.'</b>';
				else echo '<br>';
?>			</font>
		</td>
	</tr>
<br>
	<tr>
	   	<form>
		   	<td valign=bottom>
				<input type=hidden name=Betriebsstelle value='<?php echo $Betriebsstelle?>'>
				<input type=hidden name=Spur value='<?php echo $Spur?>'>
				<input type=hidden name=Bhf_ID value='<?php echo $Bhf_ID?>'>
				<input type=hidden name=Anschliesser value='<?php echo $Anschliesser?>'>
				<input type=hidden name=Trf_Bem value='<?php echo $Trf_Bem?>'>

<?php				echo"<select dir='rtl' style='background-color:#F5F5FF;width:260px;height:26px;border:1px solid;
						border-color:lightblue;font-size:18px;font-weight:bold' name=Treffen onchange='submit()'>";
				echo"<option>".$TEXT['lang-meet']."</option>";
				$result=$db->sql_query("SELECT distinct Treffen FROM treffen ORDER BY Treffen");
				$i=0;
				while( $row=$db->sql_fetchrow($result) )
				{
					$row_Treffen = $row['Treffen'];
					echo"<option value='$row_Treffen'>".$row_Treffen."</option><b>";
					$i++;
				}
				echo"</select>";
?>			</td>
		</form>
		<form>
			<td colspan='2' valign='bottom' height='50px' >
				<input type='text' maxlength='50' style='width:260px;height:26px;font-size:12pt;font-weight:bold;background-color:white' name=Treffen value='<?php echo $Treffen?>'>
			</td>
			<td valign='bottom' >
				<input type='image' src='img/key_enter.png'>
			</td>
		</form>
	</tr>
</table>






<?php

if($ShowTreffen=='Module')
{
?><div style='position:absolute; top:92px; left:10px; width:800px; text-align:center; font-size:200%; color:red; font-family:Verdana'>
	<td>Under Construction</td>
</div><?php
	include("lib/modul.list.php");
}
else
{
	if(@$_REQUEST['Betriebsstelle']!="")
	{
		if($Treffen!="" and $Bhf_exists['Betriebsstelle']=="" and $An_exists['Anschliesser']=="" or $Treffen!="" and $An_exists['Anschliesser']=="" and $Anschliesser!="")
		{
			if(isset($_REQUEST['Change']))
			{
//echo $Betriebsstelle." ".$Spur." ".$Bhf_ID." T".$Treffen_ID;
				$An_exists=$db->sql_fetchrow($db->sql_query("SELECT Anschliesser, Betriebsstelle FROM treffen WHERE Anschliesser = '$Anschliesser' and Treffen = '$Treffen'"));

				if($An_exists['Anschliesser']!="" and $An_exists['Betriebsstelle']!=$_REQUEST['Betriebsstelle'])
				{
					echo "<div style='position:absolute; top:150px; left:10; color:blue; font-size:120%; font-family:Verdana'>"
					.$TEXT['lang-an']." <b>".$Anschliesser."</b>".$TEXT['lang-exists']."</div>";
				}
				else
				{
					$db->sql_query("UPDATE treffen SET Treffen='$Treffen', Telefon='$Telefon', Betriebsstelle='$Betriebsstelle',
					Bhf_ID='$Bhf_ID',Anschliesser='$Anschliesser',An_ID='$An_ID',Trf_Bem='[$loggedInUser->user_id] $Trf_Bem' WHERE id = ".$Treffen_ID);
//echo sql_error().$Bhf_ID;
				}
			}

			if(isset($_REQUEST['Add']))
			{
				$Bhf_exists=$db->sql_fetchrow($db->sql_query("SELECT Betriebsstelle FROM treffen WHERE Bhf_ID = '$Bhf_ID' and Treffen = '$Treffen'"));
				$An_exists=$db->sql_fetchrow($db->sql_query("SELECT Anschliesser FROM treffen WHERE Anschliesser = '$Anschliesser' and Treffen = '$Treffen'"));

				if($Bhf_exists['Betriebsstelle']!="" and $Anschliesser =="")
				{
					echo "<div style='position:absolute; top:145px; left:10; color:blue; font-size:100%; font-family:Verdana'>"
					.$TEXT['lang-station']." <b>".$Betriebsstelle."</b>".$TEXT['lang-exists']."</div>";
				}

				if($An_exists['Anschliesser']!="")
				{
					echo "<div style='position:absolute; top:145px; left:10; color:blue; font-size:100%; font-family:Verdana'>"
					.$TEXT['lang-an']." <b>".$Anschliesser."</b>".$TEXT['lang-exists']."</div>";
				}

				if(($Bhf_ID!="" and $Bhf_exists['Betriebsstelle']=="" and $Anschliesser=="") or ($Bhf_ID!="" and $An_exists['Anschliesser']=="" and $Anschliesser!=""))
				{
					$db->sql_query("INSERT INTO treffen (Treffen, Betriebsstelle, Bhf_ID, Anschliesser, An_ID, Trf_Bem)
					VALUES('$Treffen', '$Betriebsstelle', '$Bhf_ID', '$Anschliesser', '$An_ID', '[$loggedInUser->user_id] $Trf_Bem');");
					$_SESSION['Treffen_ID'] = $db->sql_nextid();
					$Treffen_ID = $db->sql_nextid();
echo sql_error();
				}
			}
		}

		if($An_ID==0 and $Anschliesser!="") //and $_SERVER['SCRIPT_NAME']=="/Treffen.php"
		{
			echo "<font size='4' style=color:red;font-family:Helvetica>".$TEXT['lang-an']." <b>".$Anschliesser."</b> ".$TEXT['lang-notbhf']."</font>";
?>			<input type=button style="background-color:lightgreen" value='<?php echo $TEXT['lang-station']." ".strtolower($TEXT['lang-add'])?> ?'
			onClick="self.location.href='Bahnhof.php?Betriebsstelle=<?php echo substr($Anschliesser,0,strpos($Anschliesser,"[")-1)?>&Spur=<?php echo $Spur?>&Bhf_ID='">
<?php	}
	}
?>

<table class='fixed' border='0' cellpadding='0' cellspacing='0' >
	<col width="12px" />
	<col width="15px" />
	<col width="5px" />
	<col width="12px" />
	<col width="12px" />
	<col width="12px" />
	<col width="220px" />
	<col width="100px" />
	<col width="12px" />
	<col width="12px" />
	<col width="15px" />
	<col width="220px" />
	<col width="200px" />
	<col width="35px" />
	<col width="35px" />

<tr>
	<td colspan='12' height='25px' valign='bottom'>
		<?php $DeinBahnhof=$db->sql_fetchrow($db->sql_query("SELECT Betriebsstelle FROM manage WHERE Bhf_ID = '$Bhf_ID' and MUser_ID = '$loggedInUser->user_id'"));
echo sql_error();
		if($DeinBahnhof!="") echo "<br>";
		else ErrorMessage($Bhf_ID, $Betriebsstelle, $Spur, $loggedInUser, $lang)?>
	</td>
</tr>

<tr valign='bottom' ><td bgcolor='lightblue' colspan='15' ><img src='img/blank.gif' width='1px' height='8px' ></td></tr>
<tr>
		<td width='5' ></td>
	<form id='input' >
		<td><input type='text' name='Telefon' maxlength='3' style='width:35px;height:28px;font-size:11pt;background-color:white;text-align:right' value=<?php echo $Telefon?>></td>
	</form>
	<form id='kbz' >
		<td width=70px align=right style='font-family:Verdana'>
			<?php if(sizeof($Bhf)>1) echo "<input style='width: 60px; height:28px; font-size:12pt; font-weight:bold'
				type=text maxlength=10 name=KurzBZ value='$Kbz'>";
			else echo "<input style='width: 60px; height:28px; font-size:12pt; font-weight:bold'
				type=text maxlength=10 name=KurzBZ value='$Kbz'>";?>
		</td>
		<td colspan='3'><input type='image' src='img/key_enter.png'></td>
	</form>
		<td>
			<?php if(sizeof($Bhf)>1) echo "<input form='input' style='width: 220px;height:28px;font-size:12pt;font-weight:bold;
				background-color:#D0D0FF' type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";
			else echo "<input form='input' style='width: 220px;height:28px;font-size:12pt;font-weight:bold;
				background-color:white' type=text maxlength=50 name=Betriebsstelle value='$Betriebsstelle'>";?>
		</td>
		<td style='font-family:Helvetica;font-size:11pt'>&nbsp;<?php echo $Spur?></td>
		<input form='input' type=hidden name=Bhf_ID value=''>
		<td colspan=4><input form='input' style='width:260px;height:28px;font-size:12pt' type=text maxlength=50 name=Anschliesser value='<?php echo $Anschliesser?>'></td>
		<td><input form='input' style='width:200px;height:28px;font-size:12pt' type=text size=30 maxlength=100 name=Trf_Bem value='<?php echo $Trf_Bem?>'></td>
			<input form='input' type=submit style='background-color:lightyellow; border:0px solid; border-color:orange; padding:0; font-size:1pt' value="">

<?php if(isUserLoggedIn() and !($An_ID==0 and $Anschliesser!="")) { ?>
		<td>
			<button form='input' type='submit' name='Change' style='width:35px' >
				<img height='18px' border='0' src='img/ok.png' alt=".$TEXT['lang-save']."><br>
			</button>
		</td>
		<td align='right' >
			<button form='input' type='submit' name='Add' style='width:35px' >
				<img height='18px' border='0' src='img/addblue.png' alt=".$TEXT['lang-add']."><br>
			</button>
		</td>
<?php } ?>
</tr>









<tr bgcolor=lightblue>
	<td><img src=img/blank.gif width=5px height=25px></td>
	<td class=tabhead align=right width=35px>
		<a href="Telefon.php?Treffen=<?php echo $Treffen?>"
			onclick="InlinePopup.open(this.href, '', 'statusbar=no,resizable=no,width=700,height=500,title=<?php echo $TEXT['lang-tel']."  ".$TEXT['lang-meet'].": ".$Treffen?>');return false;">
			<img style="background-color:lightblue;height:22px;border:0px" src="img/Telefon.png">
		</a>
	</td>
		<?php selectKbz($Sp, 'lightblue', 'blue', $lang, 'ltr', 'bold','' ,'')?>
		<td colspan='3' style='font-family:Arial Narrow, Arial; font-stretch: condensed; font-weight: bold;font-size:14px'><center><?php echo $TEXT['lang-status']?></center></td>
		<?php selectBetriebsstelle($Sp, 'lightblue', 'blue', $lang, 'ltr', 'bold','' ,'')?>
	<form name='selSpur' method='get'>
		<td class=tabhead>
			<select style='width:100px;font-size:16;background-color:lightblue;border:1px solid;border-color:blue;font-weight:bold' name='Sp' value=".$Spur." onchange='submit()'>
				<option value=<?php echo $Spur?>><?php echo $TEXT['lang-group']?></option>
					<?php if(sizeof($Bhf)>1)
						{
							$i=0;
							while($i < sizeof($Bhf))
							{
								echo "<option value='".$Bhf[$i]['Spur']."'>-> ".$Bhf[$i]['Spur']."</option>";
								$i++;
							}
						}?>
					<option value='' ></option>
				<?php selectSpurOption()?>
			</select>
		</td>
	</form>

	<form action=Treffen.php method=get>
		<td colspan=4 >
	<?php	echo"<select style='background-color:lightblue;width:260px;height:22px;border:1px solid;
			border-color:blue;font-size:16px;font-weight:bold' name=An_ID onchange='submit()'>";
			echo"<option>".$TEXT['lang-an']."</option>";
			echo"<option></option>";
			$result=$db->sql_query("SELECT distinct Haltestelle, Spur, id FROM bahnhof WHERE Art='Connect' AND left(Spur,2) = left('$Spur',2) ORDER BY Haltestelle");
			$i=0;
			while($row=$db->sql_fetchrow($result))
			{
				echo"<option value='$row[id]'>".$row['Haltestelle']."</option><b>";
				$i++;
			}
			echo"</select>"?>
			<input type=hidden name=Treffen value='<?php echo $Treffen?>'>
		</td>
	</form>

	<td class=tabhead valign=top style='font-family:Helvetica;height:28px'><img src=img/blank.gif width=200px height=6px><br><b>&nbsp;<?php echo $TEXT['lang-rem']?></b></td>
	<td></td>
	<td></td>
</tr>

<?php	$query="SELECT treffen.*, bahnhof.Spur, bahnhof.Kurzbezeichnung, bahnhof.Art FROM treffen LEFT JOIN bahnhof on bahnhof.id = treffen.Bhf_ID
			WHERE Treffen = '".escape($Treffen)."' ORDER BY Betriebsstelle, Anschliesser;";
	$result=$db->sql_query($query);

	$SameBhf="";
	$i=0;
	$farbe = 0;
	while( $row=$db->sql_fetchrow($result) )
	{
		if($i>0 and $SameBhf!=$row['Bhf_ID'])
		{
			echo "<tr valign=bottom>";
			echo "<td bgcolor=lightblue colspan=15><img src=img/blank.gif width=1px height=1px></td>";
			echo "</tr>";
		}

		if($Treffen_ID==$row['id']) echo "<tr bgcolor=lightgrey valign=center>";
		else echo "<tr style='background-color:".($farbe?'#E0F0FF':'#F8F8FF')."'valign=center >";
//		echo "<tr valign=center>";
		echo "<td class=tabval><img src=img/blank.gif width=10px height=20px></td>";

		if($SameBhf!=$row['Bhf_ID'])
		{
	 		if(isUserLoggedIn())
			{
				$checkFYP = $db->sql_fetchrow($db->sql_query("SELECT id_fyp FROM fyp WHERE Bhf_ID = '$row[Bhf_ID]'"));
				$checkGleise = $db->sql_fetchrow($db->sql_query("SELECT Gleisname FROM gleise WHERE Bhf_ID = '$row[Bhf_ID]'"));
				$chkPDF = $db->sql_fetchrow($db->sql_query("SELECT Zeichnung, Spur FROM bahnhof WHERE id = '$row[Bhf_ID]'"));
				$checkPDF = rawurlencode(pathinfo($chkPDF['Zeichnung'], PATHINFO_FILENAME).".pdf");

?>				<td class=tabval align=right>
					<a href="TelNr.php?id=<?php echo $row['id']?>"
						onclick="InlinePopup.open(this.href, '', 'statusbar=no,resizable=no,width=300,height=90,title=<?php echo $TEXT['lang-meet'].": ".$Treffen?>');return false;">
						<div style='font-size:90%; color:black; font-family:Helvetica-bold'><?php echo $row['Telefon']?></div>
					</a>
				</td>

<?php			echo "<td class=tabval align=middle style='font-size:90%; color:black; font-family:Helvetica'>".$row['Kurzbezeichnung']."&nbsp;</td>";
?>
				<td class=tabval>
<?php			if($row['Art']!="SBF") { ?>
					<input type=image src='img/<?php if($checkFYP['id_fyp']!="") echo "Y"; else echo "R"?>.png'
					onClick="self.location.href='FYP.php?<?php if($checkFYP['id_fyp']!="")
					echo "action=getpdf&"?>Bhf_ID=<?php echo $row['Bhf_ID']?>&Treffen=<?php if($checkFYP['id_fyp']=="") echo $Treffen?>&lang=<?php echo $lang?>'">
<?php } ?>		</td>

				<td class=tabval><input type=image src='img/<?php
					if($checkGleise['Gleisname']=="" and $row['Kurzbezeichnung']!="") echo "O";
					elseif($row['Kurzbezeichnung']!="") echo "G"; else echo "R"?>.png'
					onClick="self.location.href='FYP.php?<?php if($row['Kurzbezeichnung']!="")
					echo "action=bhf&"?>Bhf_ID=<?php echo $row['Bhf_ID']?>&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>'"></td>

				<td class=tabval width=15px ><input type=image src='img/
					<?php if(file_exists('Module/'.$chkPDF['Spur'].'/'.$checkPDF)) echo "B"; else echo "W"?>.png'
					onClick=self.location.href="Module/<?php echo $chkPDF['Spur']?>/<?php echo $checkPDF?>">
				</td>

<?php			echo "<td class=tabval><a onclick=\"\"href=Bahnhof.php?&Bhf_ID=".$row['Bhf_ID']."&Spur=".urlencode($row['Spur']).">
				".htmlspecialchars($row['Betriebsstelle'])."</a></td>";
			}
			else
			{
				if($row['Telefon']==0) echo "<td class=tabval align=middle></td>";
				else echo "<td class=tabval align=middle><div style='font-size:90%; color:black; font-family:Helvetica-bold'>".$row['Telefon']."</div></td>";

				echo "<td class=tabval style='font-size:90%; color:black; font-family:Helvetica'>".$row['Kurzbezeichnung']."&nbsp;</td>";

				echo "<td class=tabval>&nbsp;</td>";
				echo "<td class=tabval>&nbsp;</td>";

				echo "<td class=tabval>".$row['Betriebsstelle']."&nbsp;</td>";
			}
		}
		else
		{
			echo "<td class=tabval>&nbsp;</td>";
			echo "<td class=tabval>&nbsp;</td>";
			echo "<td class=tabval>&nbsp;</td>";
			echo "<td class=tabval>&nbsp;</td>";
			echo "<td class=tabval>&nbsp;</td>";
			echo "<td class=tabval>&nbsp;</td>";
		}

		if($SameBhf!=$row['Bhf_ID']) echo "<td class=tabval style='font-size:90%; color:black; font-family:Helvetica'>&nbsp;".$row['Spur']."&nbsp;</td>";
		else echo "<td class=tabval>&nbsp;</td>";

		if(isUserLoggedIn() and $row['Anschliesser']!="")
		{
			$checkFYP = $db->sql_fetchrow($db->sql_query("SELECT id_fyp FROM fyp WHERE Bhf_ID = '$row[An_ID]'"));
			$checkGleise = $db->sql_fetchrow($db->sql_query("SELECT Gleisname FROM gleise WHERE Bhf_ID = '$row[An_ID]'"));
			$chkPDF = $db->sql_fetchrow($db->sql_query("SELECT Zeichnung, Spur FROM bahnhof WHERE id = '$row[An_ID]'"));
			$checkPDF = rawurlencode(pathinfo($chkPDF['Zeichnung'], PATHINFO_FILENAME).".pdf");

?>			<td class=tabval width=12px >
<?php		if($row['Art']!="SBF") { ?>
				<input type=image src='img/<?php if($checkFYP['id_fyp']!="") echo "Y"; else echo "R"?>.png'
				onClick="self.location.href='FYP.php?<?php if($checkFYP['id_fyp']!="") echo "action=getpdf&"?>
				<?php if($row['An_ID']!=0) echo "Bhf_ID=".$row['An_ID'];
				else echo "&Betriebsstelle=".$row['Anschliesser']."&Spur=".$Spur?>&Treffen=<?php if($checkFYP['id_fyp']=="") echo $Treffen?>&lang=<?php echo $lang?>'">
<?php } ?>	</td>

			<td class=tabval width=12px ><input type=image src='img/<?php if($checkGleise['Gleisname']=="" and $row['Kurzbezeichnung']!="")
				echo "O"; elseif($row['Kurzbezeichnung']!="") echo "G"; else echo "R"?>.png'
				onClick="self.location.href='FYP.php?<?php if($row['Kurzbezeichnung']!="") echo "action=bhf&";
			if($row['An_ID']!=0) echo "Bhf_ID=".$row['An_ID'];
			else echo "&Betriebsstelle=".$row['Anschliesser']."&Spur=".$Spur?>&Treffen=<?php echo $Treffen?>&lang=<?php echo $lang?>'"></td>

			<td class=tabval width=12px ><input type=image src='img/
				<?php if(file_exists('Module/'.$chkPDF['Spur'].'/'.$checkPDF)) echo "B"; else echo "W"?>.png'
				onClick=self.location.href="Module/<?php echo $chkPDF['Spur']?>/<?php echo $checkPDF?>">
			</td>
<?php
			if($row['An_ID']==0)
			{
				echo "<td class=tabval><a onclick=\"\"href=Bahnhof.php?Betriebsstelle=".urlencode($row['Anschliesser'])."&Spur=".$Spur.">".htmlspecialchars($row['Anschliesser'])."</a></td>";
			}
			else
			{
				echo "<td class=tabval><a onclick=\"\"href=Bahnhof.php?Bhf_ID=".$row['An_ID'].">".htmlspecialchars($row['Anschliesser'])."</a></td>";
			}
		}
		else
		{
			echo "<td class=tabval width=12px >&nbsp;</td>";
			echo "<td class=tabval width=12px >&nbsp;</td>";
			echo "<td class=tabval width=15px >&nbsp;</td>";
			echo "<td class=tabval>".$row['Anschliesser']."&nbsp;</td>";
		}

		$Username = trim(substr($row['Trf_Bem'],1,strpos($row['Trf_Bem'],']')-1));
		if(intval($Username)==0) $rowemail = $db->sql_fetchrow($db->sql_query("SELECT Email, Username FROM Users WHERE Username = '".$Username."'"));
		else $rowemail = $db->sql_fetchrow($db->sql_query("SELECT Email, Username FROM Users WHERE User_ID = '".$Username."'"));
		if($Username!="") $Username="[".$rowemail['Username']."]";

		echo "<td class=tabval style='font-family:Arial Narrow, Arial; font-stretch: condensed;font-size:12px'>
			<a href='mailto:".$rowemail['Email']."?subject=FYPages - ".$TEXT['lang-meet']." ".$Treffen."'>".$Username."</a>
			&nbsp".trim(substr($row['Trf_Bem'],strpos($row['Trf_Bem'],']')+1))."&nbsp;</td>";

		if(isUserLoggedIn())
		{
			echo "<td align=middle ><a onclick=\"return\"href=Treffen.php?action=edit&Treffen_ID=".$row['id']."><img style='height:18px;border:0px' src='img/edit.png'></a></td>";

			echo "<td align=middle><a onclick=\"return confirm('".$TEXT['lang-station'].'\n[ '.$row['Betriebsstelle']
			.' ]\n'.$TEXT['lang-del']."');\"href=Treffen.php?action=delete&Treffen_ID=".$row['id'].">
			<img style='height:18px;border:0px' src='img/del.png'></a></td>";
		}

		echo "<td class=tabval></td></tr>";

		$SameBhf = $row['Bhf_ID'];
		$farbe = !$farbe;
		$i++;
	}
	echo "<tr valign=bottom><td bgcolor=lightblue colspan=15><img src=img/blank.gif width=1px height=8px></td></tr>";
	echo "</table>";
?>






<?php if(isUserLoggedIn()) { ?>
	<table border=0 cellpadding=0 cellspacing=8>
		<td width="98px"></td>
		<td align=left width="370px">
			<font size="2">
			<div style="font-size:80%; color:darkblue; font-family:Verdana">
				<img src=img/blank.gif width=2px height=1px><img src='img/Y.png'> = <?php echo $TEXT['lang-colorlegend1']?><br>
				<img src=img/blank.gif width=2px height=1px><img src='img/R.png'> = <?php echo $TEXT['lang-colorlegend2']?><br>
				<img src=img/blank.gif width=17px height=1px><img src='img/G.png'> = <?php echo $TEXT['lang-colorlegend3']?><br>
				<img src=img/blank.gif width=17px height=1px><img src='img/O.png'> = <?php echo $TEXT['lang-colorlegend4']?><br>
				<img src=img/blank.gif width=17px height=1px><img src='img/R.png'> = <?php echo $TEXT['lang-colorlegend5']?><br>
				<img src=img/blank.gif width=30px height=1px><img src='img/B.png'> = <?php echo $TEXT['lang-colorlegend6']?><br>
				<img src=img/blank.gif width=30px height=1px><img src='img/W.png'> = <?php echo $TEXT['lang-colorlegend7']?>
			</div>
			</font>
		</td>
		<td align=right width="60px" style='font-family:Verdana;'>
			<?php echo $TEXT['lang-group']?>
			<br>
			<?php echo $TEXT['lang-bvw']?>
		</td>
		<form action=Treffen.php method=get>
			<td>
				<select style='width:90px;font-size:12;background-color:Snow' name=Spur1 value='<?php echo $Spur1?>' onchange="submit()">
					<option value=<?php echo $Spur1?> ><?php echo $Spur1?></option>
						<option value='' ></option>
						<?php selectSpurOption()?>
				</select>
				<br>
				<select onchange='this.form.submit()' style='background-color:Snow;width:90px;border:1px solid;font-size:12px' name='Bv' value=".$Bv.">"
					<option><?php echo $Bv?></option>
					<option value='' ></option>
<?php				$result=$db->sql_query("SELECT DISTINCT Bahnverwaltung FROM bahnhof WHERE Spur = '".escape($Spur1)."' ORDER BY Bahnverwaltung;");
echo sql_error();
					while($row=$db->sql_fetchrow($result))
					{
						$row_Bahnverwaltung = $row['Bahnverwaltung'];
						if($row_Bahnverwaltung != '') echo"<option value='$row_Bahnverwaltung'>".$row_Bahnverwaltung."</option><b>";
						$i++;}?>
				</select>
			</td>
		</form>
		<td align=left width="100px"></td>
	<form action='Treffen.php' method='post'>
		<td align=right width="80px">
			
			<input type='image' src='img/Upload_bl.png' border='0' height='80px'  alt='Submit' /><br>
			<input type='submit' style="background-color:lightblue" value="<?php echo $TEXT['lang-add']?>">
			
		</td>
	</table>

			<table border=0 cellpadding=0 cellspacing=0 >
				<tr>
			<?php	if($Bv!="") $andBv = "and Bahnverwaltung = '".$Bv."'"; else $andBv = '';
					if($Spur1=="") $result=$db->sql_query("SELECT * FROM bahnhof ORDER BY Haltestelle;");
					else $result=$db->sql_query("SELECT * FROM bahnhof WHERE Spur = '".escape($Spur1)."' $andBv ORDER BY Haltestelle;");

				    $i=0;
					while($row=$db->sql_fetchrow($result))
					{
						$Betriebsstelle=ucfirst(stripslashes(str_replace("'","`",htmlspecialchars($row['Haltestelle']))));
						$query=$db->sql_query("SELECT * FROM treffen WHERE Betriebsstelle = '".escape($Betriebsstelle)."'
							and Treffen = '".escape($Treffen)."'");
						if($db->sql_fetchrow($query) == "")
						{
							if($row['Art']=="Connect") echo "<th align=left width='280px' style='font-size:90%; color:grey; font-family:Helvetica'>
								<input type='checkbox' name='alle[]' value='$row[id]'>$row[Haltestelle] [An]</th>";
							elseif($row['Art']=="SBF") echo "<th align=left width='280px' style='font-size:90%; color:grey; font-family:Helvetica'>
								<input type='checkbox' name='alle[]' value='$row[id]'>$row[Haltestelle] [Sbf]</th>";
							elseif($row['Art']=="Block") echo "<th align=left width='280px' style='font-size:90%; color:grey; font-family:Helvetica'>
								<input type='checkbox' name='alle[]' value='$row[id]'>$row[Haltestelle] [Blk]</th>";
							else echo "<th align=left width='280px' style='font-size:90%; color:grey; font-family:Helvetica'>
								<input type='checkbox' name='alle[]' value='$row[id]'>$row[Haltestelle]</th>";
						}
						else
						{
							if($row['Art']=="Connect") echo "<th align=left width='280px' style='font-size:90%;
								color:black; font-family:Helvetica'><input type='checkbox' name='alle[]' value='$row[id]' Checked> $row[Haltestelle] [An]</th>";
							elseif($row['Art']=="SBF") echo "<th align=left width='280px' style='font-size:90%;
								color:black; font-family:Helvetica'><input type='checkbox' name='alle[]' value='$row[id]' Checked> $row[Haltestelle] [Sbf]</th>";
							elseif($row['Art']=="Block") echo "<th align=left width='280px' style='font-size:90%;
								color:black; font-family:Helvetica'><input type='checkbox' name='alle[]' value='$row[id]' Checked> $row[Haltestelle] [Blk]</th>";
							else echo "<th align=left width='280px' style='font-size:90%; color:black; font-family:Helvetica'>
								<input type='checkbox' name='alle[]' value='$row[id]' Checked> $row[Haltestelle]</th>";
						}
						if(++$i%4==0){echo '</tr><tr>';}
					}?>
				</tr>
			</table>
		</form>
<?php }} ?>

</body>
</html>
