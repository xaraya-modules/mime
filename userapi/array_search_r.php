<?php

/**
 * @package modules\mime
 * @category Xaraya Web Applications Framework
 * @version 2.5.7
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link https://github.com/mikespub/xaraya-modules
**/

namespace Xaraya\Modules\Mime\UserApi;


use Xaraya\Modules\Mime\UserApi;
use Xaraya\Modules\MethodClass;
use sys;

sys::import('xaraya.modules.method');

/**
 * mime userapi array_search_r function
 * @extends MethodClass<UserApi>
 */
class ArraySearchRMethod extends MethodClass
{
    /** functions imported by bermuda_cleanup */

    /**
     * Search an array recursivly
     * This function will search an array recursivly  till it finds what it is looking for. An array
     * within an array within an array within array is all good. It returns an array containing the
     * index names from the outermost index to the innermost, all the way up to the needle, or FALSE
     * if the needle was not found, example:
     *
     *      $foo['bar']['some']['indice'] = 'something';
     *      $indice = array_search_r('something', $foo);
     *
     * this would yield an array like so:
     *      $indice = array(0 => 'bar', 1 => 'some', 2 => 'indice'),
     *
     * which could then be used to reconstruct the location like so:
     *      for ($i = 0; $i < count($indice); $i++) {
     *          if (!$i) $var = '$foo';
     *          $var .= "[{$indice[$i]}]";
     *      }
     *  then you could access it like so:
     *      $$var = 'something else';
     * @author Richard Sumilang      <richard@richard-sumilang.com> (original author)
     * @author Carl P. Corliss <carl.corliss@xaraya.com>
     * @param array<mixed> $args
     * @var string $needle What are you searching for?
     * @var array $haystack What you want to search in
     * @return array|false array of keys or FALSE if not found.
     * @access public
     * @see UserApi::arraySearchR()
     */
    public function __invoke(array $args = [])
    {
        extract($args);

        static $indent = 0;
        static $match = false;

        if (!isset($needle) || (!isset($haystack) || !is_array($haystack))) {
            $indent--;
            return false;
        }

        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $indent++;
                $match = $this->__invoke(['needle' => $needle, 'haystack' => $value]);
            } else {
                if ($value === $needle) {
                    $match[$indent] = $value;
                } else {
                    $match = false;
                }
            }
            if ($match) {
                $match[$indent] = $key;
                break;
            }
        }
        $indent--;

        if ($indent <= 0) {
            if (is_array($match)) {
                array_reverse($match);
            }
        }
        return $match;
    }
}
