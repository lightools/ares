## Introduction

This library provides simple interface for finding basic information about Czech companies registered in ARES database.

## Installation

```sh
$ composer require lightools/ares
```

## Simple usage

```php
$client = new Bitbang\Http\Clients\CurlClient(); // you will probably need to setup CURLOPT_CAINFO or CURLOPT_SSL_VERIFYPEER
$loader = new Lightools\Xml\XmlLoader();
$finder = new Lightools\Ares\CompanyFinder($client, $loader);

try {
    $company = $finder->find('66872944');

    if ($company === NULL) {
        // not found
    } else {
        echo $company->getVatNumber();
    }

} catch (Lightools\Ares\LookupFailedException $e) {
    // process exception
}
```

## How to run tests

```sh
$ vendor/bin/tester tests
```
