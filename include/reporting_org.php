<?php
//Grab data from the cache file of Registry API data
$cachefile = "helpers/groups_cache_dc.json";
$groups = file_get_contents($cachefile);
$groups = json_decode($groups,true);

//Set up an arry of Organisations and their IDs
$reporting_orgs = array();
$excluded_ids = array("To be confirmed.");
foreach ($groups as $key=>$value) {
  if (!empty($value["packages"])) { //only select publishers with files!
    if (!empty($value["extras"]["publisher_iati_id"])) { //only select publishers with and id
      if (!in_array($value["extras"]["publisher_iati_id"],$excluded_ids)) { //don't select publishers with excluded ids
        $reporting_orgs[$value["display_name"]] = $value["extras"]["publisher_iati_id"];
      }
    }
  }
}
//asort($reporting_orgs);
ksort($reporting_orgs);

//Create our html string
echo  '<option value="">- None -</option>' . PHP_EOL;
foreach ($reporting_orgs as $key=>$value) {
  if (isset($org)) {
    if (in_array($value,$org)) { //remember posted variables may be a multi-array
       $selected = 'selected="selected"';
     } else {
       $selected = "";
     }
   }
  echo '<option value="' . trim(htmlspecialchars($value)) . '" ' . $selected . '>' . trim(htmlspecialchars($key)) . ' : ' . trim(htmlspecialchars($value)) . '</option>' . PHP_EOL;
}

//Write this to a file for use in the page
//file_put_contents("../include/reporting_org.php",$options);
//print_r($bad_files);
 ?>
