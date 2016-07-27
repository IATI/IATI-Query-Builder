<?php
$selected = "";
echo '<option value="">- None -</option>';
$country_file = "codelists/Country.csv";
if (($handle = fopen($country_file, "r")) !== FALSE) {
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
        $data[1] = mb_convert_case($data[1], MB_CASE_TITLE, 'UTF-8'); // Convert case based on unicode character properties
        $data[1] = htmlspecialchars($data[1]);
        echo '<option value="' . $data[0] . '"' . $selected . '>' . $data[1] . ': ' . $data[0] . '</option>';
    }
    fclose($handle);
}
?>
