<?php
/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.3
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 */

/**
 * Utility function to retrieve the list of itemtypes of this module (if any).
 * @uses Xaraya\Modules\Mime\UserApi::getItemTypes()
 * @param array<string, mixed> $args array of optional parameters
 * @return array<mixed> the itemtypes of this module and their description
 */
function mime_userapi_getitemtypes(array $args = [], $context = null)
{
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);
    return $userapi->getItemTypes($args, $context);
}
