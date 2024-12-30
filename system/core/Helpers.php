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
        $environmentInUse = ($server == 'localhost' ? URL_DESENVOLVIMENTO : URL_PRODUCAO);

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

}
