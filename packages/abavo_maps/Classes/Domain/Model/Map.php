<?php

namespace TYPO3\AbavoMaps\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
/**
 * Eine Karte
 */
class Map extends AbstractEntity
{
    /**
     * Titel
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * Breite
     *
     * @var integer
     * @Extbase\Validate("NotEmpty")
     */
    protected $width = 100;

    /**
     * Höhe
     *
     * @var integer
     */
    protected $height = 480;

    /**
     * Zoom
     *
     * @var string
     */
    protected $zoom = 5;

    /**
     * Zoom Kontrolle
     *
     * @var integer
     */
    protected $zoomcontrol = 0;

    /**
     * Steuerelemente
     *
     * @var integer
     */
    protected $pancontrol = 0;

    /**
     * Skalierungs Kontrolle
     *
     * @var integer
     */
    protected $scalecontrol = 0;

    /**
     * Marker Icon (optional)
     *
     * @var ObjectStorage<FileReference>
     */
    protected $pinicon = '';

    /**
     * Marker (aktuelle PID)
     *
     * @var ObjectStorage<Marker>
     * @Extbase\ORM\Lazy
     */
    protected $markers;

    /**
     * Shape (aktuelle PID)
     *
     * @var ObjectStorage<Shape>
     * @Extbase\ORM\Lazy
     */
    protected $shapes;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->markers = new ObjectStorage();
        $this->shapes  = new ObjectStorage();
        $this->pinicon = new ObjectStorage();
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the width
     *
     * @return integer $width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Sets the width
     *
     * @param integer $width
     * @return void
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Returns the height
     *
     * @return integer $height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Sets the height
     *
     * @param integer $height
     * @return void
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * Returns the zoom
     *
     * @return string $zoom
     */
    public function getZoom()
    {
        return $this->zoom;
    }

    /**
     * Sets the zoom
     *
     * @param string $zoom
     * @return void
     */
    public function setZoom($zoom)
    {
        $this->zoom = $zoom;
    }

    /**
     * Returns the zoomcontrol
     *
     * @return integer $zoomcontrol
     */
    public function getZoomcontrol()
    {
        return $this->zoomcontrol;
    }

    /**
     * Sets the zoomcontrol
     *
     * @param integer $zoomcontrol
     * @return void
     */
    public function setZoomcontrol($zoomcontrol)
    {
        $this->zoomcontrol = $zoomcontrol;
    }

    /**
     * Returns the pancontrol
     *
     * @return integer $pancontrol
     */
    public function getPancontrol()
    {
        return $this->pancontrol;
    }

    /**
     * Sets the pancontrol
     *
     * @param integer $pancontrol
     * @return void
     */
    public function setPancontrol($pancontrol)
    {
        $this->pancontrol = $pancontrol;
    }

    /**
     * Returns the scalecontrol
     *
     * @return integer $scalecontrol
     */
    public function getScalecontrol()
    {
        return $this->scalecontrol;
    }

    /**
     * Sets the scalecontrol
     *
     * @param integer $scalecontrol
     * @return void
     */
    public function setScalecontrol($scalecontrol)
    {
        $this->scalecontrol = $scalecontrol;
    }

    /**
     * Returns the pinicon
     *
     * @return string $pinicon
     */
    public function getPinicon()
    {
        return $this->pinicon;
    }

    /**
     * Sets the pinicon
     *
     * @param string $pinicon
     * @return void
     */
    public function setPinicon($pinicon)
    {
        $this->pinicon = $pinicon;
    }

    /**
     * Adds a Marker
     *
     * @param Marker $marker
     * @return void
     */
    public function addMarker(Marker $marker)
    {
        $this->markers->attach($marker);
    }

    /**
     * Adds a Shape
     *
     * @param Shape $shape
     * @return void
     */
    public function addShape(Marker $shape)
    {
        $this->shapes->attach($shape);
    }

    /**
     * Removes a Marker
     *
     * @param Marker $markerToRemove The Marker to be removed
     * @return void
     */
    public function removeMarker(Marker $markerToRemove)
    {
        $this->markers->detach($markerToRemove);
    }

    /**
     * Removes a Shape
     *
     * @param Shape $shapeToRemove The Shape to be removed
     * @return void
     */
    public function removeShape(Shape $shapeToRemove)
    {
        $this->markers->detach($shapeToRemove);
    }

    /**
     * Returns the markers
     *
     * @return ObjectStorage<Marker> $markers
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Returns the shapes
     *
     * @return ObjectStorage<Shape> $shapes
     */
    public function getShapes()
    {
        return $this->shapes;
    }

    /**
     * Sets the markers
     *
     * @param ObjectStorage<Marker> $markers
     * @return void
     */
    public function setMarkers(ObjectStorage $markers)
    {
        $this->markers = $markers;
    }

    /**
     * Sets the shapes
     *
     * @param ObjectStorage<Shape> $shapes
     * @return void
     */
    public function setShapes(ObjectStorage $shapes)
    {
        $this->shapes = $shapes;
    }
}