<?php

// require_once './vendor/autoload.php';
require_once 'Attribute.php';
require_once 'Entity.php';
require_once 'Value.php';
require_once 'Database.php';

define( "DBHOST", "" );
define( "DBUSER", "" );
define( "DBPASS", "" );
define( "DBNAME", "" );

use EAV\Attribute;
use EAV\Entity;
use EAV\Value;

$colorAttribute = new Attribute('color');
$colorSilver = new Value($colorAttribute, 'silver');
$colorBlack = new Value($colorAttribute, 'black');

$memoryAttribute = new Attribute('memory');
$memory8Gb = new Value($memoryAttribute, '8GB');

$activeAttribute = new Attribute("active");
$objValue = new Value( $activeAttribute, 0);

// $objValue->setEntityId( 1 )->setAttributeId( 6 )->save( $activeAttribute );
// die;

$entity = new Entity();
$entity->addAttribute( $colorAttribute );
$entity->addAttribute( $memoryAttribute );
$entity->addAttribute( $activeAttribute );
$entity->setId(1)->setRelType( "schedule" )->setRelId( 1 )->save();

echo '<pre>';
echo '<br>';
print_r( $entity );
echo '<br>';
echo '<br>';
echo (string) $entity;
echo '<br>';
echo '<br>';
echo '</pre>';

die;
        