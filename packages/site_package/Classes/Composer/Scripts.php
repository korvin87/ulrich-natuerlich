<?php

namespace Abavo\SitePackage\Composer;

use Composer\Script\Event;
use Composer\Installer\PackageEvent;

class Scripts
{
    public static function postPackageUpdate(PackageEvent $event)
    {
        $packageName = $event->getOperation()->getInitialPackage()->getName();

        switch ($packageName) {
            case 'typo3/cms':
                echo "Core-Update detected: running upgrade wizard.\n";
                exec('vendor/bin/typo3cms upgrade:all');
                echo "\n";
                break;
        }
    }
}
