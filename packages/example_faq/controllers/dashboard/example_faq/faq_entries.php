<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardExampleFaqFaqEntriesController extends Controller {

	public $helpers = array('html','form');

	public function on_start() {
		Loader::model('page_list');
		$this->error = Loader::helper('validation/error');
	}

	public function view() {
		$this->loadFAQSections();
		$faqList = new PageList();
		$faqList->sortBy('cDateAdded', 'desc');
		if (isset($_GET['cParentID']) && $_GET['cParentID'] > 0) {
			$faqList->filterByParentID($_GET['cParentID']);
		} else {
			$sections = $this->get('sections');
			$keys = array_keys($sections);
			$keys[] = -1;
			$faqList->filterByParentID($keys);
		}
		$this->set('faqList', $faqList);
		$this->set('faqResults', $faqList->getPage());
	}

	protected function loadFAQSections() {
		$faqSectionList = new PageList();
		$faqSectionList->filterByFaqSection(1);
		$faqSectionList->sortBy('cvName', 'asc');
		$tmpSections = $faqSectionList->get();
		$sections = array();
		foreach($tmpSections as $_c) {
			$sections[$_c->getCollectionID()] = $_c->getCollectionName();
		}
		$this->set('sections', $sections);
	}


	public function edit($cID) {
		$this->setupForm();
		$faq = Page::getByID($cID);
		$sections = $this->get('sections');
		if (in_array($faq->getCollectionParentID(), array_keys($sections))) {
			$this->set('faq', $faq);
		} else {
			$this->redirect('/dashboard/example_faq/faq_entries/');
		}
	}

	protected function setupForm() {
		$this->loadFAQSections();
		Loader::model("collection_types");
		$ctArray = CollectionType::getList('');
		$pageTypes = array();
		foreach($ctArray as $ct) {
			$pageTypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
		}
		$this->set('pageTypes', $pageTypes);
		$this->addHeaderItem(Loader::helper('html')->javascript('tiny_mce/tiny_mce.js'));
	}

	public function add() {
		$this->setupForm();
		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$parent = Page::getByID($this->post('cParentID'));
				$ct = CollectionType::getByID($this->post('ctID'));
				$data = array('cName' => $this->post('faqTitle'), 'cDescription' => $this->post('faqDescription'), 'cDatePublic' => Loader::helper('form/date_time')->translate('faqDate'));
				$p = $parent->add($ct, $data);
				$this->saveData($p);
				$this->redirect('/dashboard/example_faq/faq_entries/', 'faq_added');
			}
		}
	}

	public function update() {
		$this->edit($this->post('faqID'));

		if ($this->isPost()) {
			$this->validate();
			if (!$this->error->has()) {
				$p = Page::getByID($this->post('faqID'));
				$parent = Page::getByID($this->post('cParentID'));
				$ct = CollectionType::getByID($this->post('ctID'));
				$data = array('ctID' =>$ct->getCollectionTypeID(), 'cDescription' => $this->post('faqDescription'), 'cName' => $this->post('faqTitle'), 'cDatePublic' => Loader::helper('form/date_time')->translate('faqDate'));
				$p->update($data);
				if ($p->getCollectionParentID() != $parent->getCollectionID()) {
					$p->move($parent);
				}
				$this->saveData($p);
				$this->redirect('/dashboard/example_faq/faq_entries/', 'faq_updated');
			}
		}
	}

	protected function validate() {
		$vt = Loader::helper('validation/strings');
		$vn = Loader::Helper('validation/numbers');
		$dt = Loader::helper("form/date_time");
		if (!$vn->integer($this->post('cParentID'))) {
			$this->error->add(t('You must choose a parent page for this FAQ entry.'));
		}

		if (!$vn->integer($this->post('ctID'))) {
			$this->error->add(t('You must choose a page type for this FAQ entry.'));
		}

		if (!$vt->notempty($this->post('faqTitle'))) {
			$this->error->add(t('Title is required'));
		}

		if (!$this->error->has()) {
			Loader::model('collection_types');
			$ct = CollectionType::getByID($this->post('ctID'));
			$parent = Page::getByID($this->post('cParentID'));
			$parentPermissions = new Permissions($parent);
			if (!$parentPermissions->canAddSubCollection($ct)) {
				$this->error->add(t('You do not have permission to add a page of that type to that area of the site.'));
			}
		}
	}
	private function saveData($p) {
		$blocks = $p->getBlocks('Main');
		foreach($blocks as $b) {
			$b->deleteBlock();
		}

		$bt = BlockType::getByHandle('content');
		$data = array('content' => $this->post('faqBody'));
		$p->addBlock($bt, 'Main', $data);

		Loader::model("attribute/categories/collection");
		$cak = CollectionAttributeKey::getByHandle('faq_tags');
		$cak->saveAttributeForm($p);
	}


	public function faq_added() {
		$this->set('message', t('FAQ added.'));
		$this->view();
	}

	public function faq_updated() {
		$this->set('message', t('FAQ updated.'));
		$this->view();
	}

	public function on_before_render() {
		$this->set('error', $this->error);
	}

}