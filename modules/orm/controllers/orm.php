<?php defined('SYSPATH') or die('No direct script access.');

class Orm_Controller extends Controller {

	function index()
	{
		$this->load->library('profiler');

		$user = new User_Model(1);

		print "user: ".Kohana::debug_output($user->newsletters(1));

		print Kohana::lang('core.stats_footer');
	}

}