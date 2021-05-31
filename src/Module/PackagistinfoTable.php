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

        return parent::generate();
    }

    protected function compile()
    {
        $packagist = new PackagistinfoController();

        $timeline = $packagist->getTimeline($this->packages);
        $packages = $packagist->getPackages($this->packages);

        $this->Template->packages = $packages;

        $thead = [''];
        $tfoot = [];
        $tbody = [];

        foreach ($timeline as $tstamp) {
            $marker = Date::parse($packagist->getLabelFormat(), $tstamp);
            $thead[] = $marker;
        }
        $tfoot = $thead;

        $n = 0;
        foreach ($packages as $key => $value) {
            $tbody[$key][] = $value['id'];

            foreach ($timeline as $tstamp) {
                $tbody[$key][$tstamp] = '';
            }

            $data = $packagist->getPackageItems($value['id']);

            $last = [
                'downloads' => '',
                'favers' => '',
            ];
            foreach ($data as $count) {
                $tbody[$key][$count['check']] = [
                    'count' => [
                        'downloads' => $count['downloads'],
                        'favers' => $count['favers'],
                    ],
                    'status' => [
                        'downloads' => ('' === $last['downloads'] ? '' : ($last['downloads'] === $count['downloads'] ? 0 : ($last['downloads'] < $count['downloads'] ? 1 : -1))),
                        'favers' => ('' === $last['downloads'] ? '' : ($last['favers'] === $count['favers'] ? 0 : ($last['favers'] < $count['favers'] ? 1 : -1))),
                    ],
                ];

                $last = [
                    'downloads' => $count['downloads'],
                    'favers' => $count['favers'],
                ];
            }

            ++$n;
        }

        $tstampStart = $packagist->getFirstDate($timeline);
        $tstampEnd = $packagist->getLastDate($timeline);

        $this->Template->thead = $thead;
        $this->Template->tfoot = $tfoot;
        $this->Template->tbody = $tbody;
        $this->Template->caption = $this->packagistsummary;

        $this->Template->timeline = [
            'datim' => [
                'from' => Date::parse($packagist->getDatimFormat(), $tstampStart),
                'to' => Date::parse($packagist->getDatimFormat(), $tstampEnd),
            ],
            'date' => [
                'from' => Date::parse($packagist->getDateFormat(), $tstampStart),
                'to' => Date::parse($packagist->getDateFormat(), $tstampEnd),
            ],
            'time' => [
                'from' => Date::parse($packagist->getTimeFormat(), $tstampStart),
                'to' => Date::parse($packagist->getTimeFormat(), $tstampEnd),
            ],
            'raw' => [
                'from' => $tstampStart,
                'to' => $tstampEnd,
            ],
        ];
    }
}
