<?php 
	$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";      
	//***********************************************************************************************************************
	// V1.0 : Script de gestion d'une alarme r�veil
	//*********************************************************************************************************
	
  	// mode
  	$mode = getArg("mode", $mandatory = true, $default = 'poll');
  	// alarm
  	$alarm = getArg("alarm", $mandatory = true, $default = 1);
  	// jour
  	$jour = getArg("jour", $mandatory = false, $default = 'LUN-MAR-MER-JEU-VEN');
  	// code api du capteur appelant
  	$actualapi = getArg('eedomus_controller_module_id');
  
  
  if ($mode == "set") {
	// l'api du p�riph�rique appelant est celle de l'alarme
	// on stocke ce code en m�moire
     	saveVariable("MYREVEIL".$alarm, $actualapi);
	die();
  }

  if ($mode == "poll") {
	$apireveil = $actualapi;
	

	$heurenum = date("H").":".date("i");
  	$heurelit = date("G")." heure ".date("i");
	$currentday = date("N");
	$currentmonth = date("n");
 	$calendriernum = date("d")."/".date("m")."/".date("Y");
	$tabjoursem = array("1" => "Lundi", "2" => "Mardi", "3" => "Mercredi", "4" => "Jeudi", "5" => "Vendredi", "6" => "Samedi", "7" => "Dimanche");
	$tabjourparam = array("LUN" => 1, "MAR" => 2, "MER" => 3, "JEU" => 4, "VEN" => 5, "SAM" => 6, "DIM" => 7);
	$tabmois = array("1" => "janvier", "2" => "f�vrier", "3" => "mars", "4" => "avril", "5" => "mai", "6" => "juin", "7" => "juillet", "8" => "ao�t", "9" => "septembre", "10" => "octobre", "11" => "novembre", "12" => "d�cembre");
	$jourlit = date("j");
	if (date("j") == 1) {
		$jourlit = "1er";
	}
	$calendrierlit = $tabjoursem[date("N")]." ".$jourlit." ".$tabmois[date("n")]." ".date("Y");

	// test des param�tres de jour de l'alarme
 	$tabjour = explode("-", $jour);
 	$cejournum = date("N");
	$jouralarm = false;
	foreach ($tabjour as $testjour) {
		if ($tabjourparam[$testjour] == $cejournum) {
			$jouralarm = true;
		}
	}

	// Lecture des donn�es de l'alarme li�e au r�veil
  	if (loadVariable("MYREVEIL".$alarm)) {
		  $apialarm = loadVariable("MYREVEIL".$alarm);
 	} else {
		  $apialarm  = 0;
     	}
     
	$reveil = 0;
  	if ($apialarm == 0) {
		// l'actionneur alarm n'a pas �t� positionn� encore
		$alarm_value = 0;
		$alarm_text = "";
  	} else {
		// r�cup�ration de la valeur de l'alarme
		$tabalarm = getValue($apialarm, true);
		$alarm_value = $tabalarm["value"];
		$alarm_text = $tabalarm["value_text"];
    	 	// test si r�veil sonne
		if ($alarm_text == $heurenum && $jouralarm) {
			$reveil = 1;	    
           	}
  	}

	$xml .= "<REVEIL>";
    $xml .= "<ALARM>".$reveil."</ALARM>";
	$xml .= "<HORLOGE>".$heurenum."</HORLOGE>";
	$xml .= "<HORLOGEL>".$heurelit."</HORLOGEL>";
    $xml .= "<CALENDRIER>".$calendriernum."</CALENDRIER>";
	$xml .= "<CALENDRIERL>".$calendrierlit."</CALENDRIERL>";
	$xml .= "<DAY>".$currentday."</DAY>";
	$xml .= "<MONTH>".$currentmonth."</MONTH>";
    $xml .= "</REVEIL>";
	sdk_header('text/xml');
	echo $xml;
  }

?>