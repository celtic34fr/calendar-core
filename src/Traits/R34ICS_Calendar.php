<?php

namespace Celtic34fr\CalendarCore\Traits;

/**
 * Trait build with some function of Wordpress Plugin ICS Calendar By Room 34 Creative Services, LLC
 */
trait R34ICS_Calendar
{
    // Kludge to fix rare cases where lines aren't properly folded
    // See: https://icalendar.org/iCalendar-RFC-5545/3-1-content-lines.html
    public function r34ics_line_break_fix($ics_contents='') {
        $ics_contents = (string)$ics_contents; // Avoid PHP 8.1 "Passing null to parameter" deprecation notice
        $lines = explode("\r\n", $ics_contents);
        $replace_contents = false;
        $prev = null;
        foreach ((array)$lines as $key => $line) {
            preg_match('/([A-Z]+[:;])/', $line, $matches, PREG_OFFSET_CAPTURE);
            if (!isset($matches[1][1]) || $matches[1][1] !== 0) {
                $lines[$key] = ' ' . trim($line ?? '');
                // May need to also insert an extra space if the last word of
                // the previous line is not a URL... this isn't perfect!
                if (!empty($prev)) {
                    $prev_arr = null;
                    if (strpos(trim($prev), ' ') !== false) {
                        $prev_arr = explode(' ', trim($prev));
                    }
                    elseif (strpos(trim($prev), "\\n") !== false) {
                        $prev_arr = explode("\\n", trim($prev));
                    }
                    if (!empty($prev_arr)) {
                        $prev_count = count($prev_arr);
                        if	($prev_count > 1 && strpos(($prev_arr[$prev_count - 1] ?? ''), 'http') === false)
                        {
                            $lines[$key] = ' ' . $lines[$key];
                        }
                    }
                }
                $replace_contents = true;
            }
            $prev = $line;
        }
        if ($replace_contents) {
            $ics_contents = implode("\r\n", $lines);
        }
        return $ics_contents;
    }

    /**
     * Removes unprintable ASCII and UTF-8 characters
     *
     * @param  string $data
     * @return string|null
     */
    protected function removeUnprintableChars($data)
    {
        return preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $data);
    }

    /**
     * Toggles whether to disable all character replacement.
     *
     * @var boolean
     */
    public $disableCharacterReplacement = false;

    /**
     * Replace curly quotes and other special characters with their standard equivalents
     * @see https://utf8-chartable.de/unicode-utf8-table.pl?start=8211&utf8=string-literal
     *
     * @param  string $input
     * @return string
     */
    protected function cleanCharacters($input)
    {
        return strtr(
            $input,
            array(
                "\xe2\x80\x98"     => "'",   // ‘
                "\xe2\x80\x99"     => "'",   // ’
                "\xe2\x80\x9a"     => "'",   // ‚
                "\xe2\x80\x9b"     => "'",   // ‛
                "\xe2\x80\x9c"     => '"',   // “
                "\xe2\x80\x9d"     => '"',   // ”
                "\xe2\x80\x9e"     => '"',   // „
                "\xe2\x80\x9f"     => '"',   // ‟
                "\xe2\x80\x93"     => '-',   // –
                "\xe2\x80\x94"     => '--',  // —
                "\xe2\x80\xa6"     => '...', // …
                $this->mb_chr(145) => "'",   // ‘
                $this->mb_chr(146) => "'",   // ’
                $this->mb_chr(147) => '"',   // “
                $this->mb_chr(148) => '"',   // ”
                $this->mb_chr(150) => '-',   // –
                $this->mb_chr(151) => '--',  // —
                $this->mb_chr(133) => '...', // …
            )
        );
    }

    /**
     * Gets the key value pair from an iCal string
     *
     * @param  string $text
     * @return array
     */
    public function keyValueFromString($text)
    {
        $splitLine = $this->parseLine($text);
        $object    = array();
        $paramObj  = array();
        $valueObj  = '';
        $i         = 0;

        while ($i < count($splitLine)) {
            // The first token corresponds to the property name
            if ($i === 0) {
                $object[0] = $splitLine[$i];
                $i++;

                continue;
            }

            // After each semicolon define the property parameters
            if ($splitLine[$i] == ';') {
                $i++;
                $paramName = $splitLine[$i];
                $i += 2;
                $paramValue = array();
                $multiValue = false;
                // A parameter can have multiple values separated by a comma
                while ($i + 1 < count($splitLine) && $splitLine[$i + 1] === ',') {
                    $paramValue[] = $splitLine[$i];
                    $i += 2;
                    $multiValue = true;
                }

                if ($multiValue) {
                    $paramValue[] = $splitLine[$i];
                } else {
                    $paramValue = $splitLine[$i];
                }

                // Create object with paramName => paramValue
                $paramObj[$paramName] = $paramValue;
            }

            // After a colon all tokens are concatenated (non-standard behaviour because the property can have multiple values
            // according to RFC5545)
            if ($splitLine[$i] === ':') {
                $i++;
                while ($i < count($splitLine)) {
                    $valueObj .= $splitLine[$i];
                    $i++;
                }
            }

            $i++;
        }

        // Object construction
        if ($paramObj !== array()) {
            $object[1][0] = $valueObj;
            $object[1][1] = $paramObj;
        } else {
            $object[1] = $valueObj;
        }

        return $object;
    }

    /**
     * Parses a line from an iCal file into an array of tokens
     *
     * @param  string $line
     * @return array
     */
    protected function parseLine($line)
    {
        $words = array();
        $word  = '';
        // The use of str_split is not a problem here even if the character set is in utf8
        // Indeed we only compare the characters , ; : = " which are on a single byte
        $arrayOfChar = str_split($line);
        $inDoubleQuotes = false;

        foreach ($arrayOfChar as $char) {
            // Don't stop the word on ; , : = if it is enclosed in double quotes
            if ($char === '"') {
                if ($word !== '') {
                    $words[] = $word;
                }

                $word = '';
                $inDoubleQuotes = !$inDoubleQuotes;
            } elseif (!in_array($char, array(';', ':', ',', '=')) || $inDoubleQuotes) {
                $word .= $char;
            } else {
                if ($word !== '') {
                    $words[] = $word;
                }

                $words[] = $char;
                $word = '';
            }
        }

        $words[] = $word;

        return $words;
    }

    /**
     * Provides a polyfill for PHP 7.2's `mb_chr()`, which is a multibyte safe version of `chr()`.
     * Multibyte safe.
     *
     * @param  integer $code
     * @return string
     */
    protected function mb_chr($code) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    {
        if (function_exists('mb_chr')) {
            return mb_chr($code);
        } else {
            if (($code %= 0x200000) < 0x80) {
                $s = chr($code);
            } elseif ($code < 0x800) {
                $s = chr(0xc0 | $code >> 6) . chr(0x80 | $code & 0x3f);
            } elseif ($code < 0x10000) {
                $s = chr(0xe0 | $code >> 12) . chr(0x80 | $code >> 6 & 0x3f) . chr(0x80 | $code & 0x3f);
            } else {
                $s = chr(0xf0 | $code >> 18) . chr(0x80 | $code >> 12 & 0x3f) . chr(0x80 | $code >> 6 & 0x3f) . chr(0x80 | $code & 0x3f);
            }

            return $s;
        }
    }
}
