<?php
/**
 * Smarty plugin
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @copyright  2013 Kaoru Ishikura
 * @author     Kaoru Ishikura
 * @package    Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty link to urls modifier plugin
 *
 * Type:     modifier<br>
 * Name:     url2link<br>
 * Purpose:  gets html string including automatically linked urls<br>
 * Examples: {$string|url2link}
 *
 * @param string $string html string
 * @param string $target target attribute
 * @return string returns html string including automatically linked urls
 */
function smarty_modifier_url2link($string, $target = null)
{
    $string = str_replace(array("\x0D\x0A", "\x0D"), "\x0A", $string);

    $pattern = "/(?:\A|<\/(?:a|pre|script|style|textarea)(?:|[^\"'<>a-zA-Z0-9][^\"'<>]*(?:\"[^\"]*\"[^\"'<>]*|'[^']*'[^\"'<>]*)*)(?:>|(?=<)|\z)).*?"
             . "(?:\z|<(?:a|pre|script|style|textarea)(?:|[^\"'<>a-zA-Z0-9][^\"'<>]*(?:\"[^\"]*\"[^\"'<>]*|'[^']*'[^\"'<>]*)*)(?:>|(?=<)|\z))/is"
             . Smarty::$_UTF8_MODIFIER;
    $string  = preg_replace_callback(
        $pattern,
        function($matches) use($target)
        {
            $pattern    = "/(?:\A|(?<=>))(?:[^<>\x0A][^<>]*?|\x0A[^<>]+?)(?:(?=[<>])|\z)/is"
                        . Smarty::$_UTF8_MODIFIER;
            $matches[0] = preg_replace_callback(
                $pattern,
                function($matches) use($target)
                {
                    if (trim($matches[0]) !== '') {
                        if ($target === null) {
                            if (isset($_SERVER['HTTP_HOST'])) {
                                $pattern    = '/(?:ftps?|s?https?):\/\/' . preg_quote($_SERVER['HTTP_HOST'], '/')
                                            . '[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/'
                                            . Smarty::$_UTF8_MODIFIER;
                                $matches[0] = preg_replace($pattern, '<a href="' . "$0" . '">' . "$0" . '</a>', $matches[0]);
                            }
                            $matches[0] = smarty_modifier_url2link($matches[0], '_blank');
                        } else {
                            $pattern    = '/(?:ftps?|s?https?):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+/'
                                        . Smarty::$_UTF8_MODIFIER;
                            $matches[0] = preg_replace(
                                $pattern,
                                '<a href="' . "$0" . '" target="' . $target . '">' . "$0" . '</a>',
                                $matches[0]
                            );
                        }
                    }

                    return $matches[0];
                },
                $matches[0]
            );

            return $matches[0];
        },
        $string
    );

    return $string;
}

?>