<?php

/**
 * DATABASE
 */

const CONF_DB_HOST = "db";
const CONF_DB_USER = "root";
const CONF_DB_PASS = "a654321";
const CONF_DB_NAME = "mvc-fsphp";

/**
 * URLs
 */
const CONF_URL_BASE = "http://www.localhost/fsphp/";
const CONF_URL_ADMIN = "/admin";
const CONF_URL_ERROR = CONF_URL_BASE . "/404";

/**
 * DATES
 */
const CONF_DATE_BR = "d/m/Y H:i:s";
const CONF_DATE_APP = "Y-m-d H:i:s";

/**
 * MESSAGE
 */
const CONF_MESSAGE_CLASS = 'trigger';
const CONF_MESSAGE_INFO = 'info';
const CONF_MESSAGE_SUCCESS = 'success';
const CONF_MESSAGE_WARNING = 'warning';
const CONF_MESSAGE_ERROR = 'error';


/**
 * PASSWORD
 */
const CONF_PASS_MIN_LENGTH = 8;
const CONF_PASS_MAX_LENGTH = 40;

const CONF_PASS_ALGO = PASSWORD_DEFAULT;
const CONF_PASS_OPTION = ['cost' => 10];

/**
 * MAIL
 */
const CONF_MAIL_HOST = '172.17.0.1';
const CONF_MAIL_PORT = 2025;
const CONF_MAIL_USER = '';
const CONF_MAIL_PASS = '';
const CONF_MAIL_SENDER_EMAIL = 'marquesdanilocarlos@gmail.com';
const CONF_MAIL_SENDER_NAME = 'Danilo';
const CONF_MAIL_OPTION_LANG = 'br';
const CONF_MAIL_OPTION_HTML = true;
const CONF_MAIL_OPTION_AUTH = false;
const CONF_MAIL_OPTION_SECURE = 'tls';
const CONF_MAIL_OPTION_CHARSET = 'utf-8';

/**
 * VIEW
 */
const CONF_VIEW_PATH = __DIR__ . "/../../assets/views";
const CONF_VIEW_EXT = "php";

/**
 * UPLOAD
 */
const CONF_UPLOAD_DIR = '../storage';
const CONF_UPLOAD_IMG_DIR = 'image';
const CONF_UPLOAD_FILE_DIR = 'file';
const CONF_UPLOAD_MEDIA_DIR = 'media';

/**
 * IMAGE
 */
const CONF_IMG_CACHE = CONF_UPLOAD_DIR . '/' . CONF_UPLOAD_IMG_DIR . '/cache';
const CONF_IMG_SIZE = 2000;
const CONF_IMG_QUALITY = ['jpg' => 75, 'png' => 5];

/**
 * SITE
 */
const CONF_SITE_NAME = 'FSPHP';
const CONF_SITE_LANG = 'pt_BR';
const CONF_SITE_DOMAIN = 'fsphp.com.br';

/**
 * SOCIAL
 */
const CONF_SOCIAL_TWITTER_CREATOR = '@robsonvleite';
const CONF_SOCIAL_TWITTER_PUBLISHER = '@robsonvleite';
const CONF_SOCIAL_FACEBOOK_APP = '356464859317524';
const CONF_SOCIAL_FACEBOOK_AUTHOR = 'marquesdanilocarlos';
const CONF_SOCIAL_FACEBOOK_PAGE = 'marquesdanilocarlos';

