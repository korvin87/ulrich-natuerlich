<?php

namespace TYPO3\AbavoMaps\Controller;

/*
 * abavo_maps
 *
 * @copyright   2015 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\AbavoMaps\Domain\Repository\MapRepository;
use TYPO3\AbavoMaps\Domain\Repository\MarkerRepository;
use TYPO3\AbavoMaps\Domain\Repository\ShapeRepository;
use TYPO3\AbavoMaps\Domain\Repository\AbavoAddressRepository;
use TYPO3\AbavoMaps\Domain\Repository\AddressRepository;
use TYPO3\AbavoMaps\Domain\Repository\FeUserRepository;
use TYPO3\AbavoMaps\Domain\Repository\NnAddressRepository;
use TYPO3\AbavoMaps\Domain\Model\Map;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\AbavoMaps\Domain\Model\Marker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\AbavoMaps\User\Geocode;

/**
 * MapController
 */
class MapController extends ActionController
{
    /**
     * mapRepository
     *
     * @var MapRepository
     */
    protected $mapRepository;

    /**
     * markerRepository
     *
     * @var MarkerRepository
     */
    protected $markerRepository;

    /**
     * shapeRepository
     *
     * @var ShapeRepository
     */
    protected $shapeRepository;

    /**
     * abavoAddressRepository
     *
     * @var AbavoAddressRepository
     */
    protected $abavoAddressRepository;

    /**
     * addressRepository
     *
     * @var AddressRepository
     */
    protected $addressRepository;

    /**
     * feUserRepository
     *
     * @var FeUserRepository
     */
    protected $feUserRepository;

    /**
     * nnAddressRepository
     *
     * @var NnAddressRepository
     */
    protected $nnAddressRepository;

    /**
     * Make custom shapes
     *
     * @param $conf string serialized objects config
     * @return array
     */
    private function makeCustomShapes($arrSubObjects = [])
    {

        /*
         * SHAPES-EXAMPLES
         *
         * Circle:
         * &tx_abavomaps_pimain[addObjects][0][circle]={"longitude":"10.72","latitude":"48.03","radius":"16","color":"0000FF"}
         * optional circle usage without latng(beware the usage limit amount 2500/day!):
         * {"location":"NebelhornstraÃŸe 8 Buchloe"}
         *
         * Rectangle:
         * &tx_abavomaps_pimain[addObjects][0][rectangle]={"longitude":"10.75","latitude":"48.05","longitudesw":"10.72","latitudesw":"48.03","color":"0000FF"}
         *
         * Polygon:
         * &tx_abavomaps_pimain[addObjects][0][polygon]={"bounds":["48.987595867710674,10.767821984374905","48.399362232073024,10.921874984374995","48.512381122196615,10.153808984374905"],"color":"0000FF"}
         */

        foreach ($arrSubObjects as $key => $subObject) {

            // Config
            foreach (json_decode(current($subObject), null, 512, JSON_THROW_ON_ERROR) as $property => $value) {
                $arrSubObjects[$key][$property] = $value;
            }
            $arrSubObjects[$key]['body'] = htmlspecialchars(key($subObject));

            // Switch between "body"
            switch ($arrSubObjects[$key]['body']) {

                case 'circle':
                    // If no geocode given, try to get it server side from Google
                    if (($arrSubObjects[$key]['longitude'] == '' || $arrSubObjects[$key]['latitude'] == '') && $arrSubObjects[$key]['location'] != '') {
                        $gc      = new Geocode();
                        $geocode = $gc->geoCodeAdress(
                            htmlspecialchars($arrSubObjects[$key]['location']), htmlspecialchars($this->settings['gmApiKey']),
                            htmlspecialchars($this->settings['quotaUser'])
                        );

                        if ($geocode) {
                            $arrSubObjects[$key]['longitude'] = (float) $geocode['longitude'];
                            $arrSubObjects[$key]['latitude']  = (float) $geocode['latitude'];
                        }
                    }
                    break;
                // Dummy case
                case 'rectangle':
                    break;
                // Dummy case
                case 'polygon':
                    break;
            }

            //Define ShapeÂ´s color
            $arrSubObjects[$key]['color'] = ($arrSubObjects[$key]['color']) ? GeneralUtility::removeXSS($arrSubObjects[$key]['color']) : $this->settings['shapeColor'];
            $arrSubObjects[$key]['color'] = (substr($arrSubObjects[$key]['color'], 0, 1) != '#') ? '#'.$arrSubObjects[$key]['color'] : $arrSubObjects[$key]['color']; //Prepend a "#"
            // Clean unused fields
            unset($arrSubObjects[$key][$arrSubObjects[$key]['body']]);
        }

        return $arrSubObjects;
    }

