<?php

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

namespace Trilobit\PackagistinfoBundle\Module;

use Contao\BackendTemplate;
use Contao\Date;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Patchwork\Utf8;
use Trilobit\PackagistinfoBundle\Controller\PackagistinfoController;

class PackagistinfoTable extends Module
{
    protected $strTemplate = 'mod_packagistinfotable';

    /**
     * @var mixed|PackagistinfoController
     */
    private $packagist;

    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['packagistinfotable'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        if (null === ($this->packages = StringUtil::deserialize($this->packagistbundles))) {
            return '';
        }

        $this->packages = array_map((function ($item) { return (int) $item; }), $this->packages);
        $this->packagist = new PackagistinfoController();

        return parent::generate();
    }

    protected function compile()
    {
        $timeline = $this->packagist->getTimeline($this->packages);
        $packages = $this->packagist->getPackages($this->packages);

        $this->Template->url = $this->packagist->getPackagistUrl();
        $this->Template->packages = $packages;

        $thead = [''];
        $tfoot = [];
        $tbody = [];

        foreach ($timeline as $tstamp) {
            $thead[] = [
                'label' => Date::parse($this->packagist->getLabelFormat(), $tstamp),
                'shortLabel' => Date::parse($this->packagist->getShortLabelFormat(), $tstamp),
                'tstamp' => $tstamp,
            ];
        }
        $tfoot = $thead;

        $n = 0;
        foreach ($packages as $key => $value) {
            $tbody[$key][] = $key;

            foreach ($timeline as $tstamp) {
                $tbody[$key][$tstamp] = [
                    'count' => [
                        'downloads' => null,
                        'favers' => null,
                    ],
                    'status' => [
                        'downloads' => '',
                        'favers' => '',
                    ],
                ];
            }

            $max[$key] = [
                'downloads' => [],
                'favers' => [],
            ];

            $min[$key] = [
                'downloads' => [],
                'favers' => [],
            ];

            $last = [
                'downloads' => null,
                'favers' => null,
            ];

            $data = $this->packagist->getPackageItems($value['id']);

            foreach ($data as $count) {
                $tbody[$key][$count['check']] = [
                    'count' => [
                        'downloads' => $count['downloads'],
                        'favers' => $count['favers'],
                    ],
                    'status' => [
                        'downloads' => (empty($last['downloads']) ? '' : ($last['downloads'] === $count['downloads'] ? 0 : ($last['downloads'] < $count['downloads'] ? 1 : -1))),
                        'favers' => (empty($last['favers']) ? '' : ($last['favers'] === $count['favers'] ? 0 : ($last['favers'] < $count['favers'] ? 1 : -1))),
                    ],
                    'id' => $key,
                ];

                $min[$key] = [
                    'downloads' => ($count['downloads'] < $min[$key]['downloads'] || empty($min[$key]['downloads'])) ? $count['downloads'] : $min[$key]['downloads'],
                    'favers' => ($count['favers'] < $min[$key]['favers'] || empty($min[$key]['favers'])) ? $count['favers'] : $min[$key]['favers'],
                ];

                $max[$key] = [
                    'downloads' => ($count['downloads'] > $max[$key]['downloads'] || empty($max[$key]['downloads'])) ? $count['downloads'] : $max[$key]['downloads'],
                    'favers' => ($count['favers'] > $max[$key]['favers'] || empty($max[$key]['favers'])) ? $count['favers'] : $max[$key]['favers'],
                ];

                $last = [
                    'downloads' => $count['downloads'],
                    'favers' => $count['favers'],
                ];

                if ($count['downloads'] < $min['downloads']['count'] || empty($min['downloads'])) {
                    $min['downloads'] = [
                        'package' => $key,
                        'count' => $count['downloads'],
                    ];
                }

                if ($count['favers'] < $min['favers']['count'] || empty($min['favers'])) {
                    $min['favers'] = [
                        'package' => $key,
                        'count' => $count['favers'],
                    ];
                }

                if ($count['downloads'] > $max['downloads']['count'] || empty($min['downloads'])) {
                    $max['downloads'] = [
                        'package' => $key,
                        'count' => $count['downloads'],
                    ];
                }

                if ($count['favers'] > $max['favers']['count'] || empty($min['downloads'])) {
                    $max['favers'] = [
                        'package' => $key,
                        'count' => $count['favers'],
                    ];
                }
            }

            ++$n;
        }

        $tstampStart = $this->packagist->getFirstDate($timeline);
        $tstampEnd = $this->packagist->getLastDate($timeline);

        $this->Template->types = explode('-', $this->packagistdatatype);

        $this->Template->thead = $thead;
        $this->Template->tfoot = $tfoot;
        $this->Template->tbody = $tbody;
        $this->Template->caption = $this->packagistsummary;
        $this->Template->min = $min;
        $this->Template->max = $max;

        $this->Template->timeline = [
            'datim' => [
                'from' => Date::parse($this->packagist->getDatimFormat(), $tstampStart),
                'to' => Date::parse($this->packagist->getDatimFormat(), $tstampEnd),
            ],
            'date' => [
                'from' => Date::parse($this->packagist->getDateFormat(), $tstampStart),
                'to' => Date::parse($this->packagist->getDateFormat(), $tstampEnd),
            ],
            'time' => [
                'from' => Date::parse($this->packagist->getTimeFormat(), $tstampStart),
                'to' => Date::parse($this->packagist->getTimeFormat(), $tstampEnd),
            ],
            'raw' => [
                'from' => $tstampStart,
                'to' => $tstampEnd,
            ],
        ];
    }
}
