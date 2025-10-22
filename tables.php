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
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

namespace Xaraya\Modules\Mime;

class Tables
{
    /**
     * Return mime table names to xaraya
     *
     * This function is called internally by the core whenever the module is
     * loaded.  It is loaded by xarMod::loadDbInfo().
     *
     * @access private
     * @return array
     */
    public function __invoke(string $prefix = 'xar')
    {
        // Initialise table array
        $xartable = [];

        $mime_type      = $prefix . '_mime_type';
        $mime_subtype   = $prefix . '_mime_subtype';
        $mime_extension = $prefix . '_mime_extension';
        $mime_magic     = $prefix . '_mime_magic';

        // Set the table name
        $xartable['mime_type']      = $mime_type;
        $xartable['mime_subtype']   = $mime_subtype;
        $xartable['mime_extension'] = $mime_extension;
        $xartable['mime_magic']     = $mime_magic;

        // Return the table information
        return $xartable;
    }
}
