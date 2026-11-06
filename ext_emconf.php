<?php

$EM_CONF['be_acl'] = [
	'title' => 'Backend ACLs',
	'description' => 'Backend Access Control Lists',
	'category' => 'be',
	'state' => 'stable',
	'version' => '5.0.0',
	'clearcacheonload' => false,
	'author' => 'Sebastian Kurfuerst, Jan Bartels, Moritz Ngo',
	'author_email' => 'sebastian@garbage-group.de, j.bartels@arcor.de, moritz.ngo@p2media.de',
	'author_company' => '',
	'constraints' => [
		'depends' => [
			'typo3' => '14.3.0-14.3.99',
			'beuser' => '14.3.0-14.3.99',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
	'autoload' => [
		'psr-4' => [
		  	'JBartels\\BeAcl\\' => 'Classes/',
			'P2Media\\BeAcl\\' => 'Classes/',
		]
	],
];
