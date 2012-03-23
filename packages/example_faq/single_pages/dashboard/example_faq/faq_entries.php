<?php defined('C5_EXECUTE') or die('Access Denied');
$form = loader::helper('form');
if ($this->controller->getTask() == 'update' || $this->controller->getTask() == 'edit' || $this->controller->getTask() == 'add') {

	$title = $this->controller->getTask() == 'add' ? t('Add') : t('Update');
	$df = Loader::helper('form/date_time');

	$task = 'add';
	$buttonText = t('Add FAQ Entry');

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
		$buttonText = t('Update FAQ Entry');
	}
	echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($buttonText, false, false, false);

	?>
		<form method="post" action="<?=$this->action($task)?>" id="faq-form">
			<div class="ccm-pane-body">
					<fieldset>
					<?php
						if ($this->controller->getTask() != 'add') {
							echo $form->hidden('faqID', $faq->getCollectionID());
						}
					?>
				<div class="clearfix">
					<?=$form->label('faqTitle', t('Question'))?>
					<div class="input">
						<?=$form->text('faqTitle', $faqTitle, array('style' => 'width: 730px'))?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('faqDescription', t('Brief Answer'))?>
					<div class="input">
						<?=$form->textarea('faqDescription', $faqDescription, array('style' => 'width: 730px; height: 100px'))?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('cParentID', t('Section'))?>
					<div class="input">
						<?php
							if (count($sections) == 0) {
								echo "<div>".t('No sections defined. Please create a page with the attribute "faq_entry" set to true.')."</div>";
							} else {
								echo "<div>".$form->select('cParentID', $sections, $cParentID)."</div>";
							}
						?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('ctID', t('Page Type'))?>
					<div class="input">
						<?=$form->select('ctID', $pageTypes, $ctID)?>
					</div>
				</div>
				<div class="clearfix">
					<?=$form->label('faqDate', t('Date/Time'))?>
					<div class="input">
						<?=$df->datetime('faqDate', $faqDate)?>
					</div>
				</div>

					<strong><?=t('Full Answer')?></strong>
					<?php Loader::element('editor_init') ?>
					<?php Loader::element('editor_config') ?>
					<?php Loader::element('editor_controls', array('mode'=>'full')) ?>
					<?=$form->textarea('faqBody', $faqBody, array('style' => 'width: 100%; height: 150px', 'class' => 'ccm-advanced-editor'))?>

					<br/>

					<?php
						Loader::model("attribute/categories/collection");
						$akt = CollectionAttributeKey::getByHandle('faq_tags');
						if (is_object($faq)) {
							$tvalue = $faq->getAttributeValueObject($akt);
						}
					?>


					<div class="faq-attributes clearfix">
						<?=$akt->render('label') ?>
						<div class="input">
							<?=$akt->render('form', $tvalue, true) ?>
						</div>
					</div>

					<br/>
					<div class="ccm-spacer">&nbsp;</div>


			</div>
			<div class='ccm-pane-footer'>
				<?php
					$ih = Loader::helper('concrete/interface');
					print $ih->button(t('Cancel'), $this->url('/dashboard/example_faq/faq_entries'), 'left');
					print $ih->submit($buttonText, 'faq-form','right','primary');
				?>
			</div>
		</form>
	</div>

<?php
} else {
	echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('FAQs'), false, false, false);
	?>
	<div class="ccm-pane-body">
		<h2><?=t('New FAQ')?></h2>
		<a href="<?=$this->action('add')?>"><?=t('Click here to add a new FAQ Entry &gt;')?></a>
		<Br/><br/>

		<h2><?=t('View/Search FAQs')?></h2>

		<form method="get" action="<?=$this->action('view')?>">
			<?php
				$sections[0] = '** All';
				asort($sections);
			?>

			<strong><?=$form->label('cParentID', t('Section'))?></strong>
			<div>
				<?=$form->select('cParentID', $sections, $cParentID)?>
				<?=$form->submit('submit', 'Search')?>
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
						<th class="<?=$faqList->getSearchResultsClass('cvName')?>"><a href="<?=$faqList->getSortByURL('cvName', 'asc')?>"><?=t('Name')?></a></th>
						<th class="<?=$faqList->getSearchResultsClass('cDateAdded')?>"><a href="<?=$faqList->getSortByURL('cDateAdded', 'asc')?>"><?=t('Date Added')?></a></th>
						<th class="<?=$faqList->getSearchResultsClass('cvDatePublic')?>"><a href="<?=$faqList->getSortByURL('cvDatePublic', 'asc')?>"><?=t('Public Date')?></a></th>
						<th><?=t('Page Owner')?></th>
						<th>&nbsp;</th>
					</tr>
					<?php
					foreach($faqResults as $cobj) { ?>
						<tr>
							<td><a href="<?=$nh->getLinkToCollection($cobj)?>"><?=$cobj->getCollectionName()?></a></td>
							<td><?=$cobj->getCollectionDateAdded()?></td>
							<td><?=$cobj->getCollectionDatePublic()?></td>
							<td>
								<?php
									$user = UserInfo::getByID($cobj->getCollectionUserID());
									print $user->getUserName();
								?>
							</td>
							<td><A href="<?=$this->url('/dashboard/example_faq/faq_entries', 'edit', $cobj->getCollectionID())?>"><?=t('Edit')?></a></td>
						</tr>
					<?php
					}
					?>

				</table>
				<br/>
				<?php
				$faqList->displayPaging();
			} else {
				print t('No FAQ entries found.');
			}
	?>
	</div>

<?php } ?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>