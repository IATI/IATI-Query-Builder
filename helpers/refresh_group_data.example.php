<?php
/*
 * 
 * This file will grab data from the CKAN API to populate the 
 * data about groups used for the Reporting_org Dropdown
 * 
 * Make a copy of this file, and uncomment the code.
 * Rename it to something that others won't find!
 * Set your own cache file destination - but beware that 
 * /include/reporting_org.php needs to find it!
 * 
 * This way people can't just hammer your server and the API
 * by hitting this URL
 * 
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
  $data = $ckan->get_group_register()->result;
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
  if (count($data) > 500) {
    for ($i = 0; $i < count($data); $i++) {
      $groups[$data[$i]] = $ckan->get_group_entity($data[$i]);
    }
    $groups = json_encode($groups);
    file_put_contents($cachefile,$groups);
  }
}
*/
?>

