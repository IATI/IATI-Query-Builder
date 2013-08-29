<?php
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
  
  $allowed_orgs = array();
  
  if (isset($_POST["entry_1922375458"])) { //organisations
    $requested_org = filter_var_array($_POST["entry_1922375458"], FILTER_SANITIZE_STRING);
    if (!in_array($requested_org, $allowed_orgs) && !empty($requested_org) ) { //!!!!FIX ME!!!!
      $org = $requested_org;
    }
  }

  //$allowed_types = array();
  if (isset($_POST["entry_18398991"])) { //types
    $requested_type = filter_var_array($_POST["entry_18398991"], FILTER_SANITIZE_STRING);
    $type = build_sanitised_multi_select_values("codelists/OrganisationType.csv",$requested_type);
  }

  //$allowed_sectors = array();
  if (isset($_POST["entry_1954968791"])) { //sectors
    $requested_sector = filter_var_array($_POST["entry_1954968791"], FILTER_SANITIZE_STRING);
    $sector = build_sanitised_multi_select_values("codelists/Sector.csv",$requested_sector);
  }


  if (isset($_POST["entry_605980212"])) { //countries
    $requested_countries = filter_var_array($_POST["entry_605980212"], FILTER_SANITIZE_STRING);
    $country = build_sanitised_multi_select_values("codelists/Country.csv", $requested_countries);
  }

  
  //$allowed_regions = array();
  if (isset($_POST["entry_1179181326"])) { //organisations
    $requested_region = filter_var_array($_POST["entry_1179181326"], FILTER_SANITIZE_STRING);
    $region = build_sanitised_multi_select_values("codelists/Region.csv",$requested_region);
  }
  
  if (isset($region) && isset($country)) {
    unset($region);
    unset($country);
    $notice_message = "** You cannot select both a country and a region **";
  }
  if (isset($dataset) && isset($format) && isset($size)) {
   //&& isset($org) && isset($type) && isset($sector) && (isset($country) || isset($region)) ) {
    $api_link = "http://iati-datastore.herokuapp.com/";
    $api_link .= "api/1/access/";
    $api_link .= $dataset;
    if (isset($format) && $format == "by_sector" || $format == "by_country") {
      $api_link .= "/" . $format;
    }
    $api_link .= ".csv";
   //echo $api_link;
    if (isset($org) || isset($type) || isset($sector) || (isset($country) || isset($region)) ) {
      $api_link .= "?";
      $api_link_parameters = array();
      if (isset($org)) {
        $api_link_parameters ["reporting-org"] = implode('|',$org);
      }
      if (isset($type)) {
        $api_link_parameters ["reporting-org_type"] = implode('|',$type);
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
      if (in_array($requested_value, $allowed_values)) {
        $values[] = $requested_value;
      }
    }
  }
  
  return $values;
}
?>

<html>
  <head>
    <title>IATI Data Store CSV Query Builder</title>
    <link href='style.css' type='text/css' rel='stylesheet'>
  </head>
