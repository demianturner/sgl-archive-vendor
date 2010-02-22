<?php
/**
 * HTML/JavaScript Generation Helper
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.1
 */

/**
 * HTML/JavaScript Generation Helper
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.1
 * @link       http://pear.php.net/package/HTML_AJAX
 */
class HTML_AJAX_Helper 
{
	/**
	 * URL where an HTML_AJAX_Server instance is serving up clients and taking ajax requests
	 */
	var $serverUrl = 'server.php';

	/**
	 * JS libraries to include
	 *
	 * @var	array
	 */
	var $jsLibraries = array('Util','Main','Request','HttpClient','Dispatcher','Behavior','Loading','JSON','iframe');

	/**
	 * Remote class stubs to include
	 */
	var $stubs = array();

	/**
	 * Include all needed libraries, stubs, and set defaultServer
	 *
	 * @return	string
	 */
	function setupAJAX() 
	{
		$libs = array(0=>array());
		foreach($this->jsLibraries as $library) {
			if (is_array($library)) {
				$libs[] = implode(',',$library);
			}
			else {
				$libs[0][] = $library;
			}
		}
		$libs[0] = implode(',',$libs[0]);

		$ret = '';
		foreach($libs as $list) {
			$ret .= "<script type='text/javascript' src='{$this->serverUrl}?client={$list}'></script>\n";
		}

		if (count($this->stubs) > 0) {
			$stubs = implode(',',$this->stubs);
			$ret .= "<script type='text/javascript' src='{$this->serverUrl}?stub={$stubs}'></script>\n";
		}
		$ret .= $this->encloseInScript('HTML_AJAX.defaultServerUrl = '.$this->escape($this->serverUrl));
		return $ret;
	}

	/**
	 * Create a custom Loading message
	 *
	 * @param string	$body	HTML body of the loading div
	 * @param string	$class	CSS class of the div
	 * @param string	$style	style tag of the loading div
	 */
	function loadingMessage($body, $class = 'HTML_AJAX_Loading', 
			$style = 'position: absolute; top: 0; right: 0; background-color: red; width: 80px; padding: 4px; display: none') 
	{
		return "<div id='HTML_AJAX_LOADING' class='{$class}' style=\"{$style}\">{$body}</div>\n";
	}

	/**
	 * Update the contents of an element using ajax
	 *
	 * @param string	$id	id of the element to update
	 * @param string|array	$update	Either a url to update with or a array like array('class','method')
	 * @param string	$type	replace or append
	 * @param boolean	$enclose
	 */
	function updateElement($id, $update, $type, $enclose = false) {
		if (is_array($update)) {
			$updateStr = "";
			$comma = '';
			foreach($update as $item) {
				$updateStr .= $comma.$this->escape($item);
				$comma = ',';
			}
		}
		else {
			$updateStr = $this->escape($update);
		}

		$ret = "HTML_AJAX.{$type}(".$this->escape($id).",{$updateStr});\n";
		if ($enclose) {
			$ret = $this->encloseInScript($ret);
		}
		return $ret;
	}

	/**
	 * Escape a string and add quotes allowing it to be a javascript paramater
	 *
	 * @param string	$input
	 * @return string
	 * @todo do something here besides a quick hack
	 */
	function escape($input) {
		return "'".addslashes($input)."'";
	}

	/**
	 * Enclose a string in a script block
	 *
	 * @param string	$input
	 * @return string
	 */
	function encloseInScript($input) {
		return '<script type="text/javascript">'.$input."</script>\n";
	}

	/**
	 * Generate a JSON String
	 *
	 * @param string	$input
	 * @return string
	 */
	function jsonEncode($input) {
		require_once 'HTML/AJAX/Serializer/JSON.php';

		$s = new HTML_AJAX_Serializer_JSON();
		return $s->serialize($input);
	}
}
?>
