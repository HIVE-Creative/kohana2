<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Text helper class.
 *
 * ###### Using the text helper:
 *
 *     // Using the text helper is simple:
 *     echo text::limit_words('limit this text to four words', 4);
 *
 *     // Output:
 *     limit this text to...
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2007-2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class text_Core {

	/**
	 * Limits a phrase to a given number of words. The third argument
	 * is an end character (it is preferred to use a numeric html entity for special
	 * characters); the default ending character is the ellipsis: "...".
	 *
	 * ##### Example
	 *
	 *     echo text::limit_words('cakephp wishes it were pie', 2);
	 *
	 *     // Output:
	 *     cakephp wishes...
	 *
	 * @param   string   $str       Phrase to limit words of
	 * @param   integer  $limit     Number of words to limit to
	 * @param   string   $end_char  End character or entity
	 * @return  string
	 */
	public static function limit_words($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		if (trim($str) === '')
			return $str;

		if ($limit <= 0)
			return $end_char;

		preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).(strlen($matches[0]) === strlen($str) ? '' : $end_char);
	}

	/**
	 * Limits a phrase to a given number of characters. The third argument
	 * is an end character (it is preferred to use a numeric html entity for special
	 * characters); the default ending character is the ellipsis: "...".
	 *
	 * Setting the fourth argument to (bool) TRUE will preserve words (if you want to
	 * limit *per* word, use limit_words() as both methods use different algorithms).
	 *
	 * ##### Example
	 *
	 *     echo text::limit_chars('cakephp wishes it were pie', 4);
	 *
	 *     // Output:
	 *     cake
	 *
	 * @param   string   $str               Phrase to limit characters of
	 * @param   integer  $limit             Number of characters to limit to
	 * @param   string   $end_char          End character or entity
	 * @param   boolean  $preserve_words    Enable or disable the preservation of words while limiting
	 * @return  string
	 */
	public static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' OR mb_strlen($str) <= $limit)
			return $str;

		if ($limit <= 0)
			return $end_char;

		if ($preserve_words == FALSE)
		{
			return rtrim(mb_substr($str, 0, $limit)).$end_char;
		}

		preg_match('/^.{'.($limit - 1).'}\S*/us', $str, $matches);

		return rtrim($matches[0]).(strlen($matches[0]) == strlen($str) ? '' : $end_char);
	}

	/**
	 * Alternates between two or more strings. This is useful for alternating
	 * the class attribute of rows in a table listing users.
	 *
	 * This method accepts a variable number of arguments.
	 *
	 * ##### Example
	 *
	 *     echo '<table>';
	 *
	 *     for($i=0; $i<4;$i++)
	 *     {
	 *         echo '<tr class="'.text::alternate('even', 'odd').'"><td>user'.$i.'</td></tr>';
	 *     }
	 *
	 *     echo '</table>';
	 *
	 *     // Output:
	 *     <table>
	 *       <tr class="even">
	 *         <td>user0</td>
	 *       </tr>
	 *       <tr class="odd">
	 *         <td>user1</td>
	 *       </tr>
	 *       <tr class="even">
	 *         <td>user2</td>
	 *       </tr>
	 *       <tr class="odd">
	 *         <td>user3</td>
	 *       </tr>
	 *     </table>
	 *
	 * @param   string  strings to alternate between
	 * @return  string
	 */
	public static function alternate()
	{
		static $i;

		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = func_get_args();
		return $args[($i++ % count($args))];
	}

	/**
	 * Generates a random string of a given type and length. Possible
	 * values for the first argument ($type) are:
	 *
	 *  - alnum    - alpha-numeric characters (including capitals)
	 *  - alpha    - alphabetical characters (including capitals)
	 *  - hexdec   - hexadecimal characters, 0-9 plus a-f
	 *  - numeric  - digit characters, 0-9
	 *  - nozero   - digit characters, 1-9
	 *  - distinct - clearly distinct alpha-numeric characters.
	 *
	 * For values that do not match any of the above, the characters passed
	 * in will be used.
	 *
	 * ##### Example
	 *
	 *     echo text::random('alpha', 20);
	 *
	 *     // Output:
	 *     DdyQFCddSKeTkfjCewPa
	 *
	 *     echo text::random('distinct', 20);
	 *
	 *     // Output:
	 *     XCDDVXV7FUSYAVXFFKSL
	 *
	 * @param   string   $type     A type of pool, or a string of characters to use as the pool
	 * @param   integer  $length   Length of string to return
	 * @return  string
	 */
	public static function random($type = 'alnum', $length = 8)
	{
		$utf8 = FALSE;

		switch ($type)
		{
			case 'alnum':
				$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'alpha':
				$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
			case 'hexdec':
				$pool = '0123456789abcdef';
			break;
			case 'numeric':
				$pool = '0123456789';
			break;
			case 'nozero':
				$pool = '123456789';
			break;
			case 'distinct':
				$pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
			break;
			default:
				$pool = (string) $type;
				$utf8 = ! text::is_ascii($pool);
			break;
		}

		// Split the pool into an array of characters
		$pool = ($utf8 === TRUE) ? utf8::str_split($pool, 1) : str_split($pool, 1);

		// Largest pool key
		$max = count($pool) - 1;

		$str = '';
		for ($i = 0; $i < $length; $i++)
		{
			// Select a random character from the pool and add it to the string
			$str .= $pool[mt_rand(0, $max)];
		}

		// Make sure alnum strings contain at least one letter and one digit
		if ($type === 'alnum' AND $length > 1)
		{
			if (ctype_alpha($str))
			{
				// Add a random digit
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(48, 57));
			}
			elseif (ctype_digit($str))
			{
				// Add a random letter
				$str[mt_rand(0, $length - 1)] = chr(mt_rand(65, 90));
			}
		}

		return $str;
	}

	/**
	 * Reduces multiple slashes in a string to single slashes.
	 *
	 * ##### Example
	 *
	 *     $str = 'path/to//something';
	 *     echo text::reduce_slashes($str);
	 *
	 *     // Output:
	 *     path/to/something
	 *
	 * @param   string  $str   String to reduce slashes of
	 * @return  string
	 */
	public static function reduce_slashes($str)
	{
		return preg_replace('#(?<!:)//+#', '/', $str);
	}

	/**
	 * Replaces the given words with a string. The second argument must be
	 * an array of words and the third argument is the character to use to
	 * replace the letters of the naughty word.
	 *
	 * ##### Example
	 *
	 *     $str         = 'The income tax is a three letter word, but telemarketers are dirtbags! No cookie for you!';
	 *     $replacement = '*';
	 *     $badwords    = array('tax', 'dirtbags');
	 *
	 *     echo text::censor($str, $badwords, $replacement, FALSE);
	 *
	 *     // Output:
	 *     The income *** is a three letter word, but telemarketers are *******. No cookie for you!
	 *
	 * @param   string   $replace_partial_words    Phrase to replace words in
	 * @param   array    $replacement              Words to replace
	 * @param   string   $badwords                 Replacement string
	 * @param   boolean  $str                      Replace words across word boundries (space, period, etc)
	 * @return  string
	 */
	public static function censor($str, $badwords, $replacement = '#', $replace_partial_words = TRUE)
	{
		foreach ((array) $badwords as $key => $badword)
		{
			$badwords[$key] = str_replace('\*', '\S*?', preg_quote((string) $badword));
		}

		$regex = '('.implode('|', $badwords).')';

		if ($replace_partial_words === FALSE)
		{
			// Just using \b isn't sufficient when we need to replace a badword that already contains word boundaries itself
			$regex = '(?<=\b|\s|^)'.$regex.'(?=\b|\s|$)';
		}

		$regex = '!'.$regex.'!ui';

		if (mb_strlen($replacement) == 1)
		{
			$regex .= 'e';
			return preg_replace($regex, 'str_repeat($replacement, mb_strlen(\'$1\'))', $str);
		}

		return preg_replace($regex, $replacement, $str);
	}

	/**
	 * Finds the intersecting word obetween a set of words.
	 *
	 * ##### Example
	 *
	 *     echo text::similar(array('cookies', 'cook'));
	 *
	 *     // Output:
	 *     cook
	 *
	 * @param   array   $words  Words to find similar text of
	 * @return  string
	 */
	public static function similar(array $words)
	{
		// First word is the word to match against
		$word = current($words);

		for ($i = 0, $max = strlen($word); $i < $max; ++$i)
		{
			foreach ($words as $w)
			{
				// Once a difference is found, break out of the loops
				if ( ! isset($w[$i]) OR $w[$i] !== $word[$i])
					break 2;
			}
		}

		// Return the similar text
		return substr($word, 0, $i);
	}

	/**
	 * An alternative to the php levenshtein() function that work out the
	 * distance between 2 words using the Damerau–Levenshtein algorithm.
	 * Credit: http://forums.devnetwork.net/viewtopic.php?f=50&t=89094
	 *
	 * ##### Example
	 *
	 *     $str_uno = 'baby';
	 *     $str_dos = 'cries';
	 *
	 *     echo text::distance($str_uno, $str_dos);
	 *
	 *     // Output:
	 *     5
	 *
	 * @see http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	 * @param     string    $string1  First word
	 * @param     string    $string2  Second word
	 * @return    int       Distance between words
	 */
	public static function distance($string1, $string2)
	{
		$string1_length = strlen($string1);
		$string2_length = strlen($string2);

		// Here we start building the table of values
		$matrix = array();

		// String1 length + 1 = rows.
		for ($i = 0; $i <= $string1_length; ++$i)
		{
			$matrix[$i][0] = $i;
		}

		// String2 length + 1 columns.
		for ($j = 0; $j <= $string2_length; ++$j)
		{
			$matrix[0][$j] = $j;
		}

		for ($i = 1; $i <= $string1_length; ++$i)
		{
			for ($j = 1; $j <= $string2_length; ++$j)
			{
				$cost = substr($string1, $i - 1, 1) == substr($string2, $j - 1, 1) ? 0 : 1;

				$matrix[$i][$j] = min(
					$matrix[$i - 1][$j] + 1,		// deletion
					$matrix[$i][$j - 1] + 1,		// insertion
					$matrix[$i - 1][$j - 1] + $cost	// substitution
				);

				if ($i > 1 && $j > 1 &&	(substr($string1, $i - 1, 1) == substr($string2, $j - 2, 1))
					&& (substr($string1, $i - 2, 1) == substr($string2, $j - 1, 1)))
				{
					$matrix[$i][$j] = min(
						$matrix[$i][$j],
						$matrix[$i - 2][$j - 2] + $cost	// transposition
					);
				}
			}
		}

		return $matrix[$string1_length][$string2_length];
	}

	/**
	 * Converts text anchors into links.
	 *
	 * ##### Example
	 *
	 *     echo text::auto_link_urls('http://example.com');
	 *
	 *     // Output:
	 *     <a href="http://example.com">http://example.com</a>
	 *
	 * @param   string   $text  Text to auto link
	 * @return  string
	 */
	public static function auto_link_urls($text)
	{

		$regex = '~\\b'
				.'((?:ht|f)tps?://)?' // protocol
				.'(?:[-a-zA-Z0-9]{1,63}\.)+' // host name
				.'(?:[0-9]{1,3}|aero|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)' // tlds
				.'(?:/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?' // path
				.'(?:\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?' // query
				.'(?:#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?' // fragment
				.'(?=[?.!,;:"]?(?:\s|$))~'; // punctuation and url end

		$result = "";
		$position = 0;

		while (preg_match($regex, $text, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($url, $url_pos) = $match[0];

			// Add the text before the url
			$result  .= substr($text, $position, $url_pos - $position);

			// Default to http://
			$full_url = empty($match[1][0]) ? 'http://'.$url : $url;

			// Add the hyperlink.
			$result .= html::anchor($full_url, $url);

			// New position to start parsing
			$position = $url_pos + strlen($url);
		}

		return $result.substr($text, $position);
	}

	/**
	 * Converts text email addresses into links.
	 *
	 * Note: this method uses html::mailto() to generate
	 * the link - mailto() produces an encoded mailto string!
	 *
	 * ##### Example
	 *
	 *     echo text::auto_link_emails('cyberspace_is_kewl@example.com');
	 *
	 *     // Output (friendly):
	 *     <a href="mailto:cyberspace_is_kewl@example.com">cyberspace_is_kewl@example.com</a>
	 *
	 * @param   string   $text  Text to auto link
	 * @return  string
	 */
	public static function auto_link_emails($text)
	{
		// Finds all email addresses that are not part of an existing html mailto anchor
		// Note: The "58;" negative lookbehind prevents matching of existing encoded html mailto anchors
		//       The html entity for a colon (:) is &#58; or &#058; or &#0058; etc.
		if (preg_match_all('~\b(?<!href="mailto:|">|58;)(?!\.)[-+_a-z0-9.]++(?<!\.)@(?![-.])[-a-z0-9.]+(?<!\.)\.[a-z]{2,6}\b~i', $text, $matches))
		{
			foreach ($matches[0] as $match)
			{
				// Replace each email with an encoded mailto
				$text = str_replace($match, html::mailto($match), $text);
			}
		}

		return $text;
	}

	/**
	 * Automatically applies <p> and <br /> markup to text. Basically nl2br() on steroids.
	 *
	 * ##### Example
	 *
	 *     $str = "This is just one break \n This deserves a paragragh \n\n Hookay, this is the...";
	 *     echo text::auto_link_emails('cyberspace_is_kewl@example.com');
	 *
	 *     // Output:
	 *     <p>This is just one break<br />
     *     This deserves a paragragh</p>
     *
     *     <p>Hookay, this is the...</p>
	 *
	 * @param   string   $str  Subject
	 * @param   boolean  $br   Convert single linebreaks to <br />
	 * @return  string
	 */
	public static function auto_p($str, $br = TRUE)
	{
		// Trim whitespace
		if (($str = trim($str)) === '')
			return '';

		// Standardize newlines
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		// Trim whitespace on each line
		$str = preg_replace('~^[ \t]+~m', '', $str);
		$str = preg_replace('~[ \t]+$~m', '', $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found = (strpos($str, '<') !== FALSE))
		{
			// Elements that should not be surrounded by p tags
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			// Put at least two linebreaks before and after $no_p elements
			$str = preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		// Do the <p> magic!
		$str = '<p>'.trim($str).'</p>';
		$str = preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found !== FALSE)
		{
			// Remove p tags around $no_p elements
			$str = preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		// Convert single linebreaks to <br />
		if ($br === TRUE)
		{
			$str = preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);
		}

		return $str;
	}

	/**
	 * Returns human readable sizes.
	 *
	 * Note: this is similar to the -h option in many Unix
	 * utilities (like "ls").
	 *
	 * ##### Example
	 *
	 *     echo text::bytes('2048');
	 *
	 *     // Output:
	 *     2.05 kB
	 *
	 *     echo text::bytes('4194304', 'kB');
	 *
	 *     // Output:
	 *     4194.30 kB
	 *
	 *     echo text::bytes('4194304', 'GiB');
	 *
	 *     // Output:
	 *     0.00 GiB
	 *
	 *     echo text::bytes('4194304', NULL, NULL, FALSE);
	 *
	 *     // Output:
	 *     4.00 MiB
	 *
	 * @see  Based on original functions written by:
	 * @see  Aidan Lister: http://aidanlister.com/repos/v/function.size_readable.php
	 * @see  Quentin Zervaas: http://www.phpriot.com/d/code/strings/filesize-format/
	 *
	 * @param   integer  $bytes       Size in bytes
	 * @param   string   $force_unit  A definitive unit
	 * @param   string   $format      The return string format
	 * @param   boolean  $si          Whether to use SI prefixes or IEC
	 * @return  string
	 */
	public static function bytes($bytes, $force_unit = NULL, $format = NULL, $si = TRUE)
	{
		// Format string
		$format = ($format === NULL) ? '%01.2f %s' : (string) $format;

		// IEC prefixes (binary)
		if ($si == FALSE OR strpos($force_unit, 'i') !== FALSE)
		{
			$units = array(__('B'), __('KiB'), __('MiB'), __('GiB'), __('TiB'), __('PiB'));
			$mod   = 1024;
		}
		// SI prefixes (decimal)
		else
		{
			$units = array(__('B'), __('kB'), __('MB'), __('GB'), __('TB'), __('PB'));
			$mod   = 1000;
		}

		// Determine unit to use
		if (($power = array_search((string) $force_unit, $units)) === FALSE)
		{
			$power = ($bytes > 0) ? floor(log($bytes, $mod)) : 0;
		}

		return sprintf($format, $bytes / pow($mod, $power), $units[$power]);
	}

	/**
	 * Prevents widow words by inserting a non-breaking space between the last two words.
	 * @see  http://www.shauninman.com/archive/2006/08/22/widont_wordpress_plugin
	 *
	 * ##### Example
	 *
	 *     $str = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cras id dolor. Donec ...";
	 *
	 *     // Output:
	 *     Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Cras id dolor. Donec&nbsp;...
	 *
	 * @param   string  $str  String to remove widows from
	 * @return  string
	 */
	public static function widont($str)
	{
		$str = rtrim($str);
		$space = strrpos($str, ' ');

		if ($space !== FALSE)
		{
			$str = substr($str, 0, $space).'&nbsp;'.substr($str, $space + 1);
		}

		return $str;
	}

	/**
	 * Tests whether a string contains only 7bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 * ##### Example
	 *
	 *     $str = 'abcd';
	 *     var_export(text::is_ascii($str));
	 *
	 *     // Output:
	 *     true
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  $str  String to check
	 * @return  bool
	 */
	public static function is_ascii($str)
	{
		return is_string($str) AND ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * ##### Example
	 *
	 *     $result = text::strip_ascii_ctrl($str_containing_control_codes);
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  $str  String to clean
	 * @return  string
	 */
	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 * For a description of the difference between 7bit and
	 * extended (8bit) ASCII:
	 *
	 * @see http://en.wikipedia.org/wiki/ASCII
	 *
	 * ##### Example
	 *
	 *     $result = text::strip_non_ascii($str_with_8bit_ascii);
	 *
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string  $str  String to clean
	 * @return  string
	 */
	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * ##### Example
	 *
	 *     echo text::transliterate_to_ascii("Útgarðar");
	 *
	 *     // Output:
	 *     Utgardhar
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 * @see http://sourceforge.net/projects/phputf8/
	 * @copyright  (c) 2007-2009 Kohana Team
	 * @copyright  (c) 2005 Harry Fuecks
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
	 *
	 * @param   string   $str    String to transliterate
	 * @param   integer  $case   -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
		static $UTF8_LOWER_ACCENTS = NULL;
		static $UTF8_UPPER_ACCENTS = NULL;

		if ($case <= 0)
		{
			if ($UTF8_LOWER_ACCENTS === NULL)
			{
				$UTF8_LOWER_ACCENTS = array(
					'à' => 'a',  'ô' => 'o',  'ď' => 'd',  'ḟ' => 'f',  'ë' => 'e',  'š' => 's',  'ơ' => 'o',
					'ß' => 'ss', 'ă' => 'a',  'ř' => 'r',  'ț' => 't',  'ň' => 'n',  'ā' => 'a',  'ķ' => 'k',
					'ŝ' => 's',  'ỳ' => 'y',  'ņ' => 'n',  'ĺ' => 'l',  'ħ' => 'h',  'ṗ' => 'p',  'ó' => 'o',
					'ú' => 'u',  'ě' => 'e',  'é' => 'e',  'ç' => 'c',  'ẁ' => 'w',  'ċ' => 'c',  'õ' => 'o',
					'ṡ' => 's',  'ø' => 'o',  'ģ' => 'g',  'ŧ' => 't',  'ș' => 's',  'ė' => 'e',  'ĉ' => 'c',
					'ś' => 's',  'î' => 'i',  'ű' => 'u',  'ć' => 'c',  'ę' => 'e',  'ŵ' => 'w',  'ṫ' => 't',
					'ū' => 'u',  'č' => 'c',  'ö' => 'o',  'è' => 'e',  'ŷ' => 'y',  'ą' => 'a',  'ł' => 'l',
					'ų' => 'u',  'ů' => 'u',  'ş' => 's',  'ğ' => 'g',  'ļ' => 'l',  'ƒ' => 'f',  'ž' => 'z',
					'ẃ' => 'w',  'ḃ' => 'b',  'å' => 'a',  'ì' => 'i',  'ï' => 'i',  'ḋ' => 'd',  'ť' => 't',
					'ŗ' => 'r',  'ä' => 'a',  'í' => 'i',  'ŕ' => 'r',  'ê' => 'e',  'ü' => 'u',  'ò' => 'o',
					'ē' => 'e',  'ñ' => 'n',  'ń' => 'n',  'ĥ' => 'h',  'ĝ' => 'g',  'đ' => 'd',  'ĵ' => 'j',
					'ÿ' => 'y',  'ũ' => 'u',  'ŭ' => 'u',  'ư' => 'u',  'ţ' => 't',  'ý' => 'y',  'ő' => 'o',
					'â' => 'a',  'ľ' => 'l',  'ẅ' => 'w',  'ż' => 'z',  'ī' => 'i',  'ã' => 'a',  'ġ' => 'g',
					'ṁ' => 'm',  'ō' => 'o',  'ĩ' => 'i',  'ù' => 'u',  'į' => 'i',  'ź' => 'z',  'á' => 'a',
					'û' => 'u',  'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u',  'ĕ' => 'e',  'ı' => 'i',
				);
			}

			$str = str_replace(
				array_keys($UTF8_LOWER_ACCENTS),
				array_values($UTF8_LOWER_ACCENTS),
				$str
			);
		}

		if ($case >= 0)
		{
			if ($UTF8_UPPER_ACCENTS === NULL)
			{
				$UTF8_UPPER_ACCENTS = array(
					'À' => 'A',  'Ô' => 'O',  'Ď' => 'D',  'Ḟ' => 'F',  'Ë' => 'E',  'Š' => 'S',  'Ơ' => 'O',
					'Ă' => 'A',  'Ř' => 'R',  'Ț' => 'T',  'Ň' => 'N',  'Ā' => 'A',  'Ķ' => 'K',  'Ĕ' => 'E',
					'Ŝ' => 'S',  'Ỳ' => 'Y',  'Ņ' => 'N',  'Ĺ' => 'L',  'Ħ' => 'H',  'Ṗ' => 'P',  'Ó' => 'O',
					'Ú' => 'U',  'Ě' => 'E',  'É' => 'E',  'Ç' => 'C',  'Ẁ' => 'W',  'Ċ' => 'C',  'Õ' => 'O',
					'Ṡ' => 'S',  'Ø' => 'O',  'Ģ' => 'G',  'Ŧ' => 'T',  'Ș' => 'S',  'Ė' => 'E',  'Ĉ' => 'C',
					'Ś' => 'S',  'Î' => 'I',  'Ű' => 'U',  'Ć' => 'C',  'Ę' => 'E',  'Ŵ' => 'W',  'Ṫ' => 'T',
					'Ū' => 'U',  'Č' => 'C',  'Ö' => 'O',  'È' => 'E',  'Ŷ' => 'Y',  'Ą' => 'A',  'Ł' => 'L',
					'Ų' => 'U',  'Ů' => 'U',  'Ş' => 'S',  'Ğ' => 'G',  'Ļ' => 'L',  'Ƒ' => 'F',  'Ž' => 'Z',
					'Ẃ' => 'W',  'Ḃ' => 'B',  'Å' => 'A',  'Ì' => 'I',  'Ï' => 'I',  'Ḋ' => 'D',  'Ť' => 'T',
					'Ŗ' => 'R',  'Ä' => 'A',  'Í' => 'I',  'Ŕ' => 'R',  'Ê' => 'E',  'Ü' => 'U',  'Ò' => 'O',
					'Ē' => 'E',  'Ñ' => 'N',  'Ń' => 'N',  'Ĥ' => 'H',  'Ĝ' => 'G',  'Đ' => 'D',  'Ĵ' => 'J',
					'Ÿ' => 'Y',  'Ũ' => 'U',  'Ŭ' => 'U',  'Ư' => 'U',  'Ţ' => 'T',  'Ý' => 'Y',  'Ő' => 'O',
					'Â' => 'A',  'Ľ' => 'L',  'Ẅ' => 'W',  'Ż' => 'Z',  'Ī' => 'I',  'Ã' => 'A',  'Ġ' => 'G',
					'Ṁ' => 'M',  'Ō' => 'O',  'Ĩ' => 'I',  'Ù' => 'U',  'Į' => 'I',  'Ź' => 'Z',  'Á' => 'A',
					'Û' => 'U',  'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae', 'İ' => 'I',
				);
			}

			$str = str_replace(
				array_keys($UTF8_UPPER_ACCENTS),
				array_values($UTF8_UPPER_ACCENTS),
				$str
			);
		}

		return $str;
	}

} // End text