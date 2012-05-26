<?php
/**
 * Joomla! 1.5 component Coupon
 *
 * @version $Id: helper.php 2012-04-02 21:28:20 svn $
 * @author william.wen
 * @package Joomla
 * @subpackage Coupon
 * @license GNU/GPL
 *
 * Coupon
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Coupon Helper
 *
 * @package Joomla
 * @subpackage Coupon
 * @since 1.5
 */
class CSVHelper {
	function fields() {
		return array(
			'title' => 'Code',
			'cost' => 'Cost',
			'available' => 'Times',
			'used' => 'Used',
			'published' => 'Published'
		);		
	}
	
	function csvFileToArray($postName='file1', $fieldNames=array(), $hasTitle=true)
	{
		//setlocale(LC_ALL,array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
	    if($_FILES[$postName]['error'] == UPLOAD_ERR_NO_FILE) {
			return null;
	    }
		if (($handle = fopen($_FILES[$postName]['tmp_name'], "r")) !== FALSE) {
			# title
			if($hasTitle) {
				$titles = array();
				if (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
					for($i=0; $i<count($row); $i++) {
						$titles []= $row[$i];
					}
				}
			}
			
			if($fieldNames) {
				$titles = $fieldNames;
			}
			
			# data body
			$results = array();
	    	while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
	    		$temp = array();
	    		for($i=0; $i<count($titles); $i++) {
	    			$temp[$titles[$i]] = $row[$i];
	    		}
	    		$results []= $temp;
	    	}
	    	
	    	return $results;
	    }
	}
	
	/*
	 * @param array
	 * @return string csv data
	 */
	function arrayToCsv($data)
	{
		$buffer = fopen('php://temp', 'r+');
		foreach($data as $row) {
			fputcsv($buffer, $row);
		}
		rewind($buffer);
		$csv = stream_get_contents($buffer);
		fclose($buffer);
		return $csv;
	}
	
	/*
	 * @param array
	 */
	function importData($data)
	{
		$fields = self::fields();
		$arrData = self::csvFileToArray('file1', array_keys($fields));
    	if(!$arrData) {
    		return false;
    	}
		$dbo = JFactory::getDBO();
		foreach($arrData as $row) {
			$sql = sprintf("SELECT id FROM #__coupon WHERE title = '%s' LIMIT 1", $row['title']);
			
			$dbo->setQuery($sql);
			$id = $dbo->loadResult();
			if($id) {
				$tmpFields = array();
				foreach($row as $key => $field) {
					$tmpFields []= $key.'='.$dbo->Quote($field);
				}
				$sql = sprintf("UPDATE #__coupon SET %s WHERE id=%d", implode(', ', $tmpFields), $id);
				$dbo->Execute($sql);
			} else {
				$tmpFields = array();
				foreach($row as $key => $field) {
					$tmpFields []= $dbo->Quote($field);
				}
				$sql = sprintf("INSERT INTO #__coupon (%s,created)VALUES(%s,%s)", 
					implode(', ', array_keys($row)), 
					implode(', ', $tmpFields),
					'NOW()'
				);
				$dbo->Execute($sql);
			}
		}
	}
	
	/*
	 * @return 
	 */
	function exportData()
	{
		$fields = self::fields();
		$dbo = JFactory::getDBO();
		
		$sql = sprintf('SELECT %s FROM #__coupon', implode(', ', array_keys($fields)));
		$dbo->setQuery($sql);
		$results = $dbo->loadAssocList();
		$arrData = array();
		$arrData []= array_values($fields);
		foreach($results as $result) {
			$arrData []= array_values($result);
		}
		$csv = self::arrToCsv($arrData);
		self::downloadFile(self::saveToTempFile($csv));
	}
	
	/*
	 * @param string file data
	 * @return string file path
	 */
	function saveToTempFile($data)
	{
		$config =& JFactory::getConfig();
		$tmpPath = $config->getValue('config.tmp_path');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		if(!jfolder::exists($tmpPath)) {
			JError::raiseError( 20, 'Temp path no exits.');
	        jexit();
		}
		$date = JFactory::getDate();
		$filePath = JPath::clean($tmpPath . DS . $date->toUnix() . '.csv');
		JFile::write($filePath, $data);
		return $filePath;
	}
	
	function downloadFile($filename, $mimetype = 'application/octet-stream') {
		if (! file_exists ( $filename ) || ! is_readable ( $filename ))
			return false;
		$base = basename ( $filename );
		$base = str_replace ( ' ', '_', $base );
		$file_size = filesize ( $filename );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Content-Disposition: attachment; filename=report.csv" );
		header ( "Content-Length: " . $file_size );
		header ( "Accept-Ranges: bytes" );
		header ( "Content-Type: $mimetype" );
		header ( 'Content-Transfer-Encoding: binary' );
		$fp = fopen ( $filename, "r" );
		$buffer_size = 1024;
		$cur_pos = 0;
		while ( ! feof ( $fp ) && $file_size - $cur_pos > $buffer_size ) {
			$buffer = fread ( $fp, $buffer_size );
			echo $buffer;
			$cur_pos += $buffer_size;
		}
		$buffer = fread ( $fp, $file_size - $cur_pos );
		echo $buffer;
		fclose ( $fp );
		jexit ();
	}
}