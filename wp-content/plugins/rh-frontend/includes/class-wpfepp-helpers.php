<?php
/**
 * WPFEPP Helpers class.
 *
 * @since 3.5.4
 * @package WPFEPP
 **/
 
class WPFEPP_Helpers {

    public static function truncate( $string, $length = 80, $etc = '...', $charset = 'UTF-8', $break_words = false, $middle = false ) {
        if ( $length == 0 )
            return '';
        if ( mb_strlen( $string, 'UTF-8' ) > $length ) {
            $length -= min( $length, mb_strlen( $etc, 'UTF-8' ) );
            if ( !$break_words && !$middle ) {
                $string = preg_replace( '/\s+?(\S+)?$/', '', mb_substr( $string, 0, $length + 1, $charset ) );
            }
            if ( !$middle ) {
                return mb_substr( $string, 0, $length, $charset ) . $etc;
            } else {
                return mb_substr( $string, 0, $length / 2, $charset ) . $etc . mb_substr( $string, -$length / 2, $charset );
            }
        } else {
            return $string;
        }
    }

    static function rus2latin( $str ) {
        $iso = array(
            "Є" => "YE", "І" => "I", "Ѓ" => "G", "і" => "i", "№" => "#", "є" => "ye", "ѓ" => "g",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "J", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "X",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "'",
            "Ы" => "Y", "Ь" => "", "Э" => "E", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "yo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "j", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "x",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
            "'" => "", "\"" => "", " " => "-"
        );

        return strtr( $str, $iso );
    }
}
