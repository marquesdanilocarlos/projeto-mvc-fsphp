<?php $this->layout("_theme", ['head' => $head]); ?>

<section class="blog_page">
    <header class="blog_page_header">
        <h1><?= $title ?? "BLOG" ?></h1>
        <p><?= $search ?? $desc ?? 'Confira nossas dicas para controlar melhor suas contas' ?></p>
        <form name="search" action="<?= url("/blog/buscar"); ?>" method="post" enctype="multipart/form-data">
            <label>
                <input type="text" name="search" placeholder="Encontre um artigo:" required/>
                <button class="icon-search icon-notext"></button>
            </label>
        </form>
    </header>

    <?php if(empty($blogPosts) && !empty($search)): ?>
        <div class="content content">
            <div class="empty_content">
                <img class="empty_content_cover" title="Empty Content" alt="Empty Content"
                     src="<?= theme("/assets/images/empty-content.jpg"); ?>"/>
                <h3 class="empty_content_title">Sua pesquisa não retornou nenhum resultado. :/</h3>
                <p class="empty_content_desc">Você pesquisou por <strong><?= $search; ?></strong>, tente outros termos...</p>
                <a href="<?= url("/blog"); ?>" title="Blog"
                   class="empty_content_btn gradient gradient-green gradient-hover radius">Volte ao blog.</a>
            </div>
        </div>
    <?php elseif(empty($blogPosts)): ?>
        <div class="content content">
            <div class="empty_content">
                <img class="empty_content_cover" title="Empty Content" alt="Empty Content"
                     src="<?= theme("/assets/images/empty-content.jpg"); ?>"/>
                <h3 class="empty_content_title">Ooops, não temos conteúdo aqui. :/</h3>
                <p class="empty_content_desc">Ainda estamos trabalhando, em breve teremos novidades para você. :)</p>
                <a href="<?= url("/blog"); ?>" title="Blog"
                   class="empty_content_btn gradient gradient-green gradient-hover radius">Voltar ao blog</a>
            </div>
        </div>
    <?php else: ?>
        <!--BLOG-->
        <div class="blog_content container content">
            <div class="blog_articles">
                <?php foreach ($blogPosts as $blogPost): ?>
                    <?php $this->insert("blog-list", ['blogPost' => $blogPost]); ?>
                <?php endforeach; ?>
            </div>

            <?= $paginator; ?>
        </div>
    <?php endif; ?>
</section>