	
	
	<?php    if (($this->controller->getTask() == 'update' || $this->controller->getTask() == 'edit' || $this->controller->getTask() == 'add')) { ?>
	
	<?php    
	$title = $this->controller->getTask() == 'add' ? t('Add') : t('Update');
	$df = Loader::helper('form/date_time');
	
	if (is_object($faq)) { 
		$faqTitle = $faq->getCollectionName();
		$faqDescription = $faq->getCollectionDescription();
		$faqDate = $faq->getCollectionDatePublic();
		$cParentID = $faq->getCollectionParentID();
		$ctID = $faq->getCollectionTypeID();
		$faqBody = '';
		$eb = $faq->getBlocks('Main');
		if (is_object($eb[0])) {
			$faqBody = $eb[0]->getInstance()->getContent();
		}
		$task = 'update';
		$buttonText = t('Update Entry');
	} else {
		$task = 'add';
		$buttonText = t('Add FAQ Entry');
	}
	
	?>
	
	<div style="width: 760px">
	
	<h1><span><?php   echo t('FAQs')?></span></h1>
	<div class="ccm-dashboard-inner">
	
	<h2><span><?php   echo $title?> FAQ Entry</span></h2>
	
	<form method="post" action="<?php   echo $this->action($task)?>" id="faq-form">
	<?php    if ($this->controller->getTask() != 'add') { ?>
		<?php   echo $form->hidden('faqID', $faq->getCollectionID())?>
	<?php    } ?>
	
	<strong><?php   echo $form->label('faqTitle', t('Question'))?></strong>
	<div><?php   echo $form->text('faqTitle', $faqTitle, array('style' => 'width: 730px'))?></div>
	<br/>
	<strong><?php   echo $form->label('faqDescription', t('Brief Answer'))?></strong>
	<div><?php   echo $form->textarea('faqDescription', $faqDescription, array('style' => 'width: 730px; height: 100px'))?></div>
	<br/>			
	<strong><?php   echo $form->label('cParentID', t('Section'))?></strong>
	<?php    if (count($sections) == 0) { ?>
		<div><?php   echo t('No sections defined. Please create a page with the attribute "faq_entry" set to true.')?></div>
	<?php    } else { ?>
		<div><?php   echo $form->select('cParentID', $sections, $cParentID)?></div>
	<?php    } ?>
<br/>
	
	<strong><?php   echo $form->label('ctID', t('Page Type'))?></strong>
	<div><?php   echo $form->select('ctID', $pageTypes, $ctID)?></div>
	<br/>
	<strong><?php   echo $form->label('faqDate', t('Date/Time'))?></strong>
	<div><?php   echo $df->datetime('faqDate', $faqDate)?></div>
	<br/>
	<strong><?php   echo t('Full Answer')?></strong>
	<?php    Loader::element('editor_init'); ?>
	<?php    Loader::element('editor_config'); ?>
	<?php    Loader::element('editor_controls', array('mode'=>'full')); ?>
	<?php   echo $form->textarea('faqBody', $faqBody, array('style' => 'width: 100%; height: 150px', 'class' => 'ccm-advanced-editor'))?>
	<br/>
	<?php    
	Loader::model("attribute/categories/collection");
	$akt = CollectionAttributeKey::getByHandle('faq_tags');
	if (is_object($faq)) {
		$tvalue = $faq->getAttributeValueObject($akt);
	}
	?>
	<div class="faq-attributes">
		<div>
			<strong><?php   echo $akt->render('label');?></strong>
			<?php   echo $akt->render('form', $tvalue, true);?>
		</div>
	</div>
	
	<br/>
	
	<?php   
	
	$ih = Loader::helper('concrete/interface');
	print $ih->button(t('Cancel'), $this->url('/dashboard/example_faq/'), 'left');
	print $ih->submit($buttonText, 'faq-form');
	?>
	<div class="ccm-spacer">&nbsp;</div>
	
	</form>
	
	</div>
	</div>
	
	<?php    } else { ?>
	
		<h1><span><?php   echo t('FAQs')?></span></h1>
		<div class="ccm-dashboard-inner">
		<h2><?php   echo t('New FAQ')?></h2>
		<a href="<?php   echo $this->action('add')?>"><?php   echo t('Click here to add a new FAQ Entry &gt;')?></a>
		<Br/><br/>
		
		<h2><?php   echo t('View/Search FAQs')?></h2>
	
		<form method="get" action="<?php   echo $this->action('view')?>">
		<?php   
		$sections[0] = '** All';
		asort($sections);
		?>
		
		<strong><?php   echo $form->label('cParentID', t('Section'))?></strong>
		<div><?php   echo $form->select('cParentID', $sections, $cParentID)?>
		<?php   echo $form->submit('submit', 'Search')?>
		</div>
		</form>
		<br/>
		<?php   
		$nh = Loader::helper('navigation');
		if ($faqList->getTotal() > 0) { 
			$faqList->displaySummary();
			?>
			
		<table border="0" class="ccm-results-list" cellspacing="0" cellpadding="0">
			<tr>
				<th class="<?php   echo $faqList->getSearchResultsClass('cvName')?>"><a href="<?php   echo $faqList->getSortByURL('cvName', 'asc')?>"><?php   echo t('Name')?></a></th>
				<th class="<?php   echo $faqList->getSearchResultsClass('cDateAdded')?>"><a href="<?php   echo $faqList->getSortByURL('cDateAdded', 'asc')?>"><?php   echo t('Date Added')?></a></th>
				<th class="<?php   echo $faqList->getSearchResultsClass('cvDatePublic')?>"><a href="<?php   echo $faqList->getSortByURL('cvDatePublic', 'asc')?>"><?php   echo t('Public Date')?></a></th>
				<th><?php   echo t('Page Owner')?></th>
				<th>&nbsp;</th>
			</tr>
			<?php   
			foreach($faqResults as $cobj) { ?>
			<tr>
				<td><a href="<?php   echo $nh->getLinkToCollection($cobj)?>"><?php   echo $cobj->getCollectionName()?></a></td>
				<td><?php   echo $cobj->getCollectionDateAdded()?></td>
				<td><?php   echo $cobj->getCollectionDatePublic()?></td>
				<td>
					<?php    
					$user = UserInfo::getByID($cobj->getCollectionUserID());
					print $user->getUserName();
					?>
				</td>
				<td><A href="<?php   echo $this->url('/dashboard/example_faq', 'edit', $cobj->getCollectionID())?>"><?php   echo t('Edit')?></a></td>
			</tr>
			<?php    } ?>
			
			</table>
			<br/>
			<?php   
			$faqList->displayPaging();
		} else {
			print t('No FAQ entries found.');
		}
		?>
		</div>
		
	<?php    }?>
