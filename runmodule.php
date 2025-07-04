<?php
// translator ready
// addnews ready
// mail ready

define("ALLOW_ANONYMOUS",true);
define("OVERRIDE_FORCED_NAV",true);

use Lotgd\DumpItem;
require_once("lib/http.php");

require_once("common.php");
require_once("lib/modules.php");
require_once("lib/villagenav.php");

if (injectmodule(httpget('module'), (httpget('admin')?true:false))){
	$info = get_module_info(httpget('module'));
	if (!isset($info['allowanonymous'])){
		$allowanonymous=false;
	}else{
		$allowanonymous = $info['allowanonymous'];
	}
	if (!isset($info['override_forced_nav'])){
		$override_forced_nav=false;
	}else{
		$override_forced_nav=$info['override_forced_nav'];
	}
	do_forced_nav($allowanonymous,$override_forced_nav);

	$starttime = getmicrotime();
	$fname = $mostrecentmodule."_run";
	tlschema("module-$mostrecentmodule");
	$fname();
	$endtime = getmicrotime();
	if (($endtime - $starttime >= 1.00 && ($session['user']['superuser'] & SU_DEBUG_OUTPUT))){
		//On a side note, you won't ever see this text. A normal module calls page_footer(), which ends execution here....
		debug("Slow Module (".round($endtime-$starttime,2)."s): $mostrecentmodule`n");
		$stats = array (
			"modulename"=>$mostrecentmodule,
			"date"=>date("Y-m-d H:i:s"),
			"duration"=>round($endtime-$starttime,5),
			);
		//remember, this gets only called if you're a user with debug output triggering this!
		modulehook("modules_slowmodule",$stats);
	}
	tlschema();
}else{
	do_forced_nav(false,false);

	tlschema("badnav");

	page_header("Error");
	if ($session['user']['loggedin']){
		villagenav();
	}else{
		addnav("L?Return to the Login","index.php");
	}
	output("You are attempting to use a module which is no longer active, or has been uninstalled.");
	page_footer();
}
?>
