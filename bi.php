<?php
/**
 *
 * @package Bi Plugin Editor Button
 *
 * @copyright   Copyright (C) 2005 - 2022 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Table\Extension;
use Joomla\Registry\Registry;
use Joomla\CMS\Document\Document;
use Joomla\CMS\Uri\Uri;


class PlgSystemBi extends CMSPlugin
{

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		
		if (!Factory::getApplication()->isClient('site'))
		{
			return;
		}
		
		if (Factory::getDocument()->getType() != 'html')
		{
			return false;
		}
		

		if (strpos($row->text, '{icon="') === false)
		{
			return false;
		}

		$pattern = '#\{icon="([a-z0-9]+.*?)(})?\"}#i';
		
		if (preg_match_all($pattern, $row->text, $matches))
		{
			if (Factory::getDocument()->getType() != 'html')
			{
				$text = preg_replace($pattern, '', $row->text);
				return true;
			}

			foreach ($matches[0] as $i => $fullMatch)
			{
				$attributes = trim($matches[2][$i]);
				if (strlen($attributes) && preg_match_all('#[a-z0-9_\-]+=".*?"#i', $attributes, $attributesMatches))
				{
					$data = array();

					foreach ($attributesMatches[0] as $pair)
					{
						list($attribute, $value) = explode('=', $pair, 2);

						$attribute  = trim(html_entity_decode($attribute));
						$value 		= html_entity_decode(trim($value, '"'));

						if (isset($data[$attribute]))
						{
							if (!is_array($data[$attribute]))
							{
								$data[$attribute] = (array) $data[$attribute];
							}

							$data[$attribute][] = $value;
						}
						else
						{
							$data[$attribute] = $value;
						}
					}

				}

				$output = $matches[1][$i];

				$icon__file = file_get_contents(Uri::base() . 'media/plg_bi/icons/' . $output . '.svg');
				$find__svg = '<svg';
				$position = strpos($icon__file, $find__svg);
				$set__icon = substr($icon__file, $position);
				$set__icon = str_replace('"16"', '"1em"', $set__icon);
				$svg = '<span class="bi__icon" data-icon="' . $output . '">' . $set__icon . '</span>';
				$row->text = str_replace($fullMatch, $svg, $row->text);

			}
		}

		return true;
		
	}

}
?>