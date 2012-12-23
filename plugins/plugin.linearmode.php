<?php
/**
 * Royal linear Mode
 * Changes the Royal Poitlimit automatically 
 * Dependencies: none
 */


//Aseco::addChatCommand('linearmode', 'Changes the linearmode settings', true);
//Todo: chatcommand , check if gamemode = royal

	
class linearmode extends Plugin {
  private $multipliere;
  private $offset;
  private $min_value;
  private $max_value;
  
  function init(){
    $this->pluginVersion = '0.01';	
    $this->pluginAuthor = 'Lukas Kremsmayr';
  }
  
	function mpasecoStartup(){
   $conf_file     = 'configs/plugins/linearmode.xml'; 
    if (file_exists($conf_file)) { 
     $this->Aseco->console('Load linearmode config file [' . $conf_file . ']');
  	 if ($xml = $this->Aseco->xml_parser->parseXml($conf_file)) {
  	    /* Xml Settings_ */
        $multiplier  = $xml['LINEARMODE_SETTINGS']['FACTOR'][0];
        $offset      = $xml['LINEARMODE_SETTINGS']['OFFSET'][0];
        $min_value   = $xml['LINEARMODE_SETTINGS']['MIN_VALUE'][0];
        $max_value   = $xml['LINEARMODE_SETTINGS']['MAX_VALUE'][0];
    } else {
    trigger_error('Could not read/parse linearmode config file ' . $conf_file . ' !', E_USER_WARNING);
    }
   } else {
    trigger_error('Could not find jfreu linearmode config file ' . $conf_file . ' !', E_USER_WARNING);
   }    
	}

  function change_pointlimit() {
    /* Playercount: */
    $CurrentPlayerCount = count($aseco->server->players->player_list);
    /* Spectatorcount: */  
    $CurrentSpectatorCount = 0;
    foreach ($aseco->server->players->player_list as &$player) {
      if ($player->isspectator == 1) 
      	$CurrentSpectatorCount++;
    }
    unset($player);
    $playercount = $CurrentPlayerCount - $CurrentSpectatorCount;
      
    $pointlimit=($players * $multiplier) + $offset;
    if($pointlimit < $min_value)     
      $pointlimit = $min_value;        //Min Value
    if($pointlimit > $max_value)
      $pointlimit = $max_value;       //Max Value
                  
    $scriptset = array('S_MapPointsLimit' => $pointlimit);   
      
    $this->Aseco->client->query('SetModeScriptSettings',  $scriptset);         
  }  
}

global $linearmode;
$linearmode = new linearmode();
$linearmode->init();
$linearmode->setAuthor($linearmode->pluginAuthor); 
$linearmode->setVersion($linearmode->pluginVersion);
$linearmode->setDescription('Changes automatically the Pointlimit in Royal Mode.');
	
/* Register the used Events */
Aseco::registerEvent('onStartup', 'linearmode_mpasecoStartup');  
Aseco::registerEvent('onPlayerConnect', 'change_pointlimit');
Aseco::registerEvent('onPlayerDisconnect', 'change_pointlimit');

/* Events: */  
function linearmode_mpasecoStartup($aseco){
	global $linearmode;
	if (!$linearmode->Aseco){
			$linearmode->Aseco = $aseco;
	}
	$linearmode->mpasecoStartup();
}

function change_pointlimit($aseco){
	global $linearmode;
	$linearmode->change_pointlimit();
}
	
?>