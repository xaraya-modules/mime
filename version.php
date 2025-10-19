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

namespace Xaraya\Modules\Mime;

class Version
{
    /**
     * Get module version information
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'name' => 'mime',
            'id' => '999',
            'version' => '2.6.0',
            'displayname' => 'Mime',
            'description' => 'Hook based module that returns the content-type of a given file.',
            'credits' => 'xardocs/credits.txt',
            'help' => 'xardocs/help.txt',
            'changelog' => 'xardocs/changelog.txt',
            'license' => 'xardocs/license.txt',
            'official' => true,
            'author' => 'Carl P. Corliss <carl.corliss@xaraya.com>',
            'contact' => 'http://www.xaraya.com/',
            'admin' => true,
            'user' => false,
            'class' => 'Utility',
            'category' => 'Content',
            'securityschema'
             => [
             ],
            'namespace' => 'Xaraya\\Modules\\Mime',
            'twigtemplates' => true,
            'dependencyinfo'
             => [
                 0
                  => [
                      'name' => 'Xaraya Core',
                      'version_ge' => '2.4.1',
                  ],
             ],
        ];
    }
}
