<?php
$selected = "";
$category = "";
echo '<option value="">- None -</option>';
$reporting_org_type_file = "codelists/Sector.csv";
if (($handle = fopen($reporting_org_type_file, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); //skip first line
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
      if (isset($sector)) {
         if (in_array($data[0],$sector)) { //remember posted variables may be a multi-array
           $selected = 'selected="selected"';
         } else {
           $selected = "";
         }
       }
       //Put options into categories
       if ($category == "") { //First run through - categories not set, so set it as the first value found
         //$category = substr($data[0],0,3);
         $category = htmlspecialchars($data[3]);
         $category_name = htmlspecialchars($data[4]);
         $category_name = ucfirst(strtolower($data[4]));
         echo '<optgroup label="' . $category . ' ' . $category_name . '">';
       }
       //Now check the given sector. If it's not the same category, close the option group and start a new one. 
       //if (substr($data[0],0,3) != $category) {
        if ($data[3] != $category) {
         echo '</optgroup>';
         //$category = substr($data[0],0,3);
         $category = htmlspecialchars($data[3]);
         $category_name = htmlspecialchars($data[4]);
         $category_name = ucfirst(strtolower($data[4]));
         echo '<optgroup label="' . $category . ' ' . $category_name . '">';
       }
       
        echo '<option value="' . $data[0] . '"' . $selected . '>' . $data[0] . ': ' . $data[1] . '</option>';
    }
    echo '</optgroup>'; //close final option group
    fclose($handle);
}
?>
