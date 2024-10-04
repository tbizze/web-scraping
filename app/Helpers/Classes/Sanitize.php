<?php

declare(strict_types=1);

namespace App\Helpers\Classes;

final class Sanitize
{
    /**
     * Remove accents from string
     *
     * @param string $string String to remove accents
     *
     * @return string String without accents
     */
    public static function removeAccents(string $string): string
    {
        $string = preg_replace('/[áàãâä]/u', 'a', $string); // substitui todos os acentos "a"
        $string = preg_replace('/[éèêë]/u', 'e', $string); // substitui todos os acentos "e"
        $string = preg_replace('/[íìîï]/u', 'i', $string); // substitui todos os acentos "i"
        $string = preg_replace('/[óòõôö]/u', 'o', $string); // substitui todos os acentos "o"
        $string = preg_replace('/[úùûü]/u', 'u', $string); // substitui todos os acentos "u"
        $string = preg_replace('/[ç]/u', 'c', $string); // substitui o cedilha "ç"
        $string = preg_replace('/[ÁÀÃÂÄ]/u', 'A', $string); // substitui todos os acentos "A"
        $string = preg_replace('/[ÉÈÊË]/u', 'E', $string); // substitui todos os acentos "E"
        $string = preg_replace('/[ÍÌÎÏ]/u', 'I', $string); // substitui todos os acentos "I"
        $string = preg_replace('/[ÓÒÕÔÖ]/u', 'O', $string); // substitui todos os acentos "O"
        $string = preg_replace('/[ÚÙÛÜ]/u', 'U', $string); // substitui todos os acentos "U"
        return preg_replace('/[Ç]/u', 'C', $string);
    }

    /**
     * Remove caracteres especiais de string
     *
     * @param string $string String to remove special characters
     *
     * @return string String without special characters
     */
    public static function removeSpecialCharacters(string $string): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }

    /**
     * Torna maiúsculo o primeiro caractere das palavras de uma string.
     *
     * @param string $string String a converter caracteres
     *
     * @return string String Convertido os caracteres
     */
    public static function capitalizeEachWords(string $string)
    {
        $search =  array('Da ', 'Das ', 'De ', 'Do ', 'Dos ', 'A ', 'As ', 'E ', 'O ', 'Os ', 'Em');
        $replace = array('da ', 'das ', 'de ', 'do ', 'dos ', 'a ', 'as ', 'e ', 'o ', 'os ', 'em');

        return ucfirst(str_replace($search, $replace, ucwords(strtolower($string))));
    }
}
