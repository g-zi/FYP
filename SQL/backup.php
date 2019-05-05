<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

set_time_limit(0);
/*
$bu_filename="SQL/backup/FYPages_".date('i')."min.sql"; // minütliches Backup
if (!file_exists($bu_filename) or (date("YmdHi", filemtime($bu_filename)) < date("YmdHi"))) 
_mysqldump($db_host, $db_name, $db_user, $db_pass, $bu_filename);

echo "$bu_filename: ".date("YmdHi", filemtime($bu_filename));
echo " date: ".date("YmdHi.H:i:s.");
if(date("YmdHi", filemtime($bu_filename)) < date("YmdHi")) echo "Backup";
*/

$bu_filename="SQL/backup/FYPages_".date('H')."h.sql"; // Stündliches Backup
if (!file_exists($bu_filename) or (date("YmdH", filemtime($bu_filename)) < date("YmdH"))) _sqldump($bu_filename);

$bu_filename="SQL/backup/FYPages_".date('D').".sql"; // tägliches Backup
if (!file_exists($bu_filename) or (date("Ymd", filemtime($bu_filename)) < date("Ymd"))) _sqldump($bu_filename);

$bu_filename="SQL/backup/FYPages_".date('M').".sql"; // monatliches Backup
if (!file_exists($bu_filename) or (date("Ym", filemtime($bu_filename)) < date("Ym"))) _sqldump($bu_filename);

$bu_filename="SQL/backup/FYPages_".date('Y').".sql"; // jährliches Backup
if (!file_exists($bu_filename)) _sqldump($bu_filename);


function _sqldump($bu_filename)
{
  global $db, $db_name ; 
  
  $db->sql_query("SET NAMES 'utf8'");
  ob_start();
	$result = $db->sql_query("SHOW TABLES");
	if($result)
	{
		echo "/* FREMO Yellow Pages Backup from ".date('l jS F Y H:i:s T')." */\n\n";
		
		while($row = $db->sql_fetchrow($result))
		{
      if($row['Tables_in_'.$db_name]!='nhm') // nicht tabelle nhm sichern
			{
				_sqldump_table_structure($row['Tables_in_'.$db_name]); 
			}
		}
		
		echo "\n";
		$result->data_seek(0);
		
		while($row = $db->sql_fetchrow($result))
		{
      if($row['Tables_in_'.$db_name]!='nhm') // nicht tabelle nhm sichern
			{
				_sqldump_table_data($row['Tables_in_'.$db_name]);
			}
		}
	}
	$db->sql_freeresult($result);
	$dump = ob_get_contents();
	ob_end_clean();
	$handle = fopen ($bu_filename, 'w');
	fwrite ($handle, $dump);
	fclose ($handle);
	chmod($bu_filename, 0666);
}


function _sqldump_table_structure($table)
{
  global $db, $db_name ; 

	echo "/* Structure for table `$table` */\n";
	echo "DROP TABLE IF EXISTS `$table`;\n\n";
	$sql="SHOW CREATE TABLE `$table`";
	$result = $db->sql_query($sql);
	if( $result)
	{
		if($row = $db->sql_fetchrow($result))
		{
			echo $row['Create Table'].";\n\n";
		}
	}
	$db->sql_freeresult($result);
}


function _sqldump_table_data($table)
{
  global $db, $db_name ; 
  
  $result = $db->sql_query("SELECT * from ".$table);
	
	if($result)
	{
		$num_rows = $db->sql_affectedrows();
    $row = $db->sql_fetchrow($result);
    $num_fields = count($row);
		$result->data_seek(0);

		if($num_rows > 0)
		{
			echo "/* Data for table `$table` */\n";
            
			$field_type = array();
			$field_name = array();
			
			$cols = $db->sql_query("SHOW COLUMNS from ".$table);
			while($col = $db->sql_fetchrow($cols))
			{
        array_push($field_name, $col['Field']);
        array_push($field_type, $col['Type']);
			}
			
			echo "insert into `$table` values\n";
			$index = 0;
			while($row = $db->sql_fetchrow($result))
			{
				echo "(";
				for( $i = 0; $i < $num_fields; $i++)
				{
					if(is_null($row[$field_name[$i]])) echo "null";
					elseif(strpos($field_type[$i],'int') !== FALSE ) echo $row[$field_name[$i]]; // integer
					elseif(strpos($field_type[$i],'text') !== FALSE ) echo "'".($row[$field_name[$i]])."'"; // freetext
					else echo "'".escape($row[$field_name[$i]])."'";
					if($i < $num_fields-1) echo ",";
				}
				echo ")";
				if($index < $num_rows-1) echo ",";
				else echo ";";
				echo "\n";
				$index++;
			}
		}
	}
	$db->sql_freeresult($result);
	echo "\n";
}

?>
