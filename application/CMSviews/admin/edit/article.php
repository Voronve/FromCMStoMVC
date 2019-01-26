
<h1><?php echo $results['pageTitle'] ?></h1>

<?php if (!isset($results['article']->id)){ ?>
	<form action="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/newArticle')?>" method="post">
<?php }else{ ?> 
	<form action="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/editArticle')?>" method="post">
		<input type="hidden" name="articleId" value="<?php echo $results['article']->id ?>">
<?php } ?> 
	<?php if (isset($results['errorMessage'])) { ?>
		<div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
	<?php } ?>
	<ul>
		<li>
			<label for="title">Article Title</label>
			<input type="text" name="title" id="title" placeholder="Name of the article" required autofocus maxlength="255" value="<?php echo htmlspecialchars($results['article']->title) ?>" />
		</li>

		<li>
			<label for="summary">Article Summary</label>
			<textarea name="summary" id="summary" placeholder="Brief description of the article" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars($results['article']->summary) ?></textarea>
		</li>

		<li>
			<label for="content">Article Content</label>
			<textarea name="content" id="content" placeholder="The HTML content of the article" required maxlength="100000" style="height: 30em;"><?php echo htmlspecialchars($results['article']->content) ?></textarea>
		</li>

		<li>
			<label for="categoryId">Article Category</label>
			<select name="categoryId">
				<?php foreach ($results['categories'] as $category) { ?>
					<option value="<?php echo $category->id ?>"<?php echo ( $category->id == $results['categoryIdCompare'] ) ? " selected" : "" ?>><?php echo htmlspecialchars($category->name) ?></option>
				<?php } ?>
			</select>
		</li>
		<li>
			<label for="subcategoryId">Article Subcategory</label>
			<select name="subcategoryId">
				<?php foreach ($results['subcategories'] as $subcategory) { ?>
					<option value="<?php echo $subcategory->id ?>"<?php echo( $subcategory->id == $results['article']->subcategoryId ) ? " selected" : "" ?>><?php echo htmlspecialchars($subcategory->name) ?></option>
				<?php } ?>
			</select>
		</li>
		<li>
			<label for="authorsId[]">Authors names</label>
			<select name="authorsId[]" multiple="">
				<?php foreach ($results['users'] as $user) { ?>
					<option value="<?php echo $user->id ?>"<?php foreach($results['authors'] as $author){ echo $user->id == $author ? " selected" : ""; } ?>><?php echo htmlspecialchars($user->name) ?></option>
				<?php } ?>
			</select>
		</li>
		<li>
			<label for="publicationDate">Publication Date</label>
			<input type="date" name="publicationDate" id="publicationDate" placeholder="YYYY-MM-DD" required maxlength="10" value="<?php echo $results['article']->publicationDate ? date("Y-m-d", $results['article']->publicationDate) : "" ?>" />
		</li>

		<li>
			<label for="active" style="line-height: 0;">Activeness</label> 
			<input style="width: auto;" type="checkbox" value="1" name="active" <?php
			if ($results['article']->active) {
				echo 'checked';
			}
			?>>	  
		</li>

	</ul>

	<div class="buttons">
		<input type="submit" name="saveChanges" value="Save Changes" />
		<input type="submit" formnovalidate name="cancel" value="Cancel" />
	</div>

</form>

<?php if ($results['article']->id) { ?>
	<p><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/deleteArticle')?>&amp;articleId=<?php echo $results['article']->id ?>" onclick="return confirm('Delete This Article?')">
			Delete This Article
		</a>
	</p>
<?php } ?>

