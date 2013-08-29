<?php
$selected = "";
echo '<option value="">- None -</option>';
$reporting_org_type_file = "codelists/Country.csv";
if (($handle = fopen($reporting_org_type_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); //skip first line
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
       if (isset($country)) {
         if (in_array($data[0],$country)) { //remember posted variables may be a multi-array
           $selected = 'selected="selected"';
         } else {
           $selected = "";
         }
       }
        $data[0] = htmlspecialchars($data[0]);
        $data[1] = ucwords(strtolower($data[1]));
        $data[1] = htmlspecialchars($data[1]);
        
        echo '<option value="' . $data[0] . '"' . $selected . '>' . $data[1] . ': ' . $data[0] . '</option>';
    }
    fclose($handle);
}
?>
