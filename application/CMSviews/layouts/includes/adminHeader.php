<div id="adminHeader">
    <h2>Widget News Admin</h2>
    <p>You are logged in as <b><?php echo $User->userName; ?></b>.
        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/index')?>">Edit Articles</a> 
        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/listCategories')?>">Edit Categories</a>
		<a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/listSubcategories')?>">Edit Subcategories</a>
		<a href="<?= \ItForFree\SimpleMVC\Url::link('CMSAdmin/listUsers')?>">Edit Users</a>
        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSLogin/logout')?>">Log Out</a>
    </p>
</div>
