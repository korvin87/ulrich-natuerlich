<?php
use TYPO3\CMS\Scheduler\Task\AbstractTask;
use TYPO3\AbavoMaps\User\Geocode;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\UpperCase;

/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */

class AbavoMapsGeocode extends AbstractTask {
	
	public const GEOCODE_LIMIT = 2500;	// Because of Google: https://developers.google.com/maps/articles/geocodestrat#quota-limits
	public const GEOCODE_SPEED = 12;	// Requests per second
	public const GEOCODE_DELAY = 1000;	// Milliseconds delay between request blocks
	
	/**
	 * Execute Task Function
	 *
	 * @return	boolean
	 */
	public function execute() {

		// Do action if task is not disabled
		if ( $this->disabled === FALSE ){
			
			// Make columns config
			$arrColumns = explode(',', $this->columns);
			if (is_array($arrColumns)){
			
				$columns = 'concat(`'.implode("`, ' ', `", $arrColumns).'`)';
				$conditions = ($this->force) ? "" : " AND latitude='' OR longitude='' ";
				
				$res = $GLOBALS['TYPO3_DB']->sql_query(
					"SELECT uid, $columns AS fulladdress FROM `{$this->table}` WHERE pid={$this->pid} $conditions LIMIT ".self::GEOCODE_LIMIT
				);
				
				if ((int) $res->num_rows > 0){
					
					// Init Geocode class
					$geocoder = new Geocode;
					
					// Gecode each record
					for ($i = 1; $record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res); ++$i) {

						// If the number of GEOCODE_SPEED is reached, sleep before requesting next records
						($i % self::GEOCODE_SPEED ==0) ? usleep(self::GEOCODE_DELAY*1000) : null;
						
						$gc = $geocoder->geoCodeAdress($record['fulladdress'], $this->gmapikey, $this->quotauser);
						($gc) ?	$GLOBALS['TYPO3_DB']->sql_query("UPDATE `{$this->table}` SET `latitude`='". (float) $gc['latitude'] . "', `longitude`='". (float) $gc['longitude'] . "' WHERE uid={$record['uid']}") : null;
					}
				}
				
				$return = true;
				
			}else{
				$return = false;
			}
			
		}else{
			$return = false;
		}
		
		// Mark scheduler as stoped an return result
		$this->stop();
		return $return;
	}
	
}
?>
