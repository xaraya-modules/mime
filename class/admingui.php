<?php

/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\Mime;

use Xaraya\Modules\AdminGuiInterface;
use Xaraya\Modules\AdminGuiTrait;
use sys;

sys::import('modules.dynamicdata.class.objects.factory');
sys::import('modules.mime.class.userapi');
sys::import('xaraya.modules.adminguitrait');

/**
 * Class instance to handle the Mime Admin GUI
**/
class AdminGui implements AdminGuiInterface
{
    use AdminGuiTrait;

    /**
     * Summary of main
     * @param array<string, mixed> $args
     * @return array<mixed>
     */
    public function main(array $args = [])
    {
        // Pass along the context for xarTpl::module() if needed
        $args['context'] ??= $this->getContext();
        return $args;
    }
}
