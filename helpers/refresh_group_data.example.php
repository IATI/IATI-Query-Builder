<?php

// Display errors for demo
@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', 'stdout');

// Include Ckan_client
require_once('CKAN_Code/Ckan_client.php');

// Create CKAN object
// Takes optional API key parameter. Required for POST and PUT methods.
$ckan = new Ckan_client();
    

try
{
  $data = $ckan->get_group_register();
  if ($data):
  //print_r($data);
  endif;
}
catch (Exception $e)
{
  print '<p><strong>Caught exception: ' . $e->getMessage() . 
    '</strong></p>';
}


$cachefile = "groups_cache_dc.json";
refresh_groups_cache($ckan,$data,$cachefile);

   


function refresh_groups_cache ($ckan,$data,$cachefile) {
  echo "Refreshing Group data...<br/>";
  $groups = array();
  for ($i = 0; $i < count($data); $i++) {
    $groups[$data[$i]] = $ckan->get_group_entity($data[$i]);
  }
  $groups = json_encode($groups);
  file_put_contents($cachefile,$groups);
}
  
?>
	
