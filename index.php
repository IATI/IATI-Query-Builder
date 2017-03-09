<?php
require_once 'vendor/autoload.php';

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
    'debug' => true,
));

//print_r($_POST);
if (isset($_POST["reset"])) {
    unset($_POST);
  }
if (isset($_POST) && $_POST != NULL) {
  
  $allowed_datasets = array("activity","transaction","budgets");
  if (isset($_POST["entry_1085079344"])) { //dataset
    $requested_dataset = filter_var($_POST["entry_1085079344"], FILTER_SANITIZE_STRING);
    if (in_array($requested_dataset, $allowed_datasets)) {
      $dataset = $requested_dataset;
    }
  }
  
  $allowed_formats = array("summary","by_sector","by_country");
  if (isset($_POST["entry_71167035"])) { //format
    $requested_format = filter_var($_POST["entry_71167035"], FILTER_SANITIZE_STRING);
    if (in_array($requested_format, $allowed_formats)) {
      $format = $requested_format;
    }
  }
  $allowed_sizes = array("50 rows","Entire selection");
  if (isset($_POST["entry_1352830161"])) { //sample size
    $requested_size = filter_var($_POST["entry_1352830161"], FILTER_SANITIZE_STRING);
    if (in_array($requested_size, $allowed_sizes)) {
      $size = $requested_size;
    if ($size == "Entire selection" ) {
        $size = "stream=True";
      }
    }
  }
  
  //The rest of the values can be multiselect values so they are all passed as arrays!
  
  $allowed_orgs = array();
  //Build allowed orgs from the cache of org info
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
          $allowed_orgs[] = $value["extras"]["publisher_iati_id"];
        }
      }
    }
  }
  //print_r($allowed_orgs);
  if (isset($_POST["entry_1922375458"])) { //organisations
    $requested_orgs = filter_var_array($_POST["entry_1922375458"], FILTER_SANITIZE_STRING);
    foreach ($requested_orgs as $requested_org) {
      if (in_array($requested_org, $allowed_orgs) && !empty($requested_org) ) { //!!!!FIX ME!!!!
        $orgs[] = $requested_org;
      }
    }
  }

  //$allowed_types = array();
  if (isset($_POST["entry_18398991"])) { //types
    $requested_type = filter_var_array($_POST["entry_18398991"], FILTER_SANITIZE_STRING);
    $type = build_sanitised_multi_select_values("codelists/OrganisationType.csv",$requested_type); //returns Null if 'none is selected
    //print_r($type);
  }

  //$allowed_sectors = array();
  if (isset($_POST["entry_1954968791"])) { //sectors
    $requested_sector = filter_var_array($_POST["entry_1954968791"], FILTER_SANITIZE_STRING);
    $sector = build_sanitised_multi_select_values("codelists/Sector.csv",$requested_sector); //returns Null if 'none is selected
  }


  if (isset($_POST["entry_605980212"])) { //countries
    $requested_countries = filter_var_array($_POST["entry_605980212"], FILTER_SANITIZE_STRING);
    $country = build_sanitised_multi_select_values("codelists/Country.csv", $requested_countries); //returns Null if 'none is selected
  }

  
  //$allowed_regions = array();
  if (isset($_POST["entry_1179181326"])) { //organisations
    $requested_region = filter_var_array($_POST["entry_1179181326"], FILTER_SANITIZE_STRING);
    $region = build_sanitised_multi_select_values("codelists/Region.csv",$requested_region); //returns Null if 'none is selected
  }
  
  if (isset($region) && isset($country)) {
    unset($region);
    unset($country);
    $notice_message = "** You cannot select both a country and a region **";
  }
  if (isset($dataset) && isset($format) && isset($size)) {
   //&& isset($org) && isset($type) && isset($sector) && (isset($country) || isset($region)) ) {
    $api_link = "http://datastore.iatistandard.org/";
    $api_link .= "api/1/access/";
    $api_link .= $dataset;
    if (isset($format) && $format == "by_sector" || $format == "by_country") {
      $api_link .= "/" . $format;
    }
    $api_link .= ".csv";
   //echo $api_link;
   //print_r($orgs);
    if ( isset($orgs) || isset($type) || isset($sector) || isset($country) || isset($region) ) {
      $api_link .= "?";
      $api_link_parameters = array();
      if (isset($orgs)) {
        $api_link_parameters ["reporting-org"] = implode('|',$orgs);
      }
      if (isset($type)) {
        $api_link_parameters ["reporting-org.type"] = implode('|',$type);
      }
      if (isset($sector)) {
        $api_link_parameters ["sector"] = implode('|',$sector);
      }
      if (isset($country) && !isset($region)) {
        $api_link_parameters ["recipient-country"] = implode('|',$country);
      }
      if (isset($region) && !isset($country)) {
        $api_link_parameters ["recipient-region"] = implode('|',$region);
      }
      if ($size == "stream=True") {
        $api_link_parameters ["stream"] = "True";
      }
      $api_link .= http_build_query($api_link_parameters);
    }
  } else {
    $error_message = "You must select something from each of the 3 required fields";
  }
}
/*DEBUG
echo  "<br/>";
echo $dataset . "<br/>";
echo $format . "<br/>";
echo $requested_size . "<br/>";
echo $org . "<br/>";
echo $type . "<br/>";
echo $sector . "<br/>";
echo $country . "<br/>";
echo $region . "<br/>";
*/

