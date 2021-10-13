<?php
// phpinfo();
// die;
 
$conn = oci_connect('smart', 'Smart_%002', '10.12.5.191:1526/suseora');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
 
$stid = oci_parse($conn, 'select a.res_code from inventory.res_sim a where rownum <=10');
oci_execute($stid);
 
echo "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";
 
?>