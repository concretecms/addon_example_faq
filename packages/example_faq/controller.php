<?php   

defined('C5_EXECUTE') or die(_("Access Denied."));

class ExampleFaqPackage extends Package {

	protected $pkgHandle = 'example_faq';
	protected $appVersionRequired = '5.4.0';
	protected $pkgVersion = '1.1.1';
	
	public function getPackageDescription() {
		return t('Adds a simple FAQ system to a website. Used in the Example FAQ Single Page How-To.');
	}
	
	public function getPackageName() {
		return t('Example FAQ');
	}
	
	public function install() {
		$pkg = parent::install();
		Loader::model('single_page');
		Loader::model('attribute/categories/collection');
		
		// install attributes
		$cab1 = CollectionAttributeKey::add('BOOLEAN',array('akHandle' => 'faq_section', 'akName' => t('FAQ Section'), 'akIsSearchable' => true), $pkg);
		$cab2 = CollectionAttributeKey::add('SELECT',array('akHandle' => 'faq_tags', 'akName' => t('FAQ Tags'), 'akSelectAllowMultipleValues' => true, 'akSelectAllowOtherValues' => true, 'akIsSearchable' => true), $pkg);

		$def = SinglePage::add('/dashboard/example_faq', $pkg);
		$def->update(array('cName'=>'FAQ Entries', 'cDescription'=>'Frequently asked questions.'));
	}
}