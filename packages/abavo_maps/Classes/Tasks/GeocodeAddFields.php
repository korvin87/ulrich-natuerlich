<?php

use TYPO3\CMS\Scheduler\AbstractAdditionalFieldProvider;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;
/*
 * abavo_maps
 *
 * @copyright   2014 abavo GmbH <dev@abavo.de>
 * @license     Proprietary
 */
class AbavoMapsGeocodeAddfields extends AbstractAdditionalFieldProvider
{
    public function getAdditionalFields(array &$taskInfo, $task, SchedulerModuleController $parentObject) {
   
   		// Make table selection
   		$res = $GLOBALS['TYPO3_DB']->sql_query("
			SELECT DISTINCT TABLE_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE COLUMN_NAME IN ('longitude','latitude')
			ORDER BY TABLE_NAME
		");
   		$tableSelections = ['' => '-- choose table --'];
   		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
   			$tableSelections[ $row['TABLE_NAME'] ] = $row['TABLE_NAME'];
   		}
   		
   		// Generate TaskInfo Array
   		$arrTaskInfo = ['table'	=> ['value' 		=>	$task->table, 'label'			=>	'<b>Use database table</b>', 'default'		=>	0, 'inputType'		=>	'select', 'inputValues'	=>	$tableSelections], 'columns' 		=> ['value' 		=>	$task->columns, 'label'			=>	'<b>Comma separated columns with address data</b>', 'default'		=>	'address,city,zip', 'inputType'		=>	'input'], 'pid'	=> ['value' 		=>	$task->pid, 'label'			=>	'<b>PID of records</b> <a href="https://developers.google.com/maps/articles/geocodestrat#quota-limits" target="_blank">(Google usage limits)</a>', 'default'		=>	0, 'inputType'		=>	'input'], 'force'	=> ['value' 		=>	$task->force, 'label'			=>	'<b>Force Update spite of existing geodata</b>', 'default'		=>	'0', 'inputType'		=>	'check'], 'gmapikey' 		=> ['value' 		=>	$task->gmapikey, 'label'			=>	'Optional: GoogleMap API Server Key<br><a href="https://developers.google.com/maps/documentation/javascript/tutorial?hl=de#api_key" target="_blank">(Create Key)</a>', 'default'		=>	'', 'inputType'		=>	'input'], 'quotauser' 		=> ['value' 		=>	$task->quotauser, 'label'			=>	'Optional: QuotaUser for <a href="https://console.developers.google.com" target="_blank">Developers Console</a> <br><i>best practice is google account email address</i>', 'default'		=>	'', 'inputType'		=>	'input']];
   		
   		$additionalFields = [];
   		foreach ($arrTaskInfo as $key => $value) {
   
   			// Set input field data
   			if (empty($taskInfo[$key])){
   				$taskInfo[$key] = ((string) $parentObject->getCurrentAction() == 'edit') ? $value['value'] : $value['default'];
   			}
   			
   			// Generate input fields		
   			if ($value['inputType'] == 'select'){
   				foreach ($value['inputValues'] as $dataKey => $dataValue){
   					$selected = ($value['value'] == $dataKey) ? 'selected="selected"' : '';
   					$typeOptions .='<option value="'.$dataKey.'" '.$selected.'>'.$dataValue.'</option>';
   				}
   				$fieldCode = '<select name="tx_scheduler['.$key.']" id="'.$key.'" style="width:332px">'.$typeOptions.'</select>';
   				unset ($typeOptions);
   				
   			}elseif ($value['inputType'] == 'input'){
   				$fieldCode = '<input type="text" name="tx_scheduler['.$key.']" id="'.$key.'" value="'.htmlspecialchars( $taskInfo[$key] ).'" size="50" />';
   			}elseif ($value['inputType'] == 'check'){
   
   				$checked = ($value['value'] == 1) ? 'checked="checked"' : '';
   				$fieldCode = '<input type="checkbox" name="tx_scheduler['.$key.']" id="'.$key.'" value="1" '.$checked.' />';
   			}
   
   			// Put in additionalFields Array
   			$additionalFields[$key] = ['code'     => $fieldCode, 'label'    => $value['label']];
   		}
   		return $additionalFields;
   	}
    public function validateAdditionalFields(array &$submittedData, SchedulerModuleController $parentObject) {
   		$submittedData['gmapikey']				= trim( strip_tags($submittedData['gmapikey']));
   		$submittedData['quotauser']				= trim( strip_tags($submittedData['quotauser']));
   		$submittedData['columns']			= trim( strip_tags($submittedData['columns']) );
   		$submittedData['pid']				= trim( strip_tags($submittedData['pid']) );
   		$submittedData['table']				= trim( strip_tags($submittedData['table']));
   		$submittedData['force']				= trim( strip_tags($submittedData['force']));
   		return true;
   	}
    public function saveAdditionalFields(array $submittedData, AbstractTask $task) {
   		$task->gmapikey				= $submittedData['gmapikey'];
   		$task->quotauser			= $submittedData['quotauser'];
   		$task->columns 				= $submittedData['columns'];
   		$task->pid					= $submittedData['pid'];
   		$task->table				= $submittedData['table'];
   		$task->force				= $submittedData['force'];
   	}
}
