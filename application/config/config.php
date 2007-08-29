<?php defined('SYSPATH') or die('No direct script access.');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Example: http://mysite.com/kohana/
|
*/
$config['base_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/kfsk/';

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = '';

$config['url_suffix'] = '.html';

$config['permitted_uri_chars'] = 'a-z 0-9~%.:_-';

$config['locale'] = 'en';

$config['include_paths'] = array
(
	'modules/user_guide'
);

$config['enable_hooks'] = FALSE;

$config['subclass_prefix'] = 'MY_';

$config['timezone'] = '';