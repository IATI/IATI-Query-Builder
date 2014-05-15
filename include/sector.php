<?php
$selected = "";
$category = "";

//1.04 codelist changes so we need to get both DAC-3 categories
//and DAC-% categories from seperate lists.

//Make an array of categories and their names
$dac_3_categories_data = "codelists/SectorCategory.csv";
if (($handle = fopen($dac_3_categories_data, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); //skip first line
    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
      $dac_3_categories[] = array('id' => $data[0],
                                'name' => $data[1]
                                );
    }
}
//print_r($dac_3_categories); die;

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
         $category = htmlspecialchars($data[4]);
         //echo $category; print_r($data);die;
         //Find the associated name of the category
         foreach ($dac_3_categories as $dac_3_category) {
             if ($category == $dac_3_category['id']) {
                $category_name = htmlspecialchars($dac_3_category['name']);
                $category_name = ucfirst(strtolower($category_name));
                continue;
             }
           }
         echo '<optgroup label="' . $category . ' ' . $category_name . '">';
       }
       //Now check the given sector. If it's not the same category, close the option group and start a new one. 
       //if (substr($data[0],0,3) != $category) {
        if ($data[4] != $category) {
         echo '</optgroup>';
         //$category = substr($data[0],0,3);
         $category = htmlspecialchars($data[4]);
         //Find the associated name of the category
         foreach ($dac_3_categories as $dac_3_category) {
             if ($category == $dac_3_category['id']) {
                $category_name = htmlspecialchars($dac_3_category['name']);
                $category_name = ucfirst(strtolower($category_name));
                continue;
             }
           }
         echo '<optgroup label="' . $category . ' ' . $category_name . '">';
       }
       
        echo '<option value="' . $data[0] . '"' . $selected . '>' . $data[0] . ': ' . $data[1] . '</option>';
    }
    echo '</optgroup>'; //close final option group
    fclose($handle);
}
?>
