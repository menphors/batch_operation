<?php 
ini_set('error_reporting',E_ALL^E_NOTICE); 

	//$serial = '200284825681'; //$_GET['serial'];	
	$serial = $_GET['serial'];	
	$i=0;
	
	//echo $c_dlog;
// Connect Database
$conn = oci_connect("smart", "Smart_%002", "10.12.5.191:1526/suseora");
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}else{
}

// Call procedur

$sql="select ',' || pri_identity || ',' || trans_id || ',' as batch from arsh_1601.ar_his_batch_payment@edrdb1
where batch_no= :eid ";

$stid = oci_parse($conn,$sql);

// Bind input value

oci_bind_by_name($stid,':eid', $serial);
//echo $sql;
oci_execute($stid);

?>


<div class="line" id="line"></div>
<table width='50px' border="1" style="border:1px solid #000033;border-collapse:collapse;">
 <tr align="center" bgcolor="#999999">
     <td width="10%" style="color:#000033;border:1px solid #000033;"><strong>BATCH No</strong></td>

  </tr>

 <?php
 $data = array();
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
   $data[] = $row['BATCH'];
   echo "<tr>\n";
   echo "<td>" . $row['BATCH'] . "</td>\n";
   echo "</tr>\n";
}
?> 
  
 
 <tr align="center" >
   <td style="color:#000033;border:1px solid #000033;"><?php echo $o_an;?></td>
   </tr>

<head>
<a href="downloadbatch.php?serial=<?php echo $serial; ?>">Download Files</a>
</body>
</head>
</html>
<?php

	

oci_free_statement($stid);
oci_close($conn);
?>  
