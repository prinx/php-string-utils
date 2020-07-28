<?php

/*
 * This file is part of the PHP String Utils package.
 *
 * (c) Prince Dorcis <princedorcis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prinx;

/**
 * String utilities class
 *
 * @author Prince Dorcis <princedorcis@gmail.com>
 */
class Str
{
    /**
     * Check if a string contains only alphabetic characters
     *
     * @param string $str
     * @param integer $minLength
     * @param integer $maxLength
     * @return boolean
     */
    public static function isAlphabetic($str, $minLength = 1, $maxLength = -1)
    {
        $length = strlen($str);

        $inLength = $minLength <= $length;

        if ($maxLength > $minLength) {
            $inLength = $inLength && ($length <= $maxLength);
        }

        return $inLength && '' . intval($str) !== $str && '' . floatval($str) !== $str;
    }

    /**
     * Check if a string contains only alphabetic characters
     *
     * @param string $str
     * @param integer $minLength
     * @param integer $maxLength
     * @return boolean
     */
    public static function isAlphanumeric($str)
    {
        return preg_match('/(\W?\w)+/i', $str);
    }

    /**
     * Check if a string represents a number
     *
     * @param string $num
     * @return boolean
     */
    public static function isNumeric($num)
    {
        // return self::isIntegerNumeric($num);
        return is_numeric($num);
    }

    /**
     * Check if a string represents a float number
     *
     * @param string $num
     * @return boolean
     */
    public static function isFloatNumeric($num)
    {
        return preg_match('/^[0-9]+(,[0-9]+)*\.?[0-9]*$/', $num);
    }

    /**
     * Check if a string represents an integer number
     *
     * @param string $num
     * @return boolean
     */
    public static function isIntegerNumeric($num)
    {
        return preg_match('/^[0-9]+(,[0-9]+)*$/', $num);
    }

    /**
     * Convert a string to camel case
     *
     * @param string $name
     * @param null|string|array $sep
     * @return string
     */
    public static function camelCase($name, $sep = null)
    {
        return lcfirst(self::pascalCase($name, $sep));
    }

    /**
     * Convert a string to pascal case.
     *
     * If no separator passed, the default separators are used.
     *
     * @param string $name
     * @param string|array|null $separators
     * @return string
     */
    public static function pascalCase($name, $separators = null)
    {
        $separators = $separators ?? ['_', '-', ' '];
        $separators = is_string($separators) ? [$separators] : $separators;

        $pascal_case = $name;

        foreach ($separators as $sep) {
            $chunks = explode($sep, $pascal_case);

            $temp = '';
            foreach ($chunks as $value) {
                $temp .= ucfirst($value);
            }

            $pascal_case = $temp;
        }

        return $pascal_case;
    }

    /**
     * Convert a string to pascal case.
     *
     * If no separator passed, the function uses every character that is neither an alphabetic character nor a number as seperator.
     *
     * @param string $name
     * @param string|array|null $separators
     * @return string
     */
    public function pascalCaseNew($name, $separators = null)
    {
        $pascal_case = $name;

        $separators = $separators ?? '_';

        if (is_string($separators)) {
            $pascal_case = preg_replace('/[^a-z0-9]/i', $separators, $pascal_case);
            $separators = [$separators];
        } elseif (!is_array($separators)) {
            throw new \Exception("Separator must be either NULL or a string or an array. Got " . gettype($separators));
        }

        foreach ($separators as $sep) {
            $chunks = explode($sep, $pascal_case);

            $temp = '';
            foreach ($chunks as $value) {
                $temp .= ucfirst($value);
            }

            $pascal_case = $temp;
        }

        return $pascal_case;
    }

