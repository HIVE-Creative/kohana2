<?php defined('SYSPATH') or die('No direct script access.');

class PDODB_Core extends PDO {

	// Database-specific driver
	protected $driver;

	/**
	 * Creates a new PDO connection and loads the driver.
	 *
	 * @param   array  configuration
	 * @return  void
	 */
	public function __construct(array $config)
	{
		// All keys must be set
		$config += array('charset' => 'utf8', 'driver' => '', 'hostname' => '', 'database' => '', 'persistent' => FALSE);

		// Construct the PDO DSN
		$dsn = $config['driver'].':host='.$config['hostname'].';dbname='.$config['database'];

		// Driver-specific PDO options
		$driver = array();

		if ($config['persistent'] === TRUE)
		{
			// Turn on persistent connections
			$driver[PDO::ATTR_PERSISTENT] = TRUE;
		}

		// Connect to the database
		parent::__construct($dsn, $config['username'], $config['password'], $driver);

		// Enable exceptions for errors
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Set the character set of the connection
		$this->exec('SET NAMES '.$this->quote($config['charset']));

		// Get the driver name
		$driver = 'PDODB_'.ucfirst(strtolower($this->getAttribute(PDO::ATTR_DRIVER_NAME))).'_Driver';

		// Load the driver
		$this->driver = call_user_func(array($driver, 'instance'));
	}

	/**
	 * Returns a database-specific LIMIT statement.
	 *
	 * @param   integer  limit
	 * @param   integer  offset
	 * @return  string
	 */
	public function limit($limit, $offset = NULL)
	{
		return $this->driver->limit($limit, $offset);
	}

	/**
	 * Quotes the value of a identifier.
	 *
	 * @param   integer  offset
	 * @param   integer  limit
	 * @return  string
	 */
	public function quote_identifier($str)
	{
		return $this->driver->quote_identifier($str);
	}

	/**
	 * Quotes the value of a row. Integers are type casted, arrays are
	 * made into a string of comma-separated values, strings are quoted
	 * using the PDO quote() method, and NULL is made into a string value.
	 *
	 * @param   mixed   value to quote
	 * @return  mixed
	 */
	public function quote($str)
	{
		if ($str === NULL)
		{
			// Make the value into a string for SQL
			return 'NULL';
		}
		elseif (is_array($str))
		{
			$array = array();
			foreach ($str as $s)
			{
				// Quote the value
				$array[] = $this->quote($s);
			}
			return '('.implode(', ', $array).')';
		}
		elseif (is_int($str) OR ctype_digit($str))
		{
			// No quoting for integers
			return (int) $str;
		}
		else
		{
			// Quote the value
			return parent::quote($str);
		}
	}

} // End PDODB