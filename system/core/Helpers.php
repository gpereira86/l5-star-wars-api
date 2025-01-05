<?php

namespace system\core;


class Helpers
{
    /**
     *  Retorna Verdadeiro ou falso para ambiente localhost
     *
     * @return bool
     */
    public static function localhost(): bool
    {
        $server = filter_input(INPUT_SERVER, 'SERVER_NAME');

        if ($server == 'localhost') {
            return true;
        }
        return false;
    }

    /**
     * Monta URL de acordo com Ambiente desenvolvimento ou produção
     *
     * @param string|null $url
     * @return string
     */
    public static function url(string $url = null): string
    {
        $server = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $environmentInUse = ($server == 'localhost' ? DEVELOPMENT_URL : PRODUCTION_URL);

        if (strpos($url, '/') === 0) {
            return$environmentInUse . $url;
        }

        return$environmentInUse . '/' . $url;
    }


    /**
     * Redirecionamento de URL simplidicado, apenas com o slug
     *
     * @param string|null $url
     * @return void
     */
    public static function redirectUrl(string $url = null): void
    {
        header('HTTP/1.1 302 found');

        $local = ($url ? self::url($url) : self::url());

        header("location: {$local}");
        exit();
    }

    public static function summarizeText(string $text, int $limit, string $continue = '...'): string
    {
        $cleanText = trim(strip_tags($text));

        if (mb_strlen($cleanText) <= $limit) {
            return $cleanText;
        }

        $summarizeText = mb_substr($cleanText, 0, mb_strrpos(mb_substr($cleanText, 0, $limit), ' '));

        return $summarizeText . $continue;
    }

    public static function slug(string $string): string
    {
        $map['a'] = "ÁáãÃÉéÍíÓóÚúÀàÈèÌìÒòÙùÂâÊêÎîÔôÛûÄäËëÏïÖöÜüÇçÑñÝý!@#$%&!*_-+=:;,.?/|'~^°¨ªº´";
        $map['b'] = 'AaaAEeIiOoUuAaEeIiOoUuAaEeIiOoUuAaEeIiOoUuCcNnYy___________________________';

        $slug = strtr(utf8_decode($string), utf8_decode($map['a']), $map['b']);
        $slug = strip_tags(trim($slug));
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(['-----', '----', '--', '-'], '-', $slug);

        return strtolower(utf8_decode($slug));
    }




}
