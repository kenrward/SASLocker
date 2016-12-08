<?php
// UTC Format required for SAS, https://docs.microsoft.com/en-us/azure/storage/storage-dotnet-shared-access-signature-part-1
$datemask = "Y-d-m\TH:i:s\Z";

// add 60 seconds to today for Expiry UTC time
$today=Date($datemask , strtotime("+60 seconds"));
$se = rtrim($today, "+00:00");
$se = urlencode($se);

echo $se;

?>