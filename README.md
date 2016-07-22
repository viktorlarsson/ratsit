Ratsit PHP Class
==============

This package is old and deprecated, please use https://github.com/pontusab/Ratsit instead.

[![deprecated](http://badges.github.io/stability-badges/dist/deprecated.svg)](http://github.com/badges/stability-badges)

A PHP class for communicating with Ratsit API.

http://www.ratsit.se/Content/API_Webservice.aspx

Supports
==============
* GetCompanyInformationPackage
* GetPersonInformationPackage

Example code

```php
<?php
include_once('ratsit/information.php');
include_once('ratsit/config.php');

$number = $_REQUEST['number'];
?>

<?php
// Only the basic package
Ratsit::setPackageSmall1();
Ratsit::asJson();

$result = Ratsit::getInformation($number);

echo $result;

?>
```

Request and response
==============

```text
Request URL:localhost/ratsit/?number=556709-0526
Request Method:GET
Status Code:200 OK
```

```javascript
{
	"companyName": "Ratsit AB",
	"Street":"DATAV\u00c4GEN 12 A",
	"ZipCode":"43632",
	"City":"ASKIM"
}
```

Requirements
==============
* cURL

Roadmap
==============
* GetAnnualReport
* GetCompanies
* GetCompanyInformationPackage_NOLetterOfNotice
* GetPersonInformationPackage_NOLetterOfNotice
