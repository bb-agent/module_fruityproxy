<? 
/*
	Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?

//include "../../../login_check.php";
include "../../../config/config.php";
include "../_info_.php";
include "../../../functions.php";

include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
	regex_standard($_POST['type'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['tempname'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_action'], "../../../msg.php", $regex_extra);
	regex_standard($_GET['mod_service'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename'], "../../../msg.php", $regex_extra);
	regex_standard($_POST['new_rename_file'], "../../../msg.php", $regex_extra);
}

$type = $_POST['type'];
$tempname = $_POST['tempname'];
$action = $_POST['action'];
$mod_action = $_GET['mod_action'];
$mod_service = $_GET['mod_service'];
$newdata = html_entity_decode(trim($_POST["newdata"]));
$newdata = base64_encode($newdata);
$new_rename = $_POST["new_rename"];
$new_rename_file = $_POST["new_rename_file"];

// ngrep options
if ($type == "opt_value") {

    $tmp = array_keys($opt_value);
    for ($i=0; $i< count($tmp); $i++) {
        
        $exec = "/bin/sed -i 's/opt_value\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/opt_value\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 0;/g' options_config.php";
        $output = exec_blackbulb($exec);
        
        $exec = "/bin/sed -i 's/^".$tmp[$i].".*/".$tmp[$i]." = Off/g' Responder-master/Responder.conf";
        //$output = exec_blackbulb($exec);
        
    }
	
    $tmp = $_POST["options"];
    for ($i=0; $i< count($tmp); $i++) {
        
        $exec = "/bin/sed -i 's/opt_value\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\].*/opt_value\\[\\\"".$tmp[$i]."\\\"\\]\\[0\\] = 1;/g' options_config.php";
		//echo $exec . "<br>";
        $output = exec_blackbulb($exec);
        
        $exec = "/bin/sed -i 's/^".$tmp[$i].".*/".$tmp[$i]." = On/g' Responder-master/Responder.conf";
        //exec_blackbulb($exec);
        
    }

    header('Location: ../index.php?tab=2');
    exit;

}

if ($type == "config") {

    if ($newdata != "") {
		//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
        $newdata = preg_replace("/[\n\r]/",  "", $newdata);
		$exec = "$bin_echo '$newdata' | base64 --decode > $mod_path/includes/FruityProxy-master/fruityproxy.conf";
        exec_blackbulb($exec);
        
        $exec = "$bin_dos2unix $mod_path/includes/fruityproxy/fruityproxy.conf";
        exec_blackbulb($exec);
    }

    header('Location: ../index.php?tab=tab-config');
    exit;

}

if ($type == "inject") {

    if ($newdata != "") {
		//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
        $newdata = preg_replace("/[\n\r]/",  "", $newdata);
		$exec = "$bin_echo '$newdata' | base64 --decode > $mod_path/includes/FruityProxy-master/content/InjectHTML/inject.txt";
        exec_blackbulb($exec);
        
        $exec = "$bin_dos2unix $mod_path/includes/FruityProxy-master/content/InjectHTML/inject.txt";
        exec_blackbulb($exec);
    }

    header('Location: ../index.php?tab=tab-InjectHTML');
    exit;

}

if ($type == "templates") {
	if ($action == "save") {
		
		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") {
				//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
				$newdata = preg_replace("/[\n\r]/",  "", $newdata);
				$template_path = "$mod_path/includes/MITMf/config/app_cache_poison_templates";
        		$exec = "$bin_echo '$newdata' | base64 --decode > $template_path/$tempname";
                exec_blackbulb($exec);
                
                $exec = "$bin_dos2unix $template_path/$tempname";
                exec_blackbulb($exec);
                
    		}
    	}
    	
	} else if ($action == "add_rename") {
	
		if ($new_rename == "0") {
			//CREATE NEW TEMPLATE
			if ($new_rename_file != "") {
				$template_path = "$mod_path/includes/MITMf/config/app_cache_poison_templates";
				$exec = "$bin_touch $template_path/$new_rename_file";
                exec_blackbulb($exec);

				$tempname=$new_rename_file;
			}
		} else {
			//RENAME TEMPLATE
			$template_path = "$mod_path/includes/MITMf/config/app_cache_poison_templates";
			$exec = "$bin_mv $template_path/$new_rename $template_path/$new_rename_file";
            exec_blackbulb($exec);

			$tempname=$new_rename_file;
		}
		
	} else if ($action == "delete") {
		if ($new_rename != "0") {
			//DELETE TEMPLATE
			$template_path = "$mod_path/includes/MITMf/config/app_cache_poison_templates";
			$exec = "$bin_rm $template_path/$new_rename";
            exec_blackbulb($exec);
		}
	}
	header("Location: ../index.php?tab=5&tempname=$tempname");
	exit;
}

if ($type == "filters") {
	if ($action == "save") {
		
		if ($tempname != "0") {
			// SAVE TAMPLATE
			if ($newdata != "") {
				//$newdata = ereg_replace(13,  "", $newdata); // DEPRECATED
				$newdata = preg_replace("/[\n\r]/",  "", $newdata);
				$template_path = "$mod_path/includes/filters/resources/";
        		$exec = "$bin_echo '$newdata' | base64 --decode > $template_path/$tempname";
                exec_blackbulb($exec);
                
                $exec = "$bin_dos2unix $template_path/$tempname";
                exec_blackbulb($exec);
    		}
    	}
    	
	}
	header("Location: ../index.php?tab=tab-filters&tempname=$tempname");
	exit;
}

if($mod_service == "mod_sslstrip_filter") {
    $exec = "$bin_sed -i 's/mod_sslstrip_filter=.*/mod_sslstrip_filter=\\\"".$mod_action."\\\";/g' ../_info_.php";
    exec_blackbulb($exec);
}

header('Location: ../index.php');

?>