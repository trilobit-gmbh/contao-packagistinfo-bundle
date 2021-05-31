<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

use Trilobit\PackagistinfoBundle\Cron\PackagistinfoCron;
use Trilobit\PackagistinfoBundle\Model\PackagistinfoModel;
use Trilobit\PackagistinfoBundle\Module\PackagistinfoCharts;
use Trilobit\PackagistinfoBundle\Module\PackagistinfoTable;

$GLOBALS['BE_MOD']['trilobit']['tl_packagistinfo'] = [
    'tables' => ['tl_packagistinfo'],
];

$GLOBALS['FE_MOD']['application']['packagistinfocharts'] = PackagistinfoCharts::class;
$GLOBALS['FE_MOD']['application']['packagistinfotable'] = PackagistinfoTable::class;

$GLOBALS['TL_MODELS']['tl_packagistinfo'] = PackagistinfoModel::class;

$GLOBALS['TL_CRON']['minutely'][] = [
    PackagistinfoCron::class,
    '__invoke',
];

/*
$GLOBALS['TL_CRON']['minutely'][] = [
    PackagistinfoController::class,
    'import',
];
*/
