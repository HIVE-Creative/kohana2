<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Captcha driver for "basic" style.
 *
 * $Id$
 *
 * @package    Captcha
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Captcha_Basic_Driver extends Captcha_Driver {

	/**
	 * Generates a new Captcha challenge.
	 *
	 * @return  string  the challenge answer
	 */
	public function generate_challenge()
	{
		// Complexity setting is used as character count
		return text::random('distinct', max(1, Captcha::$config['complexity']));
	}

	/**
	 * Outputs the Captcha image.
	 *
	 * @param   boolean  html output
	 * @return  mixed
	 */
	public function render($html)
	{
		// Creates $this->image
		$this->image_create(Captcha::$config['background']);

		// Add a random gradient
		$color1 = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		$color2 = imagecolorallocate($this->image, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		$this->image_gradient($color1, $color2);

		// Add a few random lines
		for ($i = 0, $count = mt_rand(5, Captcha::$config['complexity'] * 3); $i < $count; $i++)
		{
			$color = imagecolorallocatealpha($this->image, mt_rand(100, 255), mt_rand(100, 255), mt_rand(100, 255), mt_rand(50, 120));
			imageline($this->image, mt_rand(0, Captcha::$config['width']), mt_rand(0, Captcha::$config['height']), mt_rand(0, Captcha::$config['width']), mt_rand(0, Captcha::$config['height']), $color);
		}

		// Calculate character font-size and spacing
		$default_size = min(Captcha::$config['width'], Captcha::$config['height'] * 2) / (strlen(Captcha::$response) + 1);
		$spacing = (int) (Captcha::$config['width'] * 0.9 / strlen(Captcha::$response));

		// Draw each Captcha character with varying attributes
		for ($i = 0, $strlen = strlen(Captcha::$response); $i < $strlen; $i++)
		{
			// Allocate random color, size and rotation attributes to text
			$color = imagecolorallocate($this->image, mt_rand(150, 255), mt_rand(200, 255), mt_rand(0, 255));
			$angle = mt_rand(-40, 20);

			// Scale the character size on image height
			$size = $default_size / 10 * mt_rand(8, 12);
			$box = imageftbbox($size, $angle, Captcha::$config['font'], Captcha::$response[$i]);

			// Calculate character starting coordinates
			$x = $spacing / 4 + $i * $spacing;
			$y = Captcha::$config['height'] / 2 + ($box[2] - $box[5]) / 4;

			// Write text character to image
			imagefttext($this->image, $size, $angle, $x, $y, $color, Captcha::$config['font'], Captcha::$response[$i]);
		}

		// Output
		return $this->image_render($html);
	}

} // End Captcha Basic Driver Class