<html>
 <head>
  <title>SAS Locker Proof of Concept</title>
 </head>
 <body>
<?php
require_once 'vendor/autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;

$connectionString = 'DefaultEndpointsProtocol=https;AccountName=' . getenv("STORAGE_ACCOUNT") . ';AccountKey=' . getenv("STORAGE_KEY") ;


function getSASForBlob($accountName,$container, $blob, $resourceType, $permissions, $expiry,$key)
 {
 
 /* Create the signature */
 $_arraysign = array();
 $_arraysign[] = $permissions;
 $_arraysign[] = '';
 $_arraysign[] = $expiry;
 $_arraysign[] = '/' . $accountName . '/' . $container . '/' . $blob;
 $_arraysign[] = '';
 $_arraysign[] = "2014-02-14"; //the API version is now required
 $_arraysign[] = '';
 $_arraysign[] = '';
 $_arraysign[] = '';
 $_arraysign[] = '';
 $_arraysign[] = '';
 
 $_str2sign = implode("\n", $_arraysign);
 
 return base64_encode(
 hash_hmac('sha256', urldecode(utf8_encode($_str2sign)), base64_decode($key), true)
 );
 }
 
 function getBlobUrl($accountName,$container,$blob,$resourceType,$permissions,$expiry,$_signature)
 {
 /* Create the signed query part */
 $_parts = array();
 $_parts[] = (!empty($expiry))?'se=' . urlencode($expiry):'';
 $_parts[] = 'sr=' . $resourceType;
 $_parts[] = (!empty($permissions))?'sp=' . $permissions:'';
 $_parts[] = 'sig=' . urlencode($_signature);
 $_parts[] = 'sv=2014-02-14';
 $_parts[] = 'spr=https';


 /* Create the signed blob URL */
 $_url = 'https://'
 .$accountName.'.blob.core.windows.net/'
 . $container . '/'
 . $blob . '?'
 . implode('&', $_parts);
 
 //  $getBlobResult = $blobClient->getBlob($container, $blob);
 
 return $_url;
 }
 
// Set time to UTC to match storage account time
date_default_timezone_set('UTC');

// UTC Format required for SAS, https://docs.microsoft.com/en-us/azure/storage/storage-dotnet-shared-access-signature-part-1
$datemask = "Y-m-d\TH:i:s\Z";

// add time to today for Expiry UTC time
$se=Date($datemask , strtotime("+300 seconds"));
 
$exp = $se;
$act = getenv("STORAGE_ACCOUNT");
$cont = 'secure';
$blob = "myblob";
$key = getenv("STORAGE_KEY");
 
$_signature = getSASForBlob($act,$cont,$blob,'b','r',$exp,$key);
$_blobUrl = getBlobUrl($act,$cont,$blob,'b','r',$exp,$_signature);


 ?>
 <?php echo '<br /><a href=' . $_blobUrl . '>' . $_blobUrl . '</a>'; ?> 

 </body>
</html>