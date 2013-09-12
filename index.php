<?php
include_once('ratsit/information.php');
include_once('ratsit/config.php');

$number = $_REQUEST['number'];
?>

<?php
Ratsit::setPackageSmall1();
Ratsit::asJson();

$result = Ratsit::getInformation($number);

echo $result;

?>