<?php
$selected = "";
echo '<option value="">- None -</option>';
$reporting_org_type_file = "codelists/Sector.csv";
if (($handle = fopen($reporting_org_type_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); //skip first line
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      if (isset($sector)) {
         if ($sector == $data[0]) {
           $selected = 'selected="selected"';
         } else {
           $selected = "";
         }
       }
        echo '<option value="' . $data[0] . '"' . $selected . '>' . $data[0] . ': ' . $data[1] . '</option>';
    }
    fclose($handle);
}
?>
