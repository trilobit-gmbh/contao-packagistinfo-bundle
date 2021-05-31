<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

namespace Trilobit\PackagistinfoBundle\Controller;

use Contao\Config;
use Contao\CoreBundle\Controller\AbstractController;
use Contao\Database;
use Contao\Date;
use Contao\System;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(defaults={"_scope": "frontend", "_token_check": false, "slug": "nop"})
 */
class PackagistinfoController extends AbstractController
{
    /**
     * @var array|bool|float|int|string|null
     */
    private $rootDir;

    /**
     * @var string
     */
    private $packagistUrl;

    /**
     * @var bool
     */
    private $sendResponse;

    private $database;
    private $datimFormat;
    private $dateFormat;
    private $timeFormat;
    private $interval;
    private $labelFormat;

    /**
     * PackagistinfoController constructor.
     */
    public function __construct(bool $sendResponse = false)
    {
        $this->rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $this->packagistUrl = 'https://packagist.org/search.json?q=trilobit-gmbh';
        $this->database = Database::getInstance();

        $this->datimFormat = Config::get('datimFormat');
        $this->dateFormat = Config::get('dateFormat');
        $this->timeFormat = Config::get('timeFormat');
        $this->interval = 'Ymd';
        $this->labelFormat = 'D, d. F Y';

        $this->sendResponse = $sendResponse;
    }

    public function getLabelFormat(): string
    {
        return $this->labelFormat;
    }

    public function setLabelFormat(string $labelFormat): void
    {
        $this->labelFormat = $labelFormat;
    }

    /**
     * @return array|bool|float|int|string|null
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    public function getPackagistUrl(): string
    {
        return $this->packagistUrl;
    }

    /**
     * @return mixed|null
     */
    public function getDatimFormat()
    {
        return $this->datimFormat;
    }

    /**
     * @return mixed|null
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return mixed|null
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function getFirstDate(array $timeline = [])
    {
        return $timeline[array_key_first($timeline)];
    }

    public function getLastDate(array $timeline = [])
    {
        return $timeline[array_key_last($timeline)];
    }

    public function import(): JsonResponse
    {
        $data = $this->grabPackagistData($this->packagistUrl);

        $this->updatePackagistData($data);

        if (!$this->sendResponse) {
            exit();
        }

        $response = new JsonResponse();
        $response->setData([
            'status' => [
                'code' => '200',
                'command' => 'import/packagist',
                'message' => 'OK',
                'data' => $data,
            ],
        ]);
        $response->send();

        exit();
    }

    public function getTimeline(array $items = []): array
    {
        //$items = [];
        return $this->database
            ->prepare('SELECT DISTINCT `check` FROM tl_packagistinfo WHERE pid!=? AND published=?'.(!empty($items) ? ' AND pid IN (\''.implode('\',\'', $items).'\')' : '').' ORDER BY `check` ASC')
            ->execute('0', '1')
            ->fetchEach('check')
        ;
    }

    public function getPackages(array $items = []): array
    {
        $result = $this->database
            ->prepare('SELECT id,name,description,url,repository,downloads,favers,`check` FROM tl_packagistinfo WHERE pid=? AND published=?'.(!empty($items) ? ' AND id IN ('.implode(',', $items).')' : '').' ORDER BY name')
            ->execute('0', '1')
            ->fetchAllAssoc()
        ;
        $data = [];
        foreach ($result as $value) {
            $data[$value['id']] = $value;
            $data[$value['id']]['title'] = $this->getPackageName($value['name']);
        }

        return $data;
    }

    public function getPackageItems($id): array
    {
        return Database::getInstance()
            ->prepare('SELECT downloads,favers,`check` FROM tl_packagistinfo WHERE pid=? ORDER BY `check` ASC')
            ->execute($id)
            ->fetchAllAssoc()
        ;
    }

    public function getPackageName($value): string
    {
        return ucwords($value, ' -');
    }

    protected function updatePackagistData(array $data): void
    {
        $rootElements = $this->database
            ->prepare('SELECT name,id,published,`check` FROM tl_packagistinfo WHERE pid=0')
            ->execute()
            ->fetchAllAssoc()
        ;

        $time = time();

        foreach ($data as $value) {
            $pid = 0;
            $tstamp = 0;
            $published = 1;

            $value['name'] = str_replace('trilobit-gmbh/', '', $value['name']);

            if (false !== array_search($value['name'], array_column($rootElements, 'name'), true)) {
                $pid = (int) $rootElements[array_search($value['name'], array_column($rootElements, 'name'), true)]['id'];
                $published = (int) $rootElements[array_search($value['name'], array_column($rootElements, 'name'), true)]['published'];
                $tstamp = (int) $rootElements[array_search($value['name'], array_column($rootElements, 'name'), true)]['check'];
            }

            if (Date::parse($this->getInterval(), $time) <= Date::parse($this->getInterval(), $tstamp)) {
                continue;
            }

            var_dump(strtotime('-1 days', $time), $tstamp, (strtotime('-1 days', $time) <= $tstamp));

            // add new row
            $this->database
                ->prepare('INSERT INTO tl_packagistinfo %s')
                ->set([
                    'pid' => $pid,
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'url' => $value['url'],
                    'repository' => $value['repository'],
                    'downloads' => $value['downloads'],
                    'favers' => $value['favers'],
                    'check' => $time,
                    'tstamp' => $time,
                    'published' => $published,
                ])
                ->execute()
            ;

            // update parent
            if (0 !== $pid) {
                $this->database
                    ->prepare('UPDATE tl_packagistinfo %s WHERE id=?')
                    ->set([
                        'downloads' => $value['downloads'],
                        'favers' => $value['favers'],
                        'check' => $time,
                        'tstamp' => $time,
                    ])
                    ->execute($pid)
                ;
            }

            // update published status
            $this->database
                ->prepare('UPDATE tl_packagistinfo %s WHERE pid=?')
                ->set([
                    'published' => $published,
                ])
                ->execute($pid)
            ;
        }
    }

    protected function grabPackagistData(string $url, array $data = []): array
    {
        $response = json_decode($this->curlData($url), true);

        if (!\is_array($response)) {
            return $data;
        }

        if (isset($response['results'])) {
            $data = array_merge($data, $response['results']);
        }
        if (isset($response['next'])) {
            $data = $this->grabPackagistData($response['next'], $data);
        }

        return $data;
    }

    protected function curlData(string $url): string
    {
        $curl = curl_init($url);

        curl_setopt($curl, \CURLOPT_HEADER, false);
        curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, \CURLOPT_USERAGENT, 'trilobit packagist info cron');
        curl_setopt($curl, \CURLOPT_COOKIEJAR, $this->rootDir.'/system/tmp/curl.cookiejar.txt');
        curl_setopt($curl, \CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, \CURLOPT_ENCODING, '');
        curl_setopt($curl, \CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, \CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, \CURLOPT_SSL_VERIFYPEER, false);    // required for https urls
        curl_setopt($curl, \CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, \CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, \CURLOPT_MAXREDIRS, 10);

        $stream = curl_exec($curl);

        if (200 !== curl_getinfo($curl, \CURLINFO_HTTP_CODE)) {
            return json_encode([]);
        }

        return $stream;
    }
}
