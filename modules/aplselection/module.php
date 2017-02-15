<?php

$Module = array( 'name' => 'aplsection', 'variable_params' => true );

$ViewList = array();

$ViewList[ 'getSelection' ] = array(
								'functions' => array( 'generateselection' ),
								'default_navigation_part' => 'ezfindnavigationpart',
								'script' => 'getSelection.php',
								'params' => array()
                            );



$FunctionList = array();
$FunctionList[ 'generateselection' ] = array();
?>