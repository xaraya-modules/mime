<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */

use Xaraya\Modules\Mime\AdminGui;

/**
 * View items of the mime objects
 * @uses AdminGui::view()
 * @param array<string, mixed> $args
 * @param mixed $context
 * @return mixed template variables or output in HTML
 */
function mime_admin_view(array $args = [], $context = null)
{
    /** @var AdminGui $admingui */
    $admingui = xarMod::getModule('mime')->getAdminGUI();
    $admingui->setContext($context);
    return $admingui->view($args);
}
