<?php defined('C5_EXECUTE') or die("Access Denied.");

class DashboardExampleFaqController extends Controller {

	public function __construct() {
		$this->redirect('/dashboard/example_faq/faq_entries');
	}

}
