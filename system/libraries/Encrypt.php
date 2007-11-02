<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Class: Encrypt
 *
 * Kohana Source Code:
 *  author    - Kohana Team
 *  copyright - Copyright (c) 2007 Kohana Team
 *  license   - <http://kohanaphp.com/license.html>
 */
class Encrypt_Core {

	// mcrypt module handle
	protected $module;

	/*
	 * Method: __construct
	 *  Initializes mcrypt
	 */
	public function __construct($config = array())
	{
		if ( ! defined('MCRYPT_ENCRYPT'))
			throw new Kohana_Exception('encrypt.requires_mcrypt');

		// Set config
		$config += Config::item('encryption');

		if (empty($config['key']))
			throw new Kohana_Exception('encrypt.no_encryption_key');

		// TODO: Handle modes other than "ECB"
		($config['mode'] === MCRYPT_MODE_ECB) or die('Only ECB mode is currently supported for encryption');

		// Open the encryption module
		$this->module = mcrypt_module_open($config['cipher'], '', $config['mode'], '');

		// Different random seeds must be used for Windows and UNIX
		$rand = (strpos(PHP_OS, 'WIN') !== FALSE) ? MCRYPT_RAND : MCRYPT_DEV_RANDOM;

		// Create an initialization vector
		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->module), $rand);

		// Hash the key, for security, and trim it to the expected key length
		$config['key'] = substr(hash('sha256', $config['key']), 0, mcrypt_enc_get_key_size($this->module));

		// Initialize the module with the key and IV
		mcrypt_generic_init($this->module, $config['key'], $iv);

		Log::add('debug', 'Encrypt Library initialized');
	}

	/*
	 * Method: __destruct
	 *  Unload mcrypt when PHP shuts down
	 */
	public function __destruct()
	{
		// Shut down mcrypt
		mcrypt_generic_deinit($this->module);

		// Close the module
		mcrypt_module_close($this->module);
	}

	/*
	 * Method: encode
	 *  Encrypts a string.
	 *
	 * Parameters:
	 *  data - string to be encrypted
	 *
	 * Returns:
	 *  Encrypted string.
	 */
	public function encode($data)
	{
		return base64_encode(mcrypt_generic($this->module, $data));
	}

	/*
	 * Method: decode
	 *  Decrypts an encrypted string.
	 *
	 * Parameters:
	 *  data - string to be decrypted
	 *
	 * Returns:
	 *  Plain-text string.
	 */
	public function decode($data)
	{
		return rtrim(mdecrypt_generic($this->module, base64_decode($data)), "\0");
	}

} // End Encrypt