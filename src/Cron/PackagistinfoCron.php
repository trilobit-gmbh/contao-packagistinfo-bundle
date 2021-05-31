<?php

declare(strict_types=1);

/*
 * @copyright  trilobit GmbH
 * @author     trilobit GmbH <https://github.com/trilobit-gmbh>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/trilobit-gmbh/contao-packagistinfo-bundle
 */

namespace Trilobit\PackagistinfoBundle\Cron;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use Psr\Log\LogLevel;
use Trilobit\PackagistinfoBundle\Controller\PackagistinfoController;

class PackagistinfoCron // extends AbstractController
{
    public function __invoke(): void
    {
        $logger = System::getContainer()->get('monolog.logger.contao');
        $logger->log(
            LogLevel::INFO,
            'CRON | Autoimport packagist info data',
            ['contao' => new ContaoContext(__METHOD__, 'CRON')]
        );

        $packagist = new PackagistinfoController();
        $packagist->import();
    }
}