<body dir="ltr" class="ss-base-body">
  <div itemscope itemtype="http://schema.org/CreativeWork/FormObject">
    <div class="ss-form-container">
      <div class="ss-top-of-page">
        <div class="ss-form-heading">
          <h1 class="ss-form-title" dir="ltr">IATI Data Store CSV Query Builder</h1>
          <p class="guide">Please read the <a href="https://docs.google.com/document/d/1x9S_MX643jfxVY3IA2-wRdiBhLKyDIucrd7DO05BSSA/edit#">User Guide</a></p>
          <hr class="ss-email-break" style="display:none;">
          <?php
            if (isset($api_link)) {
          ?>
          <div class="url">
            <p>Your link:<br/>
              <a href="<?php echo $api_link; ?>"><?php echo htmlspecialchars($api_link); ?></a>
            </p>
            <?php if (isset($notice_message)) { echo $notice_message; } ?>
          </div>
          <?php
           } elseif (isset($error_message)) {
          ?>
          <div class="errorbox-bad">
            <?php echo $error_message; ?>
          </div>
           <?php
           } 
          ?>
          <div class="ss-required-asterisk">*Required</div>
        </div>
      </div>
      <div class="ss-form">
        <form action="index.php" method="POST" id="ss-form" target="_self" onsubmit="">
          <div class="errorbox-good">
            <div dir="ltr" class="ss-item ss-item-required ss-radio">
              <div class="ss-form-entry-top">
                <label class="ss-q-item-label" for="entry_1689841214">
                  <div class="ss-q-title">Choose Dataset
                    <label for="itemView.getDomIdToLabel()" aria-label="(Required field)"></label>
                      <span class="ss-required-asterisk">*</span></div>
                    </label>
                    <ul class="ss-choices">
                      <li class="ss-choice-item">
                        <label>
                          <input type="radio" name="entry.1085079344" value="activity" id="group_1085079344_1" class="ss-q-radio" aria-label="Activity" <?php if (isset($dataset) && $dataset == "activity") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">Activity</span>
                          </label>
                        </li>
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.1085079344" value="transaction" id="group_1085079344_2" class="ss-q-radio" aria-label="Transaction" <?php if (isset($dataset) && $dataset == "transaction") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">Transactions</span>
                          </label>
                        </li>
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.1085079344" value="budgets" id="group_1085079344_3" class="ss-q-radio" aria-label="Budgets" <?php if (isset($dataset) && $dataset == "budgets") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">Budgets</span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div> 
                <div class="errorbox-good">
                  <div dir="ltr" class="ss-item ss-item-required ss-radio">
                    <div class="ss-form-entry-top">
                      <label class="ss-q-item-label" for="entry_1948547450">
                        <div class="ss-q-title">Choose Dataset Format
                          <label for="itemView.getDomIdToLabel()" aria-label="(Required field)"></label>
                          <span class="ss-required-asterisk">*</span>
                        </div>
                      </label>
                      <ul class="ss-choices">
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.71167035" value="summary" id="group_71167035_1" class="ss-q-radio" aria-label="Summary" <?php if (isset($format) && $format == "summary") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">Summary</span>
                          </label>
                        </li>
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.71167035" value="by_sector" id="group_71167035_2" class="ss-q-radio" aria-label="By Sector" <?php if (isset($format) && $format == "by_sector") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">By Sector</span>
                          </label>
                        </li>
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.71167035" value="by_country" id="group_71167035_3" class="ss-q-radio" aria-label="By Country" <?php if (isset($format) && $format == "by_country") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">By Country</span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                <div dir="ltr" class="ss-item ss-item-required ss-radio">
                    <div class="ss-form-entry-top">
                      <label class="ss-q-item-label" for="entry_1414120858">
                        <div class="ss-q-title">Choose Sample Size
                          <label for="itemView.getDomIdToLabel()" aria-label="(Required field)"></label>
                          <span class="ss-required-asterisk">*</span>
                        </div>
                      </label>
                      <ul class="ss-choices">
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.1352830161" value="50 rows" id="group_1352830161_1" class="ss-q-radio" aria-label="50 rows" <?php if (isset($size) && $size == "50 rows") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">50 rows</span>
                          </label>
                        </li>
                        <li class="ss-choice-item">
                          <label>
                            <input type="radio" name="entry.1352830161" value="Entire selection" id="group_1352830161_2" class="ss-q-radio" aria-label="Entire selection" <?php if (isset($size) && $size == "stream=True") { echo 'checked="checked"'; } ?>>
                            <span class="ss-choice-label">Entire selection</span>
                          </label>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                  <div dir="ltr" class="ss-item  ss-text">
                    <div class="ss-form-entry">
                      <label class="ss-q-item-label" for="entry_1922375458">
                        <div class="ss-q-title">Select Reporting Organisation (eg UK DFID = GB-1)</div>
                        <div class="ss-q-help ss-secondary-text" dir="ltr"></div>
                      </label>
                      <select multiple name="entry.1922375458[]" value="" class="ss-q-short" id="entry_1922375458[]">
                        <?php include("include/reporting_org.php"); ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                  <div dir="ltr" class="ss-item  ss-text">
                    <div class="ss-form-entry">
                      <label class="ss-q-item-label" for="entry_18398991">
                        <div class="ss-q-title">Select Type of Reporting Organisation (eg. INGO = 21)</div>
                      </label>
                      <select multiple name="entry.18398991[]" value="" class="ss-q-short" id="entry_18398991[]">
                        <?php include("include/reporting_org_type.php"); ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                <div dir="ltr" class="ss-item  ss-text">
                  <div class="ss-form-entry">
                    <label class="ss-q-item-label" for="entry_1954968791">
                      <div class="ss-q-title">Select Sector (eg Basic Health Care = 12220)</div>
                    </label>
                      <select multiple name="entry.1954968791[]" value="" size="10" class="ss-q-short" id="entry_1954968791[]">
                        <?php include("include/sector.php"); ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                  <div dir="ltr" class="ss-item  ss-text">
                    <div class="ss-form-entry">
                      <label class="ss-q-item-label" for="entry_605980212">
                        <div class="ss-q-title">Select Country (eg DRC = CD)</div>
                      </label>
                      <select multiple name="entry.605980212[]" value="" class="ss-q-short" id="entry_605980212[]">
                        <?php include("include/country.php"); ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="errorbox-good">
                  <div dir="ltr" class="ss-item  ss-text">
                    <div class="ss-form-entry">
                      <label class="ss-q-item-label" for="entry_1179181326">
                        <div class="ss-q-title">Select Region (eg South Asia = 679)</div>
                      </label>

                      <select multiple name="entry.1179181326[]" value="" class="ss-q-short" id="entry_1179181326[]" >
                        <?php include("include/region.php"); ?>
                      </select>
                      </div>
                    </div>
                  </div>
                  <!--<input type="hidden" name="draftResponse" value="[]">
                  <input type="hidden" name="pageHistory" value="0">-->


                  <div class="ss-item ss-navigate">
                    <div class="ss-form-entry">
                      <input type="submit" name="submit" value="Submit" id="ss-submit">
                    </div>
                    <div class="ss-form-entry">
                      <input type="submit" name="reset" value="Reset" id="reset">
                    </div>
                  </div>
                </form>
              </div>
              <div class="ss-footer">
                <div class="ss-attribution"></div>
                <div class="ss-legal">
                  <div class="disclaimer-separator"></div>
                  <div class="disclaimer">
                    <div class="powered-by-logo"></div>
                    <div class="ss-terms"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </body>
      </html>
