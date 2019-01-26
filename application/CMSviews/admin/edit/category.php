<h1><?php echo $results['pageTitle']?></h1>
		<?php if (!isset($results['category']->id)){ ?>
			<form action="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/newCategory')?>" method="post">
		<?php }else{ ?> 
			<form action="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/editCategory')?>" method="post">
				<input type="hidden" name="categoryId" value="<?php echo $results['category']->id ?>"/>
		<?php } ?> 
          <!-- Обработка формы будет направлена файлу admin.php ф-ции newCategory либо editCategory в зависимости от formAction, сохранённого в result-е -->

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>

        <ul>

          <li>
            <label for="name">Category Name</label>
            <input type="text" name="name" id="name" placeholder="Name of the category" required autofocus maxlength="255" value="<?php echo htmlspecialchars( $results['category']->name )?>" />
          </li>

          <li>
            <label for="description">Description</label>
            <textarea name="description" id="description" placeholder="Brief description of the category" required maxlength="1000" style="height: 5em;"><?php echo htmlspecialchars( $results['category']->description )?></textarea>
          </li>

        </ul>

        <div class="buttons">
          <input type="submit" name="saveChanges" value="Save Changes" />
          <input type="submit" formnovalidate name="cancel" value="Cancel" />
        </div>

      </form>

    <?php if ( $results['category']->id ) { ?>
          <p><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/deleteCategory')?>&amp;categoryId=<?php echo $results['category']->id ?>" onclick="return confirm('Delete This Category?')">Delete This Category</a></p>
    <?php } ?>

