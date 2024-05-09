<?php

use Source\Core\Connection;
use Source\Core\Message;
use Source\Core\Session;
use Source\Models\User;
use Source\Support\Thumb;

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

/**
 * @param string $email
 * @return bool
 */
function isEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $password
 * @return bool
 */
function isPassword(string $password): bool
{
    return password_get_info($password)['algo']
        || (mb_strlen($password) >= CONF_PASS_MIN_LENGTH && mb_strlen($password) <= CONF_PASS_MAX_LENGTH);
}


/**
 * ##################
 * ###   STRING   ###
 * ##################
 */

/**
 * @param string $slug
 * @return string
 */
function strSlug(string $string): string
{
    $slug = strip_tags($string);
    $slug = preg_replace('~[^\pL\d]+~u', '-', $slug);
    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
    $slug = preg_replace('~[^-\w]+~', '', $slug);
    $slug = trim($slug, '-');
    $slug = preg_replace('~-+~', '-', $slug);
    $slug = strtolower($slug);

    if (empty($slug)) {
        return '';
    }

    return $slug;
}

/**
 * @param string $string
 * @return string
 */
function strStudlyCase(string $string): string
{
    $studlyCase = strSlug($string);
    return str_replace(
        ' ',
        '',
        mb_convert_case(
            str_replace('-', ' ', $studlyCase),
            MB_CASE_TITLE
        )
    );
}

/**
 * @param string $string
 * @return string
 */
function strCamelCase(string $string): string
{
    return lcfirst(strStudlyCase($string));
}

/**
 * @param string $string
 * @return string
 */
function strTitle(string $string): string
{
    $string = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    return mb_convert_case($string, MB_CASE_TITLE);
}

/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function strLimitWords(string $string, int $limit, string $pointer = '...'): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $arrWords = explode(" ", $string);
    $numWords = count($arrWords);

    if ($numWords < $limit) {
        return $string;
    }

    $words = implode(" ", array_slice($arrWords, 0, $limit));

    return "{$words} {$pointer}";
}

/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function strLimitChars(string $string, int $limit, string $pointer = '...'): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    if (mb_strlen($string) < $limit) {
        return $string;
    }

    $string = mb_substr($string, 0, $limit);
    $chars = mb_strrpos($string, " ");
    $string = mb_substr($string, 0, $chars);

    return "{$string} {$pointer}";
}

/**
 * ###############
 * ###   URL   ###
 * ###############
 */

/**
 * @param string $path
 * @return string
 */
function url(?string $path = null): string
{
    $completePath = $path && $path[0] === '/' ? mb_substr($path, 1) : $path;

    if (strpos($_SERVER['HTTP_HOST'], '.local')) {
        if ($path) {
            return CONF_URL_TEST . '/' . $completePath;
        }

        return CONF_URL_TEST;
    }

    if ($path) {
        return CONF_URL_BASE . '/' . $completePath;
    }

    return CONF_URL_BASE;
}

/**
 * @return string
 */
function url_back(): string
{
    return $_SERVER['HTTP_REFERER'] ?? url();
}

/**
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    header('HTTP/1.1 302 Redirect');
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, 'route', FILTER_DEFAULT) !== $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}


/**
 * ###############
 * ###   CORE   ###
 * ###############
 */

/**
 * @return PDO
 */
function db(): PDO
{
    return Connection::getInstance();
}

function message(): Message
{
    return new Message();
}

function session(): Session
{
    return new Session();
}

function flash(): ?string
{
    $session = new Session();

    if ($flash = $session->flash()) {
        echo $flash;
    }

    return null;
}


/**
 * ###############
 * ###  MODEL  ###
 * ###############
 */
function user(): User
{
    return new User();
}


/**
 * ##################
 * ###  PASSWORD  ###
 * ##################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }

    return password_hash($password, CONF_PASS_ALGO, CONF_PASS_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwdVerify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwdRehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASS_ALGO, CONF_PASS_OPTION);
}

function csrfInput(): string
{
    session()->csrf();
    $token = session()->csrf_token ?? '';
    return "<input type='hidden' name='csrf_token' value='{$token}'/>";
}

function csrfVerify(array $request): bool
{
    $sessionToken = session()->csrf_token;
    $requestToken = $request['csrf_token'];

    if (empty($sessionToken) || empty($requestToken) || $requestToken != $sessionToken) {
        return false;
    }

    return true;
}

/**
 * ##################
 * ####   DATE   ####
 * ##################
 */

function dateFromat(string $date = 'now', string $format = CONF_DATE_BR): string
{
    return (new DateTime($date))->format($format);
}


function dateFormatBR(string $date = 'now'): string
{
    return (new DateTime($date))->format(CONF_DATE_BR);
}

function dateFormatDefault(string $date = 'now'): string
{
    return (new DateTime())->format(CONF_DATE_APP);
}

/**
 * ####################
 * ####   ASSETS   ####
 * ####################
 */

/**
 * @param string|null $path
 * @return string
 */
function theme(string $path = null): string
{
    $completePath = $path && $path[0] === '/' ? mb_substr($path, 1) : $path;

    if (strpos($_SERVER['HTTP_HOST'], '.local')) {
        $themeUrl = CONF_URL_TEST . '/themes/' . CONF_VIEW_THEME;

        if ($path) {
            return "{$themeUrl}/{$completePath}";
        }

        return $themeUrl;
    }

    $themeUrl = CONF_URL_BASE . '/themes/' . CONF_VIEW_THEME;

    if ($path) {
        return "{$themeUrl}/{$completePath}";
    }

    return $themeUrl;
}

function image(string $image, int $width, int $height = null)
{
    return url() . '/' . (new Thumb())->make($image, $width, $height);
}