<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

function getTableContents($Treffen, $Betriebsstelle, $Spur, $NHM_0, $sort)
{	// 
  global $db ;

  if($sort == "") $sort = "fyp.Produktbeschreibung";
	if($Treffen != "")
	{
		$query=$db->sql_query('SELECT * FROM treffen WHERE Treffen = "'.escape($Treffen).'"');
		$i=0;
		$array = "";
		while($line=$db->sql_fetchrow($query)) $array = $array.str_replace("'","`",$line['Betriebsstelle']).", ";
		
		$result = $db->sql_query("SELECT DISTINCT nhm.*, treffen.*, fyp.*, bahnhof.*, gleise.* 
		FROM fyp LEFT JOIN nhm ON nhm.NHM_Code = fyp.NHM_Code
		LEFT JOIN bahnhof ON bahnhof.id = fyp.Bhf_ID
		LEFT JOIN gleise ON gleise.id = fyp.Gleis_ID
		INNER JOIN treffen ON (treffen.Bhf_ID = bahnhof.id OR treffen.An_ID = bahnhof.id)
		WHERE treffen.Treffen = '".escape($Treffen)."' 
		AND fyp.NHM_Code ".$NHM_0."
		ORDER BY ".$sort.", Haltestelle;");
	}
	elseif($Betriebsstelle != "")
	{
		$result = $db->sql_query("SELECT DISTINCT fyp.*, bahnhof.*, nhm.*, gleise.* FROM fyp LEFT JOIN nhm ON fyp.NHM_Code = nhm.NHM_Code
		LEFT JOIN bahnhof ON fyp.Bhf_ID = bahnhof.id
		LEFT JOIN gleise ON gleise.id = fyp.Gleis_ID
		WHERE bahnhof.Haltestelle = '".escape($Betriebsstelle)."' AND fyp.NHM_Code ".$NHM_0."
		AND bahnhof.Spur = '".escape($Spur)."'
		ORDER BY ".$sort.";");
	}
	else
	{
		$result = $db->sql_query("SELECT DISTINCT fyp.*, bahnhof.*, nhm.*, gleise.* FROM fyp LEFT JOIN nhm ON fyp.NHM_Code = nhm.NHM_Code
		LEFT JOIN bahnhof ON fyp.Bhf_ID = bahnhof.id
		LEFT JOIN gleise ON gleise.id = fyp.Gleis_ID
		WHERE fyp.NHM_Code ".$NHM_0." ORDER BY ".$sort.", Haltestelle;");
	}
echo sql_error();
	return $result;
}


function getGleisliste($Bhf_ID)
{	// Gleisliste
  global $db ;

  $result = $db->sql_query("SELECT * FROM gleise WHERE Bhf_ID = '".round($Bhf_ID)."' 
	                          ORDER BY FIELD (Gleisart,'Main','Siding', 'Storage Siding', 'Depot'), 
                                          (0+Gleisname)=0, LPAD(bin(Gleisname), 50, 0), LPAD(Gleisname, 50, 0), Ladestelle;");
echo sql_error();
	return $result;
}

?>