    /**
     * Put a telephone number in international format
     *
     * @param string $number
     * @param string $country
     * @return string
     */
    public static function oldInternationalizeNumber(
        string $number,
        string $country = 'GH'
    ) {
        $default_country_code = '233';

        // To be completed
        $country_codes = [
            'GH' => '233',
        ];

        $num = preg_replace('/\([0-9]+?\)/', '', $number);
        $num = preg_replace('/[^0-9]/', '', $num);
        $num = ltrim($num, '0');

        $country_type = preg_match('/^[0-9]+$/', $country) ? 'prefix' : 'name';

        if ('name' === $country_type &&
            array_key_exists($country, $country_codes)
        ) {
            $prefix = $country_codes[$country];
        } elseif ('prefix' === $country_type &&
            in_array($country, $country_codes)
        ) {
            $prefix = $country;
        } else {
            $prefix = $default_country_code;
        }

        if (!preg_match('/^' . $prefix . '/', $num)) {
            $num = $prefix . $num;
        }

        return $num;
    }

    /**
     * Put a telephone number in international format
     *
     * @param string $number
     * @param string|int $countryCode
     * @param boolean $addPlus
     * @return string
     */
    public static function internationaliseNumber($number, $countryCode, $addPlus = false)
    {
        $num = preg_replace('/\([0-9]+?\)/', '', $number);
        $num = preg_replace('/[^0-9]/', '', $num);
        $num = ltrim($num, '0');

        if (!self::startsWith($countryCode, $num)) {
            $num = $countryCode . $num;
        }

        if ($addPlus && !self::startsWith('+', $num)) {
            $num = '+' . $num;
        }

        return $num;
    }

    /**
     * Make uppercase each word of the string passed to it
     *
     * @param string $str
     * @return string
     */
    public static function capitalise(string $str)
    {
        $exploded = explode(' ', $str);
        $capitalised = '';

        foreach ($exploded as $value) {
            $capitalised .= ucfirst(strtolower($value)) . ' ';
        }

        return trim($capitalised);
    }

    /**
     * Check if a string can be parse to a telephone number
     *
     * @param string $str
     * @return boolean
     */
    public static function isTelNumber(string $str)
    {
        return preg_match('/^(\+|00)?[0-9-() ]{7,15}$/', $str) === 1;
    }

    /**
     * Check if a string has at most a certain number of characters
     *
     * @param string $str
     * @param integer $maxLen
     * @return boolean
     */
    public static function isMaxLength(string $str, int $maxLen)
    {
        return strlen($str) <= $maxLen;
    }

    /**
     * Check if a string length has at least a certain number of characters
     *
     * @param string $str
     * @param integer $minLen
     * @return boolean
     */
    public static function isMinLength(string $str, int $minLen)
    {
        return strlen($str) >= $minLen;
    }

    /**
     * Check if a string starts with a certain string
     *
     * @param string $startStr
     * @param string $subject
     * @return boolean
     *
     * For contributors: Do not use preg_match for the check as conflict happen
     * when the delimiters are in the string that is going to be checked
     */
    public static function startsWith(string $startStr, string $subject)
    {
        return strpos($subject, $startStr) === 0;
        // return preg_match('/^' . $startStr . '/', $str);
    }

    /**
     * Check if a string end with a certain string
     *
     * @param string $endStr
     * @param string $subject
     * @return boolean
     *
     * For contributors: Do not use preg_match for the check as conflict happen
     * when the delimiters are in the string that is going to be checked
     */
    public static function endsWith(string $endStr, string $subject)
    {
        $endStrLength = strlen($endStr);
        $subjectLength = strlen($subject);

        if ($endStrLength > $subjectLength) {
            return false;
        }

        $endStrMustBePosition = $subjectLength - $endStrLength;
        $endStrActualPosition = strrpos($subject, $endStr);

        return $endStrMustBePosition === $endStrActualPosition;
        // return preg_match('/' . $endStr . '$/', $subject);
    }

    /**
     * Check if a string contains another string
     *
     * @param string $substr
     * @param string $subject
     * @return boolean
     */
    public static function contains(string $substr, string $subject)
    {
        return strpos($subject, $substr) !== false;
    }
}
