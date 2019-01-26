<h1>Article Subcategories</h1>

<?php if (isset($results['errorMessage'])) { ?>
	<div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
<?php } ?>


<?php if (isset($results['statusMessage'])) { ?>
	<div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
<?php } ?>

<table>
	<tr>
		<th>Subcategory name</th>
		<th>Category name</th>
	</tr>

	<?php foreach ($results['subcategories'] as $subcategory) { ?>

		<tr onclick="location = '<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/editSubcategory')?>&amp;subcategoryId=<?php echo $subcategory->id ?>'">
			<td>
				<?php echo $subcategory->name ?>
			</td>
			<td>
				<?php echo $subcategory->cat_name ?>
			</td>
		</tr>

	<?php } ?>

</table>

<p><?php echo $results['totalRows'] ?> categor<?php echo ( $results['totalRows'] != 1 ) ? 'ies' : 'y' ?> in total.</p>

<p><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/newSubcategory')?>">Add a New Subcategory</a></p>

