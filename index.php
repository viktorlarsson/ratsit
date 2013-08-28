<?php
include_once('ratsit/information.php');
include_once('config.php');
?>

<?php
Ratsit::setPackageSmall1();
Ratsit::asJson();

$result = Ratsit::getInformation('8604214992');

echo $result;

?>