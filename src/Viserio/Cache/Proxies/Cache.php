<?php
namespace Viserio\Cache\Proxies;

/**
 * Narrowspark - a PHP 5 framework.
 *
 * @author      Daniel Bannert <info@anolilab.de>
 * @copyright   2015 Daniel Bannert
 *
 * @link        http://www.narrowspark.de
 *
 * @license     http://www.narrowspark.com/license
 *
 * @version     0.10.0-dev
 */

use Viserio\Support\StaticalProxyManager;

/**
 * Cache.
 *
 * @author  Daniel Bannert
 *
 * @since   0.9.1-dev
 */
class Cache extends StaticalProxyManager
{
    protected static function getFacadeAccessor()
    {
        return 'cache';
    }
}