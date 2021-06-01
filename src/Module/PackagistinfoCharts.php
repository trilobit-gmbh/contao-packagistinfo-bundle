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

class PackagistinfoCharts extends Module
{
    const chartSettings = [
        'type' => 'line', //line, radar, doughnut,

        'default' => [
            'fill' => 'false',
            'spanGaps' => 'false',
            'steppedLine' => 'false',
            //'backgroundColor' => '',
            //'pointBorderColor' => '',
            //'borderColor' => '',
            'borderWidth' => 1,
            'pointRadius' => '1',
            'pointHoverRadius' => '5',
            'showLine' => 'true',
        ],

        'options' => [
            'scales' => [
                'xAxes' => [
                    [
                        'ticks' => [
                            'min' => 0,
                        ],
                        'position' => 'bottom',
                    ],
                ],
                'yAxes' => [
                    [
                        'ticks' => [
                            'beginAtZero' => 'false',
                            'min' => 0,
                        ],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'title' => ['display' => false],
            ],
        ],

        'colors' => [
            '#ffebee',
            '#ffcdd2',
            '#ef9a9a',
            '#e57373',
            '#ef5350',
            '#f44336',
            '#e53935',
            '#d32f2f',
            '#c62828',
            '#b71c1c',
            '#ff8a80',
            '#ff5252',
            '#ff1744',
            '#d50000',
            '#fce4ec',
            '#f8bbd0',
            '#f48fb1',
            '#f06292',
            '#ec407a',
            '#e91e63',
            '#d81b60',
            '#c2185b',
            '#ad1457',
            '#880e4f',
            '#ff80ab',
            '#ff4081',
            '#f50057',
            '#c51162',
            '#f3e5f5',
            '#e1bee7',
            '#ce93d8',
            '#ba68c8',
            '#ab47bc',
            '#9c27b0',
            '#8e24aa',
            '#7b1fa2',
            '#6a1b9a',
            '#4a148c',
            '#ea80fc',
            '#e040fb',
            '#d500f9',
            '#aa00ff',
            '#ede7f6',
            '#d1c4e9',
            '#b39ddb',
            '#9575cd',
            '#7e57c2',
            '#673ab7',
            '#5e35b1',
            '#512da8',
            '#4527a0',
            '#311b92',
            '#b388ff',
            '#7c4dff',
            '#651fff',
            '#6200ea',
            '#e8eaf6',
            '#c5cae9',
            '#9fa8da',
            '#7986cb',
            '#5c6bc0',
            '#3f51b5',
            '#3949ab',
            '#303f9f',
            '#283593',
            '#1a237e',
            '#8c9eff',
            '#536dfe',
            '#3d5afe',
            '#304ffe',
            '#e3f2fd',
            '#bbdefb',
            '#90caf9',
            '#64b5f6',
            '#42a5f5',
            '#2196f3',
            '#1e88e5',
            '#1976d2',
            '#1565c0',
            '#0d47a1',
            '#82b1ff',
            '#448aff',
            '#2979ff',
            '#2962ff',
            '#e1f5fe',
            '#b3e5fc',
            '#81d4fa',
            '#4fc3f7',
            '#29b6f6',
            '#03a9f4',
            '#039be5',
            '#0288d1',
            '#0277bd',
            '#01579b',
            '#80d8ff',
            '#40c4ff',
            '#00b0ff',
            '#0091ea',
            '#e0f7fa',
            '#b2ebf2',
            '#80deea',
            '#4dd0e1',
            '#26c6da',
            '#00bcd4',
            '#00acc1',
            '#0097a7',
            '#00838f',
            '#006064',
            '#84ffff',
            '#18ffff',
            '#00e5ff',
            '#00b8d4',
            '#e0f2f1',
            '#b2dfdb',
            '#80cbc4',
            '#4db6ac',
            '#26a69a',
            '#009688',
            '#00897b',
            '#00796b',
            '#00695c',
            '#004d40',
            '#a7ffeb',
            '#64ffda',
            '#1de9b6',
            '#00bfa5',
            '#e8f5e9',
            '#c8e6c9',
            '#a5d6a7',
            '#81c784',
            '#66bb6a',
            '#4caf50',
            '#43a047',
            '#388e3c',
            '#2e7d32',
            '#1b5e20',
            '#b9f6ca',
            '#69f0ae',
            '#00e676',
            '#00c853',
            '#f1f8e9',
            '#dcedc8',
            '#c5e1a5',
            '#aed581',
            '#9ccc65',
            '#8bc34a',
            '#7cb342',
            '#689f38',
            '#558b2f',
            '#33691e',
            '#ccff90',
            '#b2ff59',
            '#76ff03',
            '#64dd17',
            '#f9fbe7',
            '#f0f4c3',
            '#e6ee9c',
            '#dce775',
            '#d4e157',
            '#cddc39',
            '#c0ca33',
            '#afb42b',
            '#9e9d24',
            '#827717',
            '#f4ff81',
            '#eeff41',
            '#c6ff00',
            '#aeea00',
            '#fffde7',
            '#fff9c4',
            '#fff59d',
            '#fff176',
            '#ffee58',
            '#ffeb3b',
            '#fdd835',
            '#fbc02d',
            '#f9a825',
            '#f57f17',
            '#ffff8d',
            '#ffff00',
            '#ffea00',
            '#ffd600',
            '#fff8e1',
            '#ffecb3',
            '#ffe082',
            '#ffd54f',
            '#ffca28',
            '#ffc107',
            '#ffb300',
            '#ffa000',
            '#ff8f00',
            '#ff6f00',
            '#ffe57f',
            '#ffd740',
            '#ffc400',
            '#ffab00',
            '#fff3e0',
            '#ffe0b2',
            '#ffcc80',
            '#ffb74d',
            '#ffa726',
            '#ff9800',
            '#fb8c00',
            '#f57c00',
            '#ef6c00',
            '#e65100',
            '#ffd180',
            '#ffab40',
            '#ff9100',
            '#ff6d00',
            '#fbe9e7',
            '#ffccbc',
            '#ffab91',
            '#ff8a65',
            '#ff7043',
            '#ff5722',
            '#f4511e',
            '#e64a19',
            '#d84315',
            '#bf360c',
            '#ff9e80',
            '#ff6e40',
            '#ff3d00',
            '#dd2c00',
            '#efebe9',
            '#d7ccc8',
            '#bcaaa4',
            '#a1887f',
            '#8d6e63',
            '#795548',
            '#6d4c41',
            '#5d4037',
            '#4e342e',
            '#3e2723',
            '#d7ccc8',
            '#bcaaa4',
            '#8d6e63',
            '#5d4037',
            '#fafafa',
            '#f5f5f5',
            '#eeeeee',
            '#e0e0e0',
            '#bdbdbd',
            '#9e9e9e',
            '#757575',
            '#616161',
            '#424242',
            '#212121',
            '#f5f5f5',
            '#eeeeee',
            '#bdbdbd',
            '#616161',
            '#eceff1',
            '#cfd8dc',
            '#b0bec5',
            '#90a4ae',
            '#78909c',
            '#607d8b',
            '#546e7a',
            '#455a64',
            '#37474f',
            '#263238',
            '#cfd8dc',
            '#b0bec5',
            '#78909c',
            '#455a64',
        ],
    ];

    protected $strTemplate = 'mod_packagistinfocharts';

    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $objTemplate = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['packagistinfocharts'][0]).' ###';
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

        $color = rand(0, \count(self::chartSettings['colors']) - 1);

        $timeline = $packagist->getTimeline($this->packages);
        $packages = $packagist->getPackages($this->packages);

        $this->Template->url = $packagist->getPackagistUrl();
        $this->Template->packages = $packages;

        $config = [
            'type' => self::chartSettings['type'],
            'options' => self::chartSettings['options'],
            'data' => [
                'datasets' => [],
                'labels' => array_map(
                    (function ($tstamp) use ($packagist) {
                        return Date::parse($packagist->getLabelFormat(), $tstamp);
                    }),
                    $timeline
                ),
            ],
        ];

        $n = 0;
        foreach (['downloads', 'favers'] as $type) {
            foreach ($packages as $value) {
                $config['data']['datasets'][$n] = self::chartSettings['default'];
                $config['data']['datasets'][$n]['backgroundColor'] = self::chartSettings['colors'][$color];
                $config['data']['datasets'][$n]['pointBorderColor'] = self::chartSettings['colors'][$color];
                $config['data']['datasets'][$n]['borderColor'] = self::chartSettings['colors'][$color];
                $config['data']['datasets'][$n]['data'] = [];
                $config['data']['datasets'][$n]['label'] = $packagist->getPackageName($type.' '.$value['name']);

                foreach ($timeline as $tstamp) {
                    $marker = Date::parse($packagist->getInterval(), $tstamp);
                    $config['data']['datasets'][$n]['data'][$marker] = '';
                }

                $data = $packagist->getPackageItems($value['id']);

                foreach ($data as $count) {
                    $marker = Date::parse($packagist->getInterval(), $count['check']);
                    $config['data']['datasets'][$n]['data'][$marker] = $count[$type];
                }

                $config['data']['datasets'][$n]['data'] = array_values($config['data']['datasets'][$n]['data']);

                $color += 5;
                if ($color >= \count(self::chartSettings['colors'])) {
                    $color = 0;
                }
                ++$n;
            }
            $color += 25;
        }

        $tstampStart = $packagist->getFirstDate($timeline);
        $tstampEnd = $packagist->getLastDate($timeline);

        $this->Template->config = [
            'json' => json_encode($config),
            'php' => $config,
        ];

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
