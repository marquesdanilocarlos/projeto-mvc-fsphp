<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Models\Faq\Question;
use Source\Models\Post;
use Source\Support\Pager;
use stdClass;

class Web extends Controller
{
    public function __construct()
    {
        parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
    }

    public function home(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . ' - ' . CONF_SITE_TITLE,
            CONF_SITE_DESC,
            url(),
            theme('/assets/images/share.jpg')
        );

        $blogPosts = (new Post())
            ->find()
            ->order('post_at DESC')
            ->limit(6)
            ->fetch(true);

        echo $this->view->render('home', [
            'head' => $head,
            'video' => '1oL1TR4FiA4',
            'blogPosts' => $blogPosts,
        ]);
    }

    public function about(): void
    {
        $head = $this->seo->render(
            'Descubra o ' . CONF_SITE_NAME . ' - ' . CONF_SITE_DESC,
            CONF_SITE_DESC,
            url('/sobre'),
            theme('/assets/images/share.jpg')
        );

        $faq = (new Question())
            ->find('channel_id=:id', 'id=1', 'question, response')
            ->order('order_by')
            ->fetch(true);

        echo $this->view->render('about', [
            'head' => $head,
            'video' => '1oL1TR4FiA4',
            'faq' => $faq
        ]);
    }

    public function blog(?array $data): void
    {
        $head = $this->seo->render(
            'Blog - ' . CONF_SITE_NAME,
            'Confira em nosso blog dicas e sacadas de como controlar melhor suas contas. Vamos tomar um café?',
            url('/blog'),
            theme('/assets/images/share.jpg')
        );

        $blogPosts = (new Post())->find();

        $pager = new Pager(url('/blog/page/'));
        $pager->pager($blogPosts->count(), 9, $data['page'] ?? 1);

        $blogPosts = $blogPosts
            ->limit($pager->limit())
            ->offset($pager->offset())
            ->fetch(true);

        echo $this->view->render('blog', [
            'head' => $head,
            'paginator' => $pager->render(),
            'blogPosts' => $blogPosts,
        ]);
    }

    public function blogPost(array $data): void
    {
        $post = (new Post)

        $head = $this->seo->render(
            'Post Name - ' . CONF_SITE_NAME,
            'Post headline',
            url("/blog/{$postName}"),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('blog-post', [
            'head' => $head,
            'data' => $this->seo->getData()
        ]);
    }

    public function login(): void
    {
        $head = $this->seo->render(
            'Entrar - ' . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/entrar'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('auth-login', [
            'head' => $head,
        ]);
    }

    public function recover(): void
    {
        $head = $this->seo->render(
            'Recuperar senha - ' . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/recuperar'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('auth-forget', [
            'head' => $head,
        ]);
    }

    public function register(): void
    {
        $head = $this->seo->render(
            'Criar conta - ' . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/cadastrar'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('auth-register', [
            'head' => $head,
        ]);
    }

    public function terms(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . ' - Termos de uso',
            CONF_SITE_DESC,
            url('/termos'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('terms', [
            'head' => $head
        ]);
    }

    public function confirm(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . ' - Confirme seu cadastro',
            CONF_SITE_DESC,
            url('/confirma'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('optin-confirm', [
            'head' => $head
        ]);
    }

    public function success(): void
    {
        $head = $this->seo->render(
            CONF_SITE_NAME . ' - Bem vindo!',
            CONF_SITE_DESC,
            url('/sucesso'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('optin-success', [
            'head' => $head
        ]);
    }

    public function error(array $data): void
    {
        $error = new stdClass();

        switch ($data['errcode']) {
            case SERVICE_UNAVAILABLE_CODE:
                $error->code = SERVICE_UNAVAILABLE_CODE;
                $error->title = "Ooops... Estamos enfrentando problemas! :/";
                $error->message = "Parece que nosse serviço não está disponível no momento. Já estamos analisando o problema, mas caso precise, envie um e-mail.";
                $error->linkTitle = "Enviar e-mail!";
                $error->link = 'mailto:' . CONF_MAIL_SUPPORT;
                break;

            case SERVICE_MAINTANCE_CODE:
                $error->code = SERVICE_MAINTANCE_CODE;
                $error->title = "Desculpe, estamos em manutenção.";
                $error->message = "Voltamos logo! Por hora, estamos trabalhando para melhorar o nosso conteúdo.";
                $error->linkTitle = null;
                $error->link = null;
                break;

            default:
                $error->code = $data['errcode'];
                $error->title = "Ooops... Conteúdo indisponível! :/";
                $error->message = "Sentimos muito, mas o conteúdo que você tentou acessar não existe, está indisponível no momento, ou foi removido.";
                $error->linkTitle = "Continue navegando";
                $error->link = url_back();
        }


        $head = $this->seo->render(
            "{$error->code} | {$error->title}",
            $error->message,
            url("/ops/{$error->code}"),
            theme('/assets/images/share.jpg'),
            false
        );
        echo $this->view->render('error', [
            'head' => $head,
            'error' => $error
        ]);
    }
}