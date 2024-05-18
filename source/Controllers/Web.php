<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Models\Faq\Question;
use Source\Models\Post;
use Source\Models\User;
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
        $blogPost = (new Post())->findByUri($data['uri']);

        if (!$blogPost) {
            redirect('/404');
        }

        $blogPost->views += 1;
        $blogPost->save();

        $head = $this->seo->render(
            "{$blogPost->title} - " . CONF_SITE_NAME,
            $blogPost->subtitle,
            url("/blog/{$blogPost->uri}"),
            image($blogPost->cover, 1200, 628)
        );

        echo $this->view->render('blog-post', [
            'head' => $head,
            'blogPost' => $blogPost,
            'relatedPosts' => (new Post())
                ->find('category = :category AND id != :id', "category={$blogPost->category}&id={$blogPost->id}")
                ->order("rand()")
                ->limit(3)
                ->fetch(true)
        ]);
    }

    public function blogSearch(array $data): void
    {
        if (!empty($data['search'])) {
            $search = filter_var($data['search'], FILTER_SANITIZE_SPECIAL_CHARS);
            echo json_encode(['redirect' => url("/blog/buscar/{$search}/1")]);
            return;
        }

        if (empty($data['terms'])) {
            redirect('/blog');
        }

        $search = filter_var($data['terms'], FILTER_SANITIZE_SPECIAL_CHARS);
        $page = filter_var($data['page']) >= 1 ? $data['page'] : 1;

        $head = $this->seo->render(
            "Pesquisa por {$search} - " . CONF_SITE_NAME,
            "Confira os resultados de sua pesquisa por {$search}",
            url("/blog/buscar/{$search}/{$page}"),
            theme('/assets/images/share.jpg')
        );

        $blogPost = (new Post())->find("MATCH(title, subtitle) AGAINST(:search)", "search={$search}");
        $blogPostCount = $blogPost->count();

        if (!$blogPostCount) {
            echo $this->view->render('blog', [
                'head' => $head,
                'title' => "PESQUISA POR: ",
                'search' => $search
            ]);
            return;
        }

        $pager = new Pager(url("/blog/buscar/{$search}"));
        $pager->pager($blogPostCount, 9, $page);

        echo $this->view->render('blog', [
            'head' => $head,
            'title' => "PESQUISA POR: ",
            'search' => $search,
            'blogPosts' => $blogPost->limit($pager->limit())->offset($pager->offset())->fetch(true),
            'paginator' => $pager->render()
        ]);
    }

    public function login(?array $data): void
    {
        if (!empty($data['csrf_token'])) {
            if (!csrfVerify($data)) {
                $json['message'] = $this->message->error('Erro ao enviar, favor use o formulário correto.')->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['email'] || empty($data['password']))) {
                $json['message'] = $this->message->warning('Informe o e-mail e senha.')->render();
                echo json_encode($json);
                return;
            }

            $save = !empty($data['save']) ? true : false;
            $auth = new Auth();
            $login = $auth->login($data['email'], $data['password'], $save);

            if (!$login) {
                $json['message'] = $auth->getMessage()->render();
                echo json_encode($json);
                return;
            }

            $json['redirect'] = url('/app');
            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
            'Entrar - ' . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/entrar'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('auth-login', [
            'head' => $head,
            'cookie' => filter_input(INPUT_COOKIE, 'authEmail')
        ]);
    }

    public function recover(?array $data): void
    {
        if (!empty($data['csrf_token'])) {
            if (!csrfVerify($data)) {
                $json['message'] = $this->message->error('Erro ao enviar, favor use o formulário correto.')->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['email'])) {
                $json['message'] = $this->message->error('Informe o e-mail.')->render();
                echo json_encode($json);
                return;
            }

            $auth = new Auth();
            if (!$auth->recover($data['email'])) {
                $json['message'] = $auth->getMessage()->render();
                echo json_encode($json);
                return;
            }

            $json['message'] = $this->message->success('Acesse seu e-mail para recuperar a senha.')->render();
            echo json_encode($json);
            return;
        }

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

    public function reset(array $data): void
    {
        if (!empty($data['csrf_token'])) {
            if (!csrfVerify($data)) {
                $json['message'] = $this->message->error('Erro ao enviar, favor use o formulário correto.')->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['password'] || empty($data['password_repeat']))) {
                $json['message'] = $this->message->warning('Informe e repita a senha para continuar')->render();
                echo json_encode($json);
                return;
            }

            list($email, $code) = explode('|', $data['code']);
            $auth = new Auth();

            if (!$auth->reset($email, $code, $data['password'], $data['password_repeat'])) {
                $json['message'] = $auth->getMessage()->render();
                echo json_encode($json);
                return;
            }

            $this->message->success('Senha alterada com sucesso!')->flash();
            $json['redirect'] = url('/entrar');
            echo json_encode($json);
            return;
        }

        $head = $this->seo->render(
            'Crie sua nova senha no ' . CONF_SITE_NAME,
            CONF_SITE_DESC,
            url('/recuperar'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('auth-reset', [
            'head' => $head,
            'code' => $data['code']
        ]);
    }

    public function register(?array $data): void
    {
        if (!empty($data['csrf_token'])) {
            if (!csrfVerify($data)) {
                $json['message'] = $this->message->error('Erro ao enviar, favor use o formulário correto')->render();
                echo json_encode($json);
                return;
            }

            if (in_array('', $data)) {
                $json['message'] = $this->message->info('Informe seus dados corretamente')->render();
                echo json_encode($json);
                return;
            }

            $auth = new Auth();
            $user = new User();
            $user->bootstrap(
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['password']
            );

            if (!$auth->register($user)) {
                $json['message'] = $auth->getMessage()->render();
                echo json_encode($json);
                return;
            }

            $json['redirect'] = url('/confirma');
            echo json_encode($json);
            return;
        }

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

        echo $this->view->render('optin', [
            'head' => $head,
            'data' => (object)[
                'title' => 'Falta pouco! Confirme seu cadastro.',
                'desc' => 'Enviamos um link de confirmação para seu e-mail. Acesse e siga as instruções para concluir seu cadastro
                e comece a controlar com o CaféControl.',
                'image' => theme("/assets/images/optin-confirm.jpg")
            ]
        ]);
    }

    public function success(array $data): void
    {
        $email = base64_decode($data['email']);
        $user = (new User())->findByEmail($email);

        if ($user && $user->status !== 'confirmed') {
            $user->status = 'confirmed';
            $user->save();
        }


        $head = $this->seo->render(
            CONF_SITE_NAME . ' - Bem vindo!',
            CONF_SITE_DESC,
            url('/sucesso'),
            theme('/assets/images/share.jpg')
        );

        echo $this->view->render('optin', [
            'head' => $head,
            'data' => (object)[
                'title' => 'Tudo pronto. Você já pode controlar! :)',
                'desc' => 'Bem-vindo(a) ao seu controle de contas, vamos tomar um café?',
                'image' => theme("/assets/images/optin-success.jpg"),
                'link' => url('/entrar'),
                'linkTitle' => 'Fazer Login'
            ]
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