<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.redirect
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Plugin class for redirect handling.
 *
 * @since  1.6
 */
class PlgSystemBblock extends JPlugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  3.4
	 */

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(&$subject, $config)
	{
		$app = JFactory::getApplication();
		$params = json_decode($config['params']);
		if ($app->isAdmin()) {
			if (empty( $params->admin) ) {
				return;
			}
		} else {
			if (empty( $params->site )) {
				return;
			}
		}

        $ipInfo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
        $app = JFactory::getApplication();
        $app->set('bblock_countryCode', $ipInfo['geoplugin_countryCode']);
		parent::__construct($subject, $config);
		$countryCode = $ipInfo['geoplugin_countryCode'];

		/* First check the disable list */
		if (!empty( $params->disable_code_list )) {
			$list = explode (',', $params->disable_code_list);
			foreach( $list AS $item ) {
				if ($countryCode == $item) {
					if ($params->action_type == 0) {
						die();
					} else {
						JFactory::getApplication()->redirect($params->redirect_url);
					}
				}
			}
		}
		/* Now check the enable list */
		if (!empty( $params->disable_code_list )) {
			$list = explode (',', $params->disable_code_list);
			foreach( $list AS $item ) {
				if ($countryCode == $item) {
					return;
				}
			}
		}
		/* Not in block list BUT also not in the enable list - so block */
		if ($params->action_type == 0) {
			die();
		} else {
			JFactory::getApplication()->redirect($params->redirect_url);
		}
	}
}
