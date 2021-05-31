<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

namespace Trilobit\PackagistinfoBundle\Model;

use Contao\Model;

class PackagistinfoModel extends Model
{
    /**
     * $strTable.
     */
    protected static $strTable = 'tl_packagistinfo';

    public static function findFirst($data)
    {
        $tstamp = $data->first()->tstamp;

        $data->reset();

        return $tstamp;
    }

    public static function findLast($data)
    {
        $tstamp = $data->last()->tstamp;

        $data->reset();

        return $tstamp;
    }
}
