<?php

declare(strict_types=1);

namespace App\Helpers\Classes;

use Ramsey\Uuid\Type\Decimal;
use Carbon\Carbon;
use Illuminate\Support\Str;

final class Format
{
    /** Método para formatar moeda para BD(decimal).
     *
     * @param string|float $number Número a formatar
     * @param int $decimals [optional] Número de casas decimais (default: 2)
     *
     * @return string Número formatado
     */
    public static function currencyToDb(string|float $number, int $decimals = 2): string
    {
        // Primeiro: no 'str_replace' da direita, substitui indicador de milhar(.) por vazio.
        // Depois: no 'str_replace' da esquerda, substitui indicador de decimal(,) por ponto.
        $number = str_replace(',', '.', str_replace('.', '', $number));
        $number = number_format((float)$number, $decimals, '.', '');

        return $number;
    }

    /** Método para formatar moeda do BD(decimal) para formato brasileiro.
     *
     * @param string|float $number Número a formatar
     * @param int $decimals [optional] Número de casas decimais (default: 2)
     * @param string $thousandSeparator [optional] Separador de milhar (default: '')
     *
     * @return string Número formatado
     */
    public static function currencyGetDb(string|float $number, int $decimals = 2, string $thousandSeparator = ''): string
    {
        // Primeiro: no 'str_replace' da direita, substitui indicador de milhar(.) por vazio.
        // Depois: no 'str_replace' da esquerda, substitui indicador de decimal(,) por ponto.
        //$number = str_replace(',', '.', str_replace('.', '', $number));
        $number = number_format((float)$number, $decimals, ',', $thousandSeparator);

        return $number;
    }

    /** Método para formatar uma data(d/m/Y) para BD (Y-m-d).
     *
     * @param string $date_string Data a formatar
     *
     * @return string Data formatada
     */
    public static function dateToDb(string $date): string
    {
        // Quebra o $date em duas partes: data/hora.
        $date_string = Str::of($date)->explode(' ');

        // Quebra o $date_string em três partes: dia/mês/ano.
        $date_string = Str::of($date_string[0])->explode('/');

        // Não exite parte do ano 
        // Não exite parte do mês 
        // retorna sem fazer nada.
        if (count($date_string) == 1) {
            $date_string[2] = Carbon::now()->year;
            return $date;
        }

        // Não existe parte do ano
        // Existe parte do mês, mas é vazio
        // retorna sem fazer nada.
        if (count($date_string) == 2 && empty($date_string[1])) {
            return $date;
        }

        // Se parte do ano existe
        if (count($date_string) == 3) {

            if (empty($date_string[2])) {

                // Quando ano vazio, o define com ano corrente
                $date_string[2] = Carbon::now()->year;
            } elseif (strlen($date_string[2]) == 2) {

                // Quando ano com 2 números, o define para 4
                $date_string[2] = Carbon::createFromFormat('y', $date_string[2])->format('Y');
            }
        }

        // Não existe parte do ano
        // Existe parte do mês 
        // cria parte ano com o ano corrente
        if (count($date_string) == 2) {
            $date_string[2] = Carbon::now()->year;
        }

        // Retorna data remontada no formato Y-m-d.
        //dd($date_string[2], $date_string[1], $date_string[0]);
        //return Carbon::create($date_string[2], $date_string[1], $date_string[0]);
        return Carbon::createFromFormat('Y-m-d', $date_string[2] . '-' . $date_string[1]  . '-' . $date_string[0])->format('Y-m-d');
    }











    /** method to format number to database
     *
     * @param string $number Number to format
     * @param string $thousandSeparator [optional] Thousand separator (default: .)
     *
     * @return float Formatted number
     */
    public static function numberToDb(string $number): string
    {
        $number = str_replace(',', '.', str_replace('.', '', $number));
        $number = number_format((float)$number, 2, '.', '');

        return $number;
    }

    /** method to format date
     *
     * @param string $date Date to format
     * @param string|null $format [optional] Format to use (default: d/m/Y)
     *
     * @return string Formatted date
     */
    public static function date(string $date, string|null $format = null): string
    {
        return date($format ?? self::dateFormatDefault(), strtotime($date));
    }

    /** method to format datetime
     *
     * @param string $date Date to format
     * @param string|null $format [optional] Format to use (default: d/m/Y H:i:s)
     *
     * @return string Formatted date
     */
    public static function datetime(string $date, string|null $format = null): string
    {
        return date($format ?? self::datetimeFormatDefault(), strtotime($date));
    }



    /** method to format datetime to database
     *
     * @param string $date_string Date to format
     * @param string|null $format [optional] Format to use (default: d/m/Y H:i:s)
     *
     * @return string Formatted date
     */
    public static function datetimeDb(string $date_string, string|null $format = null): string
    {
        $date = \DateTime::createFromFormat($format ?? self::datetimeFormatDefault(), $date_string);
        return $date->format('Y-m-d H:i:s');
    }

    private static function dateFormatDefault(): string
    {
        return config('php-helpers.date_format');
    }

    private static function datetimeFormatDefault(): string
    {
        return config('php-helpers.datetime_format');
    }
}
