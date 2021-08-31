<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mailbyzip".
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Powermail by ZIP',
	'description' => 'Send email to receivers in dependence of the entered ZIP code.',
	'category' => 'plugin',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 1,
	'author' => 'Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'jambage.com',
	'version' => '0.1.1',
	'constraints' => array(
		'depends' => array(
			'php' => '5.5.0-7.99.99',
			'typo3' => '9.5.0-9.5.99',
			'powermail' => '7.4.0-0.0.0',
		),
	),  
);
