<?php   
	defined('C5_EXECUTE') or die(_("Access Denied."));
	// now that we're in the specialized content file for this block type, 
	// we'll include this block type's class, and pass the block to it, and get
	// the content
	

	if (count($cArray) > 0) { ?>
	
	<table border="0" cellspacing="0" cellpadding="0">
	<?php    foreach($cArray as $cobj) { ?>
	
	<tr id="f<?php   echo $cobj->getCollectionID()?>">
		<td valign="top"><div style="width: 20px; text-align: center"><strong>Q.</strong></div></td>
		<td valign="top" style="width: 100%"><a href="javascript:void(0)" onclick="toggleFAQEntry('<?php   echo $cobj->getCollectionID()?>')"><?php   echo $cobj->getCollectionName()?></a></td>
	</tr>
	<tr id="fa<?php   echo $cobj->getCollectionID()?>" style="display: none">
		<td valign="top"><div style="width: 20px; text-align: center"><strong style="color: #666">A.</strong></div></td>
		<td>
		<div class="faq-entry-description">
			<?php   echo $cobj->getCollectionDescription()?>
			<a href="<?php   echo $nh->getLinkToCollection($cobj)?>" style="font-weight: bold">Read More &gt;</a>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><br/></td>
	</tr>
		
	<?php    } ?>
	
	</table>
	
	<?php    } ?>
	
	
<script type="text/javascript">
toggleFAQEntry = function(item) {
	var desc = $('#fa' + item);
	if (desc.css('display') == 'none') {
		desc.fadeIn(200);
	} else {
		desc.hide();
	}
}
</script>