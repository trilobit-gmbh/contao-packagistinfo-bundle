<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

namespace Trilobit\PackagistinfoBundle\DataContainer;

use Trilobit\PackagistinfoBundle\Controller\PackagistinfoController;

/**
 * Class Edit.
 */
class Edit
{
    /**
     * @return array
     */
    public static function onGroupOptions()
    {
        $result = (new PackagistinfoController())->getPackages();

        return self::generateOptions($result);
    }

    /**
     * @param $result
     * @param string $value
     * @param string $label
     *
     * @return array
     */
    protected static function generateOptions($result, $value = 'id', $label = 'title')
    {
        $helper = [];
        $options = [];

        // build tree
        foreach ($result as $item) {
            $pid = (int) $item['pid'];
            $id = (int) $item['id'];
            $sorting = (int) $item['sorting'];

            if (0 === $pid) {
                $helper[$id][0] = $item;
            } else {
                $helper[$pid][$sorting] = $item;
            }
        }

        // sort tree subitems
        foreach (array_keys($helper) as $pid) {
            ksort($helper[$pid]);
        }

        // prepare options
        foreach (array_values($helper) as $item) {
            $key = $item[0][$label];

            if (1 === \count($item)) {
                $options[$item[0][$value]] = $key;
                continue;
            }

            array_shift($item);

            foreach ($item as $option) {
                $options[$key][$option[$value]] = $option[$label];
            }
        }

        return $options;
    }
}
