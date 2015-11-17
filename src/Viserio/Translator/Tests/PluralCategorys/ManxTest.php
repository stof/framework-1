<?php
namespace Viserio\Test\Translator\PluralCategorys;

/*
 * Narrowspark - a PHP 5 framework
 *
 * @author      Daniel Bannert <info@anolilab.de>
 * @copyright   2015 Daniel Bannert
 * @link        http://www.narrowspark.de
 * @license     http://www.narrowspark.com/license
 * @version     0.9.6-dev
 * @package     Narrowspark/framework
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

/**
 * ManxTest.
 *
 * @author  Daniel Bannert
 *
 * @since   0.9.6-dev
 */
class ManxTest extends AbstractPluralCategorysTest
{
    public function category()
    {
        return [
            [0, 'one'],
            ['0', 'one'],
            [0.0, 'one'],
            ['0.0', 'one'],
            [1, 'one'],
            [2, 'one'],
            [11, 'one'],
            [12, 'one'],
            [20, 'one'],
            [21, 'one'],
            [22, 'one'],
            [3, 'other'],
            [4, 'other'],
            [5, 'other'],
            [6, 'other'],
            [7, 'other'],
            [8, 'other'],
            [9, 'other'],
            [10, 'other'],
            [13, 'other'],
            [14, 'other'],
            [15, 'other'],
            [16, 'other'],
            [17, 'other'],
            [18, 'other'],
            [19, 'other'],
            [23, 'other'],
            [25, 'other'],
            [29, 'other'],
            [30, 'other'],
            [0.31, 'other'],
            [1.2, 'other'],
            [2.07, 'other'],
            [3.31, 'other'],
            [11.31, 'other'],
            [21.11, 'other'],
            [100.31, 'other'],
        ];
    }
}