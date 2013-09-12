Ratsit PHP Class
==============

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
Request URL:http://www.kontentan.se/ratsit/?number=556709-0526
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


Contact
==============
Code is maintained by We made you look (http://wemadeyoulook.at)

API is provided by Ratsit

Ratsit AB, Säljavdelningen API
Fredrik Tengström
fredrik.tengstrom@ratsit.se
031-67 38 12

