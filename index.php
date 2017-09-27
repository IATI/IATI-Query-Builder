<?php
require_once 'vendor/autoload.php';
require_once 'helpers/functions.php';

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader, array(
    'debug' => true,
));

//print_r($_POST);
if (isset($_POST["reset"])) {
    unset($_POST);
  }
if (isset($_POST) && $_POST != NULL) {

  $allowed_formats = array("activity","transaction","budget");
  if (isset($_POST["format"])) {
    $requested_format = filter_var($_POST["format"], FILTER_SANITIZE_STRING);
    if (in_array($requested_format, $allowed_formats)) {
      $format = $requested_format;
    }
  }

  $allowed_groupings = array("summary","by_sector","by_country");
  if (isset($_POST["grouping"])) {
    $requested_grouping = filter_var($_POST["grouping"], FILTER_SANITIZE_STRING);
    if (in_array($requested_grouping, $allowed_groupings)) {
      $grouping = $requested_grouping;
    }
  }
  $allowed_sizes = array("50 rows","Entire selection");
  if (isset($_POST["sample-size"])) {
    $requested_size = filter_var($_POST["sample-size"], FILTER_SANITIZE_STRING);
    if (in_array($requested_size, $allowed_sizes)) {
      $size = $requested_size;
      if ($size == "Entire selection" ) {
        $size = "stream=True";
      }
    }
  }
  $non_allowed_values = array("");
  if (isset($_POST["start_date__lt"])) {
    $requested_date = filter_var($_POST["start_date__lt"], FILTER_SANITIZE_STRING);
    if (!in_array($requested_date, $non_allowed_values)) {
      $start_date__lt[] = $requested_date;
    }
  }
  if (isset($_POST["start_date__gt"])) {
    $requested_date = filter_var($_POST["start_date__gt"], FILTER_SANITIZE_STRING);
    if (!in_array($requested_date, $non_allowed_values)) {
      $start_date__gt[] = $requested_date;
    }
  }
  if (isset($_POST["end_date__lt"])) {
    $requested_date = filter_var($_POST["end_date__lt"], FILTER_SANITIZE_STRING);
    if (!in_array($requested_date, $non_allowed_values)) {
      $end_date__lt[] = $requested_date;
    }
  }
  if (isset($_POST["end_date__gt"])) {
    $requested_date = filter_var($_POST["end_date__gt"], FILTER_SANITIZE_STRING);
    if (!in_array($requested_date, $non_allowed_values)) {
      $end_date__gt[] = $requested_date;
    }
  }
  //The rest of the values can be multiselect values so they are all passed as arrays!

  $allowed_orgs = array();
  //Build allowed orgs from the cache of org info
  $cachefile = "helpers/groups_cache_dc.json";
  $groups = file_get_contents($cachefile);
  $groups = json_decode($groups,true);

  //Set up an array of Organisations and their IDs
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
  if (isset($_POST["reporting-org"])) {
    $requested_orgs = filter_var_array($_POST["reporting-org"], FILTER_SANITIZE_STRING);
    foreach ($requested_orgs as $requested_org) {
      if (in_array($requested_org, $allowed_orgs) && !empty($requested_org) ) {
        $orgs[] = $requested_org;
      }
    }
  }
  //print_r($allowed_orgs);
  if (isset($_POST["transaction_provider-org"])) { //organisations
    $requested_orgs = filter_var_array($_POST["transaction_provider-org"], FILTER_SANITIZE_STRING);
    foreach ($requested_orgs as $requested_org) {
      if (in_array($requested_org, $allowed_orgs) && !empty($requested_org) ) {
        $provider_orgs[] = $requested_org;
      }
    }
  }
  if (isset($_POST["participating-org"])) {
    $requested_orgs = filter_var_array($_POST["participating-org"], FILTER_SANITIZE_STRING);
    foreach ($requested_orgs as $requested_org) {
      if (in_array($requested_org, $allowed_orgs) && !empty($requested_org) ) {
        $participating_orgs[] = $requested_org;
      }
    }
  }
  //print_r($allowed_orgs);
  //$allowed_types = array();
  if (isset($_POST["reporting-org_type"])) {
    $requested_type = filter_var_array($_POST["reporting-org_type"], FILTER_SANITIZE_STRING);
    $type = build_sanitised_multi_select_values("codelists/OrganisationType.csv",$requested_type); //returns Null if 'none is selected
    //print_r($type);
  }


  //$allowed_sectors = array();
  if (isset($_POST["sector"])) {
    $requested_sector = filter_var_array($_POST["sector"], FILTER_SANITIZE_STRING);
    $sector = build_sanitised_multi_select_values("codelists/Sector.csv",$requested_sector); //returns Null if 'none is selected
  }


  if (isset($_POST["recipient-country"])) { //countries
    $requested_countries = filter_var_array($_POST["recipient-country"], FILTER_SANITIZE_STRING);
    $country = build_sanitised_multi_select_values("codelists/Country.csv", $requested_countries); //returns Null if 'none is selected
  }


  //$allowed_regions = array();
  if (isset($_POST["recipient-region"])) { //organisations
    $requested_region = filter_var_array($_POST["recipient-region"], FILTER_SANITIZE_STRING);
    $region = build_sanitised_multi_select_values("codelists/Region.csv",$requested_region); //returns Null if 'none is selected
  }

  if (isset($region) && isset($country)) {
    unset($region);
    unset($country);
    $notice_message = "** You cannot select both a country and a region **";
  }
  if (isset($format) && isset($grouping) && isset($size)) {
   //&& isset($org) && isset($type) && isset($sector) && (isset($country) || isset($region)) ) {
    $api_link = "http://datastore.iatistandard.org/";
    $api_link .= "api/1/access/";
    $api_link .= $format;
    if (isset($grouping) && $grouping == "by_sector" || $grouping == "by_country") {
      $api_link .= "/" . $grouping;
    }
    $api_link .= ".csv";
   //echo $api_link;
   //print_r($orgs);
    if ( isset($orgs) || isset($type) || isset($participating_orgs) || isset($sector) || isset($country) || isset($region) || isset($provider_orgs) || isset($start_date__lt) || isset($start_date__gt) || isset($end_date__lt) || isset($end_date__gt) ) {
      $api_link .= "?";
      $api_link_parameters = array();
      if (isset($orgs)) {
        $api_link_parameters ["reporting-org"] = implode('|',$orgs);
      }
      if (isset($type)) {
        $api_link_parameters ["reporting-org.type"] = implode('|',$type);
      }
      if (isset($participating_orgs)) {
        $api_link_parameters ["participating-org"] = implode('|',$participating_orgs);
      }
      if (isset($sector)) {
        $api_link_parameters ["sector"] = implode('|',$sector);
      }
      if (isset($provider_orgs)) {
        $api_link_parameters ["transaction_provider-org"] = implode('|',$provider_orgs);
      }
      if (isset($country) && !isset($region)) {
        $api_link_parameters ["recipient-country"] = implode('|',$country);
      }
      if (isset($region) && !isset($country)) {
        $api_link_parameters ["recipient-region"] = implode('|',$region);
      }
      if (isset($start_date__lt)) {
        $api_link_parameters ["start-date__lt"] = implode('|',$start_date__lt);
      }
      if (isset($start_date__gt)) {
        $api_link_parameters ["start-date__gt"] = implode('|',$start_date__gt);
      }
      if (isset($end_date__lt)) {
        $api_link_parameters ["end-date__lt"] = implode('|',$end_date__lt);
      }
      if (isset($end_date__gt)) {
        $api_link_parameters ["end-date__gt"] = implode('|',$end_date__gt);
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
echo $format . "<br/>";
echo $grouping . "<br/>";
echo $requested_size . "<br/>";
echo $org . "<br/>";
echo $type . "<br/>";
echo $sector . "<br/>";
echo $country . "<br/>";
echo $region . "<br/>";
*/

$context = array();

$context['api_link'] = isset($api_link) ? $api_link : null;
$context['notice_message'] = isset($notice_message) ? $notice_message : null;
$context['error_message'] = isset($error_message) ? $error_message : null;

$context['format'] = isset($format) ? $format : null;
$context['grouping'] = isset($grouping) ? $grouping : null;
$context['size'] = isset($size) ? $size : null;

$context['selected_orgs'] = isset($orgs) ? $orgs : null;
$context['selected_provider_orgs'] = isset($provider_orgs) ? $provider_orgs : null;
$context['selected_sectors'] = isset($sector) ? $sector : null;
$context['selected_countries'] = isset($country) ? $country : null;
$context['selected_org_types'] = isset($type) ? $type : null;
$context['selected_participating_orgs'] = isset($participating_orgs) ? $participating_orgs : null;
$context['selected_regions'] = isset($region) ? $region : null;
$context['selected_date'] = isset($date) ? $date : null;

$context['reporting_orgs'] = reporting_orgs();
$context['countries'] = get_countries();
$context['regions'] = get_regions();
$context['org_types'] = get_org_types();
$context['sector_categories'] = get_sector_categories();

$context['start_date__lt'] = isset($start_date__lt) ? $start_date__lt[0] : null;
$context['start_date__gt'] = isset($start_date__gt) ? $start_date__gt[0] : null;
$context['end_date__lt'] = isset($end_date__lt) ? $end_date__lt[0] : null;
$context['end_date__gt'] = isset($end_date__gt) ? $end_date__gt[0] : null;

echo $twig->render('index.html', $context);
?>
