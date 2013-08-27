<?php

$dir = "../../aidinfo-data/backend/raw_IATI_xml_data/";
require_once 'functions/xml_child_exists.php';
/*$reporting_org_ref_count = 0;
$exclude = array("GB","EU");
$bad_codes = array();
$rows ="";
*/
$bad_files = array();
$reporting_orgs = array();

$dirs = scandir($dir); //all the folders in our directory holding the data
unset($dirs[0]); // unset the . value
unset($dirs[1]); //unset the .. value
//print_r($dirs); die;
/* To test a subset or single dataset, put an array of dataset here:*/
//$dirs = array("acdi_cida");
//$dirs = array("concernuk"); //conditions,location
//$dirs = array("unops"); //location
//$dirs = array("dfid");
//$dirs = array("maec-dgpolde");
//$dirs = array("theglobalfund");
//$dirs = array("worldbank");
//$dirs = array("concernuk");
//$dirs = array("minbuza_nl");
//$dirs = array("akfuk73");
//$dirs = array("unitedstates");
//$exclude  = array("art19");
//$dirs = array("sida");
$exclude  = array();
foreach ($dirs as $corpus) {

  if ($handle = opendir($dir . $corpus)) {
      //echo "Directory handle: $handle\n";
      //echo "Files:\n";

      /* This is the correct way to loop over the directory. */
      while (false !== ($file = readdir($handle))) {
        echo $file . PHP_EOL;
          //if ($file != "." && $file != ".." && $corpus == "dfid" || $corpus == "worldbank") { //ignore these system files
          if ($file != "." && $file != ".." ) { //ignore these system files
              //echo $file . PHP_EOL;
              //load the xml
              //echo $dir . $corpus . '/' . $file; die;
              if ($xml = simplexml_load_file($dir . $corpus . '/' . $file)) {;
              //print_r($xml); //debug
                  foreach ($xml as $activity) {
                      
                      //CHECK: Participating org Code matches output text
                      foreach ($activity->{'reporting-org'} as $reporting_org) {
                          $reporting_org_ref = (string)$reporting_org->attributes()->ref;
                          $reporting_org_name = (string)$reporting_org;
                          //if ($reporting_org_ref == NULL) { $reporting_org_ref = ""; }
                          //echo $reporting_org_ref . PHP_EOL;
                          if ($reporting_org_ref != NULL) {
                            $reporting_orgs[$reporting_org_ref] = $reporting_org_name;
                          }                                                   
                      }
                  } 
              } else {
                  $bad_files[] = $file;
              }
              
          }// end if file is not a system file
      } //end while
      closedir($handle);
  }
}
asort($reporting_orgs);
echo '<option value="">- None -</option>' . PHP_EOL;
foreach ($reporting_orgs as $key=>$value) {
  echo '<option value="' . trim($key) . '">' . trim($value) . ' : ' . trim($key) . '</option>' . PHP_EOL;
}
 ?>
