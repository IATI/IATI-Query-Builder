<?php
function xml_child_exists($xml, $childpath)
 {
    $result = $xml->xpath($childpath);
    if (count($result)) {
        return true;
    } else {
        return false;
    }
 }
?>
