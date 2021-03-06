
<ul id="headlines">
    <?php foreach ($results['articles'] as $article) { 
		if ($article->active){?>
        <li class='<?php echo $article->id?>'>
            <h2>
                <span class="pubDate">
                    <?php echo date('j F', $article->publicationDate)?>
                </span>
                
                <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>">
                    <?php echo htmlspecialchars( $article->title )?>
                </a>
                
                <?php if (isset($article->subcategoryId)) { ?>
                    <span class="category">
                        in category
                        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/archiveCat')?>&amp;subcategoryId=<?php echo $article->subcategoryId?>">
                            <?php echo htmlspecialchars($results['categories'][$article->subcategoryId]->name )?>
                        </a>
                    </span>
					<span class="category">
                        in subcategory
                        <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/archiveSubcat')?>&amp;subcategoryId=<?php echo $article->subcategoryId?>">
                            <?php echo htmlspecialchars($results['subcategories'][$article->subcategoryId]->name )?>
                        </a>
                    </span>
                <?php } 
                else { ?>
                    <span class="category">
                        <?php echo "Без cубкатегории"?>
                    </span>
                <?php } ?>
            </h2>
            <p class="summary"><?php echo htmlspecialchars($article->summary)?></p>
			<p class="content"><?php echo htmlspecialchars ($article->content) ?></p>
            <img id="loader-identity" src="JS/ajax-loader.gif" alt="gif">
            
            <ul class="ajax-load">
                <li><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>" class="ajaxArticleBodyByPost" data-contentId="<?php echo $article->id?>">Показать продолжение (POST)</a></li>
                <li><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>" class="ajaxArticleBodyByGet" data-contentId="<?php echo $article->id?>">Показать продолжение (GET)</a></li>
                <li><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>" class="">(POST) -- NEW</a></li>
                <li><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>" class="">(GET)  -- NEW</a></li>
            </ul>
            <a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/viewArticle')?>&amp;articleId=<?php echo $article->id?>" class="showContent" data-contentId="<?php echo $article->id?>">Показать полностью</a>
        </li>
	<?php }}?>
    </ul>
    <p><a href="<?= \ItForFree\SimpleMVC\Url::link('CMSHomepage/archive')?>">Article Archive</a></p>

    
