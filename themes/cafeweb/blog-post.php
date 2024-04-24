<?php $this->layout("_theme", ['head' => $head]); ?>

    <article class="post_page">
        <header class="post_page_header">
            <div class="post_page_hero">
                <h1><?= $blogPost->title; ?></h1>
                <img class="post_page_cover" alt="" title="" src="<?= image($blogPost->cover, 1280); ?>"/>
                <div class="post_page_meta">
                    <div class="author">
                        <div><img src="<?= image($blogPost->getAuthor()->photo, 200); ?>"/></div>
                        <div class="name">Por: <?= "{$blogPost->getAuthor()->first_name} {$blogPost->getAuthor()->lastName}"; ?></div>
                    </div>
                    <div class="date">Dia <?= dateFormatBR($blogPost->post_at); ?></div>
                </div>
            </div>
        </header>

        <div class="post_page_content">
            <div class="htmlchars">
                <h2><?= $blogPost->subtitle; ?></h2>
                <?= $blogPost->content; ?>
            </div>

            <aside class="social_share">
                <h3 class="social_share_title icon-heartbeat">Ajude seus amigos a controlar:</h3>
                <div class="social_share_medias">
                    <!--facebook-->
                    <div class="fb-share-button" data-href="<?= url("/post/{$blogPost->uri}"); ?>" data-layout="button_count"
                         data-size="large"
                         data-mobile-iframe="true">
                        <a target="_blank"
                           href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url("/post/{$blogPost->uri}")); ?>"
                           class="fb-xfbml-parse-ignore">Compartilhar</a>
                    </div>

                    <!--twitter-->
                    <a href="https://twitter.com/share?ref_src=site" class="twitter-share-button" data-size="large"
                       data-text="<?= $blogPost->title; ?>" data-url="<?= url("/post/{$blogPost->uri}"); ?>"
                       data-via="<?= str_replace("@", "", CONF_SOCIAL_TWITTER_CREATOR); ?>"
                       data-show-count="true">Tweet</a>
                </div>
            </aside>
        </div>

        <?php if(!empty($relatedPosts)): ?>
        <div class="post_page_related content">
            <section>
                <header class="post_page_related_header">
                    <h4>Veja também:</h4>
                    <p>Confira mais artigos relacionados e obtenha ainda mais dicas de controle para suas contas.</p>
                </header>

                <div class="blog_articles">
                    <?php foreach($relatedPosts as $post): ?>
                        <?php $this->insert("blog-list", ['blogPost' => $post]); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
        <?php endif; ?>
    </article>


<?php $this->start("scripts"); ?>
<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = 'https://connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v3.1&appId=267654637306249&autoLogAppEvents=1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<?php $this->end(); ?>