<?php
/*
 * abavo_form
 * 
 * @copyright   2016 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

namespace Abavo\AbavoForm\Utility;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use SJBR\StaticInfoTables\Domain\Repository\CountryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TcaHelper
 *
 * @author mbruckmoser
 */
class TcaHelper
{

    function getTemplateListNew($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['new'] as $template) {
            $optionList[] = [0 => $template['label'], 1 => $template['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getTemplateListCreate($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['create'] as $template) {
            $optionList[] = [0 => $template['label'], 1 => $template['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getTemplateListEmail($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['templates']['email'] as $template) {
            $optionList[] = [0 => $template['label'], 1 => $template['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getServicesList($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['services'] as $service) {
            $optionList[] = [0 => $service['label'], 1 => $service['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getSessionhooks($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['sessionhooks'] as $class) {
            $optionList[] = [0 => $class['label'], 1 => $class['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getModelClassList($config)
    {
        $optionList = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['abavo_form']['formmodels'] as $class) {
            $optionList[] = [0 => $class['label'], 1 => $class['value']];
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }

    function getCountriesIsoCodeA3($config)
    {
        $optionList        = [];
        $objectManager     = GeneralUtility::makeInstance(ObjectManager::class);
        $countryRepository = $objectManager->get(CountryRepository::class);
        $countryRepository->setDefaultQuerySettings($countryRepository->createQuery()->getQuerySettings()->setRespectStoragePage(false));
        $countries         = $countryRepository->findAll();

        if ($countries->count() > 0) {
            foreach ($countries as $country) {
                $optionList[] = [0 => $country->getShortNameLocal(), 1 => $country->getIsoCodeA3()];
            }
        }

        // return config
        if (is_array($config['items'])) {
            $config['items'] = array_merge($config['items'], $optionList);

            // sort by value
            usort($config['items'], fn($a, $b) => strcmp($a[0], $b[0]));
        } else {
            $config['items'] = $optionList;
        }

        return $config;
    }
}