    /**
     * Set map properties method
     *
     * @param Map $map
     * @param array $properties
     * @return Map
     */
    private function setMapProperties(Map &$map, $properties = [])
    {
        foreach ($properties as $property => $value) {
            $map->_setProperty($property, $value);
        }

        return $map;
    }

    /**
     * action show
     *
     * @return void
     */
    public function showAction(): ResponseInterface
    {

        $confCustomMap = [];
        /*
         * POST-Arguments
         *
         * UID-Examples:
         * &tx_abavomaps_pimain[action]=show&tx_abavomaps_pimain[controller]=Map&tx_abavomaps_pimain[uids]=1,2,3
         * &tx_abavomaps_pimain[action]=show&tx_abavomaps_pimain[controller]=Map&tx_abavomaps_pimain[uids]=1,2,3&tx_abavomaps_pimain[repo]=AddressRepository
         * &tx_abavomaps_pimain[action]=show&tx_abavomaps_pimain[controller]=Map&tx_abavomaps_pimain[uids]=1&tx_abavomaps_pimain[repo]=FeUserRepository
         * &tx_abavomaps_pimain[action]=show&tx_abavomaps_pimain[controller]=Map&tx_abavomaps_pimain[uids]=1,2&tx_abavomaps_pimain[repo]=NnAddressRepository
         *
         * Custom-Map-Example:
         * &tx_abavomaps_pimain[Map]={"width":"700","height":"400","zoom":"5","longitude":"10.72","latitude":"48.03"}
         */
        $arrArguments = [];
        if (is_array($this->request->getArguments()) && is_array($this->settings['arguments'])) {
            $arrArguments = array_merge($this->settings['arguments'], $this->request->getArguments());
        }

        /*
         * Use stdWrap for arguments
         * Take a look in EXT:abavo_maps/Configuration/TypoScript/setup.txt -> lib.tx_abavomaps_map_current_records
         */
        if (isset($arrArguments['useStdWrap']) && !empty($arrArguments['useStdWrap'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptArray   = $typoScriptService->convertPlainArrayToTypoScriptArray($arrArguments);
            $stdWrapProperties = GeneralUtility::trimExplode(',', $arrArguments['useStdWrap'], true);
            foreach ($stdWrapProperties as $key) {
                if (is_array($typoScriptArray[$key.'.'])) {
                    $arrArguments[$key] = $this->configurationManager->getContentObject()->stdWrap(
                        $arrArguments[$key], $typoScriptArray[$key.'.']
                    );
                }
            }
        }

        /*
         *  If POST-Data
         */
        if ($arrArguments['uids'] || $arrArguments['Map']) {
            switch (htmlspecialchars($arrArguments['repo'])) {
                case ('AbavoAddressRepository'):
                    $repository = $this->abavoAddressRepository;
                    break;
                case ('AddressRepository'):
                    $repository = $this->addressRepository;
                    break;
                case ('FeUserRepository'):
                    $repository = $this->feUserRepository;
                    break;
                case ('NnAddressRepository'):
                    $repository = $this->nnAddressRepository;
                    break;
                default:
                    $repository = $this->markerRepository;
                    break;
            }

            // Make Custom Shapes
            $arrShapes = $this->makeCustomShapes((array) $arrArguments['addObjects']);

            // Get and clean Custom-Map arguments
            if ($arrArguments['Map']) {
                $confCustomMap = [];

                $arrArgumentsMapConf = (!is_array($arrArguments['Map'])) ? json_decode($arrArguments['Map'], null, 512, JSON_THROW_ON_ERROR) : $arrArguments['Map'];
                foreach ($arrArgumentsMapConf as $key => $conf) {
                    $confCustomMap[$key] = htmlspecialchars($conf);
                }
            }

            // Make new dummy map with requested marker uid´s
            $objMap     = GeneralUtility::makeInstance(Map::class);
            $objMarkers = [];
            foreach (explode(',', htmlspecialchars($arrArguments['uids'])) AS $uid) {
                $object       = $repository->findByUid($uid);
                ( $object != null ) ? $objMarkers[] = $object : '';
            }

            // Make new dummy marker, if objMarkers is empty
            if (empty($objMarkers)) {
                $objMarker    = GeneralUtility::makeInstance(Marker::class);
                $objMarker->_setProperty('uid', 0);
                $objMarker->_setProperty('latitude', (float) ($confCustomMap['latitude']) ? $confCustomMap['latitude'] : 0.000001);
                $objMarker->_setProperty('longitude', (float) ($confCustomMap['longitude']) ? $confCustomMap['longitude'] : 0.000001);
                $objMarkers[] = $objMarker;
            }

            // Set Dummy Map Properties
            $properties = [
                'uid' => 0,
                'markers' => $objMarkers,
                'height' => (int) ($confCustomMap['height']) ? $confCustomMap['height'] : $this->settings['defaultHeight'],
                'width' => (int) ($confCustomMap['width']) ? $confCustomMap['width'] : $this->settings['defaultWidth'],
                'zoom' => (int) ($confCustomMap['zoom']) ? $confCustomMap['zoom'] : 5,
                'zoomcontrol' => (int) ($confCustomMap['zoomcontrol']) ?: 0,
                'shapes' => $arrShapes
            ];
            $this->setMapProperties($objMap, $properties);
            //
        } else {

            /*
             *  DEFAULT USAGE (render by settings.mode)
             */
            switch ($this->settings['mode']) {

                // render by flexform
                case 'flexform':
                    $objMap     = GeneralUtility::makeInstance(Map::class);
                    $properties = array_merge(['uid' => 0], $this->settings['mapProperties']);

                    if (isset($properties['markers']) && (boolean) $properties['markers']) {
                        $markerUids            = GeneralUtility::intExplode(',', $properties['markers']);
                        $properties['markers'] = [];
                        foreach ($markerUids as $MarkerUid) {
                            $properties['markers'][] = $this->markerRepository->findByUid($MarkerUid);
                        }
                    } else {
                        $properties['markers'] = [];

                        // Make new dummy marker, if objMarkers is empty
                        $objMarker = GeneralUtility::makeInstance(Marker::class);
                        $objMarker->_setProperty('uid', 1);
                        $objMarker->_setProperty('title', $this->settings['mapProperties']['title']);
                        $objMarker->_setProperty('description', $this->settings['mapProperties']['description']);
                        $objMarker->_setProperty('latitude', (boolean) ( $this->settings['mapProperties']['latitude']) ? $this->settings['mapProperties']['latitude'] : 0.000001);
                        $objMarker->_setProperty('longitude', (boolean) ( $this->settings['mapProperties']['longitude']) ? $this->settings['mapProperties']['longitude'] : 0.000001);

                        $properties['markers'][] = $objMarker;
                    }

                    $this->setMapProperties($objMap, $properties);
                    break;

                // render by settings.map (=map uid)
                default:
                    $objMap = $this->mapRepository->findByUid((int) $this->settings['map']);
            }
        }

        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($objMap);
        // Assign language string and Map object to view from current flexform if given, else all from storagePid (constants//setup//plugin//currentPid)
        $this->view->assignMultiple(['language' => $GLOBALS['TSFE']->lang, 'map' => $objMap]);
        return $this->htmlResponse();
    }

    public function injectMapRepository(MapRepository $mapRepository): void
    {
        $this->mapRepository = $mapRepository;
    }

    public function injectMarkerRepository(MarkerRepository $markerRepository): void
    {
        $this->markerRepository = $markerRepository;
    }

    public function injectShapeRepository(ShapeRepository $shapeRepository): void
    {
        $this->shapeRepository = $shapeRepository;
    }

    public function injectAbavoAddressRepository(AbavoAddressRepository $abavoAddressRepository): void
    {
        $this->abavoAddressRepository = $abavoAddressRepository;
    }

    public function injectAddressRepository(AddressRepository $addressRepository): void
    {
        $this->addressRepository = $addressRepository;
    }

    public function injectFeUserRepository(FeUserRepository $feUserRepository): void
    {
        $this->feUserRepository = $feUserRepository;
    }

    public function injectNnAddressRepository(NnAddressRepository $nnAddressRepository): void
    {
        $this->nnAddressRepository = $nnAddressRepository;
    }
}