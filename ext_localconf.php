<?php
if (! defined('TYPO3')) {
	die ('Access denied.');
}

call_user_func(function () {

	$extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
		\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
	)->get('be_acl');

	$isRedisEnabled = extension_loaded('redis') && $extensionConfiguration['enableRedis'];

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
		options.saveDocNew.tx_beacl_acl=1
	');

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauthgroup.php']['calcPerms'][] =
		\JBartels\BeAcl\Utility\UserAuthGroup::class .'->calcPerms'
	;
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauthgroup.php']['getPagePermsClause'][] =
		\JBartels\BeAcl\Utility\UserAuthGroup::class .'->getPagePermsClause'
	;

	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Beuser\Controller\PermissionController::class] = [
		'className' => \JBartels\BeAcl\Controller\PermissionController::class,
	];

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
		\JBartels\BeAcl\Hook\DataHandlerHook::class
	;
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
		\JBartels\BeAcl\Hook\DataHandlerHook::class
	;


	// set tx_be_acl_timestamp-cache
	if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_timestamp']['frontend'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_timestamp']['frontend'] =
			'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend'
		;
	}
	if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_timestamp']['backend'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_timestamp']['backend'] = $isRedisEnabled
			? \TYPO3\CMS\Core\Cache\Backend\RedisBackend::class
			: \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class
		;
	}

	// set tx_be_acl_permissions-cache
	if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_permissions']['frontend'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_permissions']['frontend'] =
			'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend'
		;
	}
	if (! isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_permissions']['backend'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_be_acl_permissions']['backend'] = $isRedisEnabled
			? \TYPO3\CMS\Core\Cache\Backend\RedisBackend::class
			: \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class
		;
	}
});