function csv_to_array ($path_to_csv) {
  if (($handle = fopen($path_to_csv, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ","); //skip first line
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
         $new_array[] =  $data[0];
      }
      fclose($handle);
  }
  return $new_array;
}
/*
 * 
 * name: build_sanitised_multi_select_values
 * @param   $path_to_csv          string      A path to a csv file - e.g. containing county codes
 * @param   $sanitized_post_var   string      A user passed variable from a form of e.g. country codes
 * @return  $values               array       an array of allowed values based on those requested
 * @return  NULL If only 'none' has been selected
 * 
 */

function build_sanitised_multi_select_values ($path_to_csv,$sanitized_post_var) {
  $values = array();
  //$allowed_countries = csv_to_array("codelists/Country.csv");
  $allowed_values = csv_to_array($path_to_csv);
  //print_r($allowed_values);
  //print_r($sanitized_post_var);
  if (!empty($sanitized_post_var)) { 
    foreach ($sanitized_post_var as $requested_value) {
      if (in_array($requested_value, $allowed_values) && !empty($requested_value)) { //check it's an allowed value and also that it is not empty
        $values[] = $requested_value;
      }
    }
  }
  if (empty($values)) {
    return; //returns NULL
  } else {
    return $values;
  }
}

function reporting_orgs() {
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

  ksort($reporting_orgs, SORT_NATURAL | SORT_FLAG_CASE);

  return $reporting_orgs;
}

function get_countries() {
  $countries = array();
  $country_file = "codelists/Country.csv";
  if (($handle = fopen($country_file, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ","); //skip first line
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $data[0] = htmlspecialchars($data[0]);
          $data[1] = mb_convert_case($data[1], MB_CASE_TITLE, 'UTF-8'); // Convert case based on unicode character properties
          $data[1] = htmlspecialchars($data[1]);
          $countries[] = $data;
      }
      fclose($handle);
  }
  return $countries;
}

function get_regions() {
  $regions = array();
  $region_file = "codelists/Region.csv";
  if (($handle = fopen($region_file, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ","); //skip first line
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $regions[] = $data;
      }
      fclose($handle);
  }
  return $regions;
}

function get_org_types() {
  $org_types = array();
  $org_type_file = "codelists/OrganisationType.csv";
  if (($handle = fopen($org_type_file, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ","); //skip first line
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $org_types[] = $data;
      }
      fclose($handle);
  }
  return $org_types;
}

function get_sector_categories() {
  $selected = "";
  $category = "";
  $categories = array();

  //1.04 codelist changes so we need to get both DAC-3 categories
  //and DAC-% categories from seperate lists.

  //Make an array of categories and their names
  $dac_3_categories_data = "codelists/SectorCategory.csv";
  if (($handle = fopen($dac_3_categories_data, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ","); //skip first line
      while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
        $dac_3_categories[$data[0]] = $data[1];
      }
  }
  //print_r($dac_3_categories); die;

  $current_data = array();
  $current_category = "";

  $reporting_org_type_file = "codelists/Sector.csv";
  if (($handle = fopen($reporting_org_type_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); //skip first line
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
      //Put options into categories
      $next_category = htmlspecialchars($data[4]);
      if ($category == "") { //First run through - categories not set, so set it as the first value found
        $category = $next_category;
        $category_name = $dac_3_categories[$category];
        $category_name = ucfirst(strtolower(htmlspecialchars($category_name)));
      }
      if ($next_category != $category) {
        $categories[] = array(
          'category' => $category,
          'category_name' => $category_name,
          'data' => $current_data
        );
        $category = $next_category;
        //echo $category; print_r($data);die;
        //Find the associated name of the category
        $category_name = $dac_3_categories[$category];
        $category_name = ucfirst(strtolower(htmlspecialchars($category_name)));
        $current_data = array();
      }
      $current_data[] = $data;
    }
    $categories[] = array(
      'category' => $category,
      'category_name' => $category_name,
      'data' => $current_data
    );
    fclose($handle);
  }

  return $categories;
}

$context = array();

$context['api_link'] = isset($api_link) ? $api_link : null;
$context['notice_message'] = isset($notice_message) ? $notice_message : null;
$context['error_message'] = isset($error_message) ? $error_message : null;

$context['dataset'] = isset($dataset) ? $dataset : null;
$context['format'] = isset($format) ? $format : null;
$context['size'] = isset($size) ? $size : null;

$context['selected_orgs'] = isset($orgs) ? $orgs : null;
$context['selected_sectors'] = isset($sector) ? $sector : null;
$context['selected_countries'] = isset($country) ? $country : null;
$context['selected_org_types'] = isset($type) ? $type : null;
$context['selected_regions'] = isset($region) ? $region : null;

$context['reporting_orgs'] = reporting_orgs();
$context['countries'] = get_countries();
$context['regions'] = get_regions();
$context['org_types'] = get_org_types();
$context['sector_categories'] = get_sector_categories();

echo $twig->render('index.html', $context);
?>
