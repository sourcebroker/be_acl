<?php

$EM_CONF['be_acl'] = [
	'title' => 'Backend ACLs',
	'description' => 'Backend Access Control Lists',
	'category' => 'be',
	'version' => '2.0.2-dev',
	'state' => 'stable',
	'clearcacheonload' => false,
	'author' => 'Sebastian Kurfuerst, Jan Bartels, Moritz Ngo',
	'author_email' => 'sebastian@garbage-group.de, j.bartels@arcor.de, moritz.ngo@p2media.de',
	'author_company' => '',
	'constraints' => [
		'depends' => [
			'typo3' => '12.4.0-12.4.99',
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
