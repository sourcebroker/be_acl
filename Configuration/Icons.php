<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'tx_beacl-object-info' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:be_acl/Resources/Public/Icons/info-solid.svg',
    ],
];
