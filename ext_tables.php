<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addService($_EXTKEY,  '' /* sv type */,  'tx_filesearch_sv1' /* sv key */,
		array(

			'title' => 'Find',
			'description' => '',

			'subtype' => '',

			'available' => TRUE,
			'priority' => 100,
			'quality' => 50,

			'os' => 'unix',
			'exec' => 'find',

			'classFile' => t3lib_extMgm::extPath($_EXTKEY).'sv1/class.tx_filesearch_sv1.php',
			'className' => 'tx_filesearch_sv1',
		)
	);


if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::addModule('txfilesearchM0','','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	t3lib_extMgm::addModule('txfilesearchM0','txfilesearchM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}
?>