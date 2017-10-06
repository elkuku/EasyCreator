<?php
/**
 * Part of the Joomla Framework Github Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

//namespace Joomla\Github;

//use Joomla\Registry\Registry;

/**
 * GitHub API package class for the Joomla Framework.
 *
 * @since  1.0
 */
abstract class EcrGithubPackage extends EcrGithubObject
{
	/**
	 * Constructor.
	 *
	 * @param   Registry  $options  GitHub options object.
	 * @param   Http      $client   The HTTP client object.
	 *
	 * @since   1.0
	 */
	public function __construct(JRegistry $options = null, JHttp $client = null)
	{
		parent::__construct($options, $client);

        $this->package = str_replace('EcrGithubPackage', '', get_class($this));
	}

	/**
	 * Magic method to lazily create API objects
	 *
	 * @param   string  $name  Name of property to retrieve
	 *
	 * @since   1.0
	 * @throws \InvalidArgumentException
	 *
	 * @return  AbstractPackage  GitHub API package object.
	 */
	public function __get($name)
	{
		$class = 'EcrGithubPackage' . $this->package . ucfirst($name);

		if (false == class_exists($class))
		{
			throw new \InvalidArgumentException(
				sprintf(
					'Argument %1$s produced an invalid class name: %2$s in package %3$s',
					$name, $class, $this->package
				)
			);
		}

		if (false == isset($this->$name))
		{
			$this->$name = new $class($this->options, $this->client);
		}

		return $this->$name;
	}
}
