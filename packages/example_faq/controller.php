<?php

defined('C5_EXECUTE') or die(_("Access Denied."));

class ExampleFaqPackage extends Package {

	protected $pkgHandle = 'example_faq';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '1.1.2';

	public function getPackageDescription() {
		return t('Adds a simple FAQ system to a website. Used in the Example FAQ Single Page How-To.');
	}

	public function getPackageName() {
		return t('Example FAQ');
	}

	public function upgrade() {
		$pkg = parent::upgrade();
		Loader::model('single_page');

		$p = SinglePage::add('dashboard/example_faq',$pkg);
		if (is_object($p)) {
			$p->update(array('cName'=>'Example FAQ', 'cDescription'=>'Frequently asked questions.'));
		}

		$p = SinglePage::add('dashboard/example_faq/faq_entries',$pkg);
		if (is_object($p)) {
			$p->update(array('cName'=>'FAQ Entries'));
		}
	}

	public function install() {
		$pkg = parent::install();
		Loader::model('single_page');
		Loader::model('attribute/categories/collection');

		// install attributes
		$cab1 = CollectionAttributeKey::add('BOOLEAN',array('akHandle' => 'faq_section', 'akName' => t('FAQ Section'), 'akIsSearchable' => true), $pkg);
		$cab2 = CollectionAttributeKey::add('SELECT',array('akHandle' => 'faq_tags', 'akName' => t('FAQ Tags'), 'akSelectAllowMultipleValues' => true, 'akSelectAllowOtherValues' => true, 'akIsSearchable' => true), $pkg);

		$p = SinglePage::add('dashboard/example_faq',$pkg);
		if (is_object($p)) {
			$p->update(array('cName'=>'Example FAQ', 'cDescription'=>'Frequently asked questions.'));
		}

		$p = SinglePage::add('dashboard/example_faq/faq_entries',$pkg);
		if (is_object($p)) {
			$p->update(array('cName'=>'FAQ Entries'));
		}

	}
}