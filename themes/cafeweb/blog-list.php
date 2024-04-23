
<article class="blog_article">
    <a title="<?= $blogPost->title; ?>" href="<?= url("/blog/{$blogPost->uri}"); ?>">
        <img title="<?= $blogPost->title; ?>" alt="<?= $blogPost->title; ?>" src="<?= image($blogPost->cover, 600, 340); ?>"/>
    </a>
    <header>
        <p class="meta">
            <?= $blogPost->getCategory()->title; ?> &bull;
            Por <?= "{$blogPost->getAuthor()->first_name} {$blogPost->getAuthor()->last_name};" ?> &bull;
            <?= dateFormatBR($blogPost->post_at) ?>
        </p>
        <h2><a title="<?= $blogPost->title; ?>" href="<?= url("/blog/{$blogPost->uri}"); ?>"><?= $blogPost->title; ?></a></h2>
        <p><a title="<?= strLimitChars($blogPost->subtitle, 120); ?>" href="<?= url("/blog/{$blogPost->uri}"); ?>">
                <?= strLimitChars($blogPost->subtitle, 120); ?>
            </a></p>
    </header>
</article>