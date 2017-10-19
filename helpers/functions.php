<?php

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

  uksort($reporting_orgs, "strcasecmp");

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
} // duplicates csv_to_array function

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
} // duplicates csv_to_array function

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
  echo "<pre>"; print_r($categories); echo "</pre>"; //debugging
  return $categories;
}
?>
