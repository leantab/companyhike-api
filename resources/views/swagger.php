<?php
require(base_path("/vendor/autoload.php"));
$openapi = \OpenApi\Generator::scan([base_path('app/Http/Controllers')]);
header('Content-Type: application/json');
header('Accpet: application/json');
echo '<pre>';
echo $openapi->toJSON();
