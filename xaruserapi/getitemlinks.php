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
 * utility function to pass individual item links to whoever
 * @uses Xaraya\Modules\Mime\UserApi::getItemLinks()
 * @param array<string, mixed> $args array of optional parameters
 *        string   $args['itemtype'] item type (optional)
 *        array    $args['itemids'] array of item ids to get
 * @return array<mixed> containing the itemlink(s) for the item(s).
 */
function mime_userapi_getitemlinks(array $args = [], $context = null)
{
    $userapi = xarMod::getAPI('mime');
    $userapi->setContext($context);
    return $userapi->getItemLinks($args, $context);
}
