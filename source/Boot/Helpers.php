<?php

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

 /**
  * is_email
  *
  * @param  mixed $email
  * @return bool
  */
function is_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * is_passwd
 *
 * @param  mixed $passwd
 * @return bool
 */
function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN) {
        return true;
    }
    return false;
}

/**
 * passwd
 *
 * @param  mixed $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }
    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * passwd_verify
 *
 * @param  mixed $password
 * @param  mixed $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * passwd_rehash
 *
 * @param  mixed $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}


/**
 * ###################
 * ###   REQUEST   ###
 * ###################
 */

/**
 * csrf_input
 *
 * @return string
 */
function csrf_input(): string
{
    session()->csrf();
    return "<input type='hidden' name='csrf' value='" . (session()->csrf_token ?? "") . "'/>"; 
}

/**
 * csrf_verify
 *
 * @param  mixed $request
 * @return bool
 */
function csrf_verify($request): bool
{
    if (empty(session()->csrf_token) || empty($request['csrf']) || $request['csrf'] != session()->csrf_token) {
        return false;
    }
    return true;
}

/**
 * flash
 *
 * @return string
 */
function flash(): ?string
{
    $session = new \Source\Core\Session();
    if ($flash = $session->flash()) {
        echo $flash;
    }

    return null;
}

/**
 * request_limit
 *
 * @param  mixed $key
 * @param  mixed $limit
 * @param  mixed $seconds
 * @return bool
 */
function request_limit(string $key, int $limit, int $seconds = 60): bool
{
    $session = new \Source\Core\Session();
    if ($session->has($key) && $session->$key->time >= time() && $session->$key->requests < $limit) {
        $session->set($key, [
            "time" => time() + $seconds,
            "requests" => $session->$key->requests + 1
        ]);
        return false;
    }

    if ($session->has($key) && $session->$key->time >= time() && $session->$key->requests >= $limit) {
        return true;
    }

    $session->set($key, [
        "time" => time() + $seconds,
        "requests" => 1
    ]);

    return false;
}

function request_repeat(string $field, string $value): bool
{
    $session = new \Source\Core\Session();
    if ($session->has($field) && $session->$field == $value) {
        return true;
    } 

    $session->set($field, $value);
    return false;
}

/**
 * ##################
 * ###   STRING   ###
 * ##################
 */
 
 /**
  * str_slug
  *
  * @param  mixed $string
  * @return string
  */
function str_slug(string $string): string
{
    $string = filter_var(mb_strtolower($string), FILTER_SANITIZE_SPECIAL_CHARS);
    $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
    $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyrr                                 ';

    $slug = str_replace(["-----", "----", "---", "--"], "-",
        str_replace(" ", "-", 
            trim(strtr(utf8_decode($string), utf8_decode($formats), $replace))
        )
    );
    return $slug;
}

/**
 * str_studly_case
 *
 * @param  mixed $string
 * @return string
 */
function str_studly_case(string $string): string
{
    $string = str_slug($string);
    $studlyCase = str_replace(" ", "",
        mb_convert_case(str_replace("-", " ", $string), MB_CASE_TITLE)
    );

    return $studlyCase;
}

/**
 * str_camel_case
 *
 * @param  mixed $string
 * @return string
 */
function str_camel_case(string $string): string
{
    return lcfirst(str_studly_case($string));
}

/**
 * str_title
 *
 * @param  mixed $string
 * @return string
 */
function str_title(string $string): string
{
    return mb_convert_case(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS), MB_CASE_TITLE);
}

/**
 * str_limit_words
 *
 * @param  string $string
 * @param  int $limit
 * @param  string $pointer
 * @return string
 */
function str_limit_words(string $string, int $limit, string $pointer = "..."): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS));
    $arrWords = explode(" ", $string);
    $numWords = count($arrWords);

    if ($numWords < $limit) {
        return $string;
    }

    $words = implode(" ", array_slice($arrWords, 0, $limit));
    return "{$words}{$pointer}";
}

/**
 * str_limit_chars
 *
 * @param  string $string
 * @param  int $limit
 * @param  string $pointer
 * @return string
 */
function str_limit_chars(string $string, int $limit, string $pointer = "..."): string
{
    $string = trim(filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS));

    if (mb_strlen($string) <= $limit) {
        return $string;
    }

    $chars = mb_substr($string, 0, mb_strrpos(mb_substr($string, 0, $limit), " "));
    return "{$chars}{$pointer}";
}


/**
 * ################
 * ###   URLs   ###
 * ################
 */


/**
 * url
 *
 * @param  mixed $path
 * @return string
 */
function url(string $path = null): string
{
    if (strpos($_SERVER['HTTP_HOST'], "localhost")) {
        if ($path) {
            return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
    }

    if ($path) {
        return CONF_URL_BASE . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE;
}

/**
 * url_back
 *
 * @return string
 */
function url_back(): string
{
    return ($_SERVER['HTTP_REFERER'] ?? url());
}

/**
 * redirect
 *
 * @param  mixed $url
 * @return void
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    } 
}


/**
 * ##################
 * ###   ASSETs   ###
 * ##################
 */

/**
 * theme
 *
 * @param  string|null $path
 * @param string $theme
 * @return string
 */
function theme(string $path = null, string $theme = CONF_VIEW_THEME): string
{
    if (strpos($_SERVER['HTTP_HOST'], "localhost")) {
        if ($path) {
            return CONF_URL_TEST . "/themes/{$theme}/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST . "/themes/{$theme}";
    }

    if ($path) {
        return CONF_URL_BASE . "/themes/{$theme}/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE . "/themes/{$theme}";
}

/**
 * @param string $image
 * @param int $width
 * @param int|null $height
 * @return string
 */
function image(string $image, int $width, ?int $height = null): string
{
    return url() . "/" . (new \Source\Support\Thumb())->make($image, $width, $height);
}


/**
 * ################
 * ###   DATE   ###
 * ################
 */

/**
 * date_fmt
 *
 * @param  mixed $date
 * @param  mixed $format
 * @return string
 */
function date_fmt(string $date = "now", string $format = "d/m/Y H\hi"): string
{
    return (new DateTime($date))->format($format);
}

/**
 * date_fmt_br
 *
 * @param  mixed $date
 * @return string
 */
function date_fmt_br(string $date = "now"): string
{
    return (new DateTime($date))->format(CONF_DATE_BR);
}

/**
 * date_fmt_app
 *
 * @param  mixed $date
 * @return string
 */
function date_fmt_app(string $date = "now"): string
{
    return (new DateTime($date))->format(CONF_DATE_APP);
}


/**
 * ###############
 * ###   CORE  ###
 * ###############
 */

 
 /**
  * db
  *
  * @return PDO
  */
function db(): PDO
{
    return \Source\Core\Connect::getInstance();
}

/**
 * message
 *
 * @return Source\Core\Message
 */
function message(): \Source\Support\Message
{
    return new \Source\Support\Message();
}

/**
 * session
 *
 * @return Source\Core\Session
 */
function session(): \Source\Core\Session
{
    return new \Source\Core\Session();
}

/**
 * ################
 * ###   MODEL  ###
 * ################
 */


/**
 * user
 *
 * @return Source\Models\User
 */
function user(): \Source\Models\User
{
    return new \Source\Models\User();
}