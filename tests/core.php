<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Base Test Class
 *
 * All tests use this base test class to make life easier
 *
 * @author Austin Bischoff <austin@codebeard.com>
 */
class Tests_Core extends PHPUnit_Framework_TestCase
{
	/*
	 * Static Section
	 */

	/**
	 * Attempt to auto-load a class based on the name
	 *
	 * @param string $class
	 */
	public static function auto_load($class)
	{
		// Transform the class name into a path
		$file = str_replace('_', '/', strtolower($class));

		// Find the file and return the full path, if it exists
		if ($path = self::find_file($file))
		{
			// Load the class file
			require $path;

			// Class has been found
			return TRUE;
		}

		// Class is not in the filesystem
		return FALSE;
	}

	/**
	 * Try to find the file based on the class passed.
	 *
	 * @param string $file
	 */
	public static function find_file($file)
	{
		$found = FALSE; // By default we did not find anything

		// Create a partial path of the filename
		$path = GAMEQ_BASE.$file.'.php';

		// Is a file so we can include it
		if(is_file($path))
		{
			$found = $path;
		}

		return $found;
	}

	/*
	 * Dynamic Section
	 */

	/**
	 * Protocol name for the given test, if needed.  Allows us easier loading of the actual protocol
	 * class we want to test against.
	 *
	 * @var string
	 */
	protected $protocol = NULL;

	/**
	 * Dummy test for protocol parent class, prevents warnings and thus failures due to no tests.  Now
	 * all classes have at least one test, this test.
	 */
	public function test_dummy()
	{
		$this->assertTrue(1 == 1);
	}

	/**
	 * Simulate loading a status packet (or set of packets) for a specific protocol
	 */
	public function provider_status()
	{
		// Define the class name
		$class_name = 'GameQ_Protocols_'.$this->protocol;

		// Load up this class
		$class = new $class_name;

		// We fake the loading of packets by using load_provider_data
		$class->packetResponse(GameQ_Protocols::PACKET_STATUS, $this->load_provider_data(GameQ_Protocols::PACKET_STATUS));

		// Load up the class we need for the data side
		$reflection_class = new ReflectionClass($class_name);

		// Grab the method we need to invoke
		$method = $reflection_class->getMethod('process_status');

		// Set the method accessible for testing because it is normally protected
		$method->setAccessible(TRUE);

		// Now we need to parse the data, so run the method and then return
		return array(array($method->invokeArgs($class, array())));
	}

	/**
	 * Read in the data from flat files for the protocol and type specified
	 *
	 * @param string $type
	 */
	public function load_provider_data($type)
	{
		// Load up the class so we can get info about it
		$reflection = new ReflectionClass($this);

		// Transform the class name into a path, add missing parts to make it a full path to the data directory
		$file_dir = GAMEQ_BASE.str_replace('_', '/', strtolower($reflection->getName()))."/{$type}/";

		// Check to see if the directory exists
		if(!is_dir($file_dir) || !is_readable($file_dir))
		{
			$this->fail("Unable to find provider directory '{$file_dir}'");
		}

		// We init the response as an array, this is how the data us save as it is read from sockets
		$response_data = array();

		// Now let's open up the directory and read in the file(s)
		$dir = dir($file_dir);

		// Read until we run out of files
		while(($file = $dir->read()) !== FALSE)
		{
			// Make sure this is a file
			if(!is_file($dir->path.$file))
			{
				// Skip, we dont care about non-files
				continue;
			}

			// Make sure the file is readable, all files should be readable
			if(!is_readable($dir->path.$file))
			{
				$this->fail("Unable to read data file '{$dir->path}{$file}'");
			}

			// Load the file's info into the response array
			$response_data[] = file_get_contents($dir->path.$file);
		}

		// Close out the dir
		$dir->close();

		// Return the data
		return $response_data;
	}
}

// Define the autoload so we can require files easy
spl_autoload_register(array('Tests_Core', 'auto_load'));

// Load up the GameQ class, we will need this later
// We also want to use its constants, auto_load, etc...
require_once realpath(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'GameQ.php';

