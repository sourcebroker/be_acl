<?php
namespace JBartels\BeAcl\ViewHelpers;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Beuser\Exception;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get a value from an array by given key.
 */
class ArrayElementViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('array', 'array', 'Array to search in', true);
        $this->registerArgument('key', 'string', 'Key to return its value', true);
        $this->registerArgument('subKey', 'string', 'If result of key access is an array, subkey can be used to fetch an element from this again', false, '');
    }

    /**
     * Return array element by key.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @throws Exception
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        $array = $arguments['array'];
        $key = $arguments['key'];
        $subKey = $arguments['subKey'];
        $result = '';

        if (is_array($array)) {
            $result = static::getValue($array, $key);
            if (is_array($result) && $subKey) {
                $result = static::getValue($result, $subKey);
            }
        }

        if (!is_scalar($result) && !is_null($result)) {
            throw new Exception(
                'Only scalar or null return values (string, int, float or double, null) are supported.',
                1382284105
            );
        }
        return (string)$result;
    }

    protected static function getValue($array, $key, $del = '.', $default = null)
    {
        try {
            $result = ArrayUtility::getValueByPath($array, (string)$key, '.');
        } catch (\RuntimeException $ex) {
            $result = $default;
        }
        return $result;
    }
}
