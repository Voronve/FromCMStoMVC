<div id="adminHeader">
    <h2>Widget News Admin</h2>
    <p>You are logged in as <b><?php echo $User->userName; ?></b>.
        <a href="admin.php?action=listArticles">Edit Articles</a> 
        <a href="admin.php?action=listCategories">Edit Categories</a> 
        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSLogin/logout')?>">Log Out</a>
    </p>
</div>
