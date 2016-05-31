<?php
/**
 * Utils.php.
 * @author keepeye <carlton.cheng@foxmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace library;


class Utils
{
    /**
     * 蛇形转驼峰
     *
     * @param $string
     * @return mixed
     */
    public static function snake2camel($string)
    {
        $string = str_replace("_","-",$string);//下划线替换为破折号统一处理
        $string = ucfirst(strtolower($string));
        return preg_replace_callback('/\-([a-z])/i',function($matches){
            return ucfirst($matches[1]);
        },$string);
    }

    /**
     * 路径转换成完整url
     *
     * @param string $rel 待转换的路径
     * @param string $base 相对的基础url
     * @return string
     */
    public static function url2abs($rel, $base) {
        /** @var string $scheme */
        /** @var string $host */
        /** @var string $path */
        // parse base URL  and convert to local variables: $scheme, $host,  $path
        extract( parse_url( $base ) );

        if ( strpos( $rel,"//" ) === 0 ) {
            return $scheme . ':' . $rel;
        }

        // return if already absolute URL
        if ( parse_url( $rel, PHP_URL_SCHEME ) != '' ) {
            return $rel;
        }

        // queries and anchors
        if ( $rel[0] == '#' || $rel[0] == '?' ) {
            return $base . $rel;
        }

        // remove non-directory element from path
        $path = preg_replace( '#/[^/]*$#', '', $path );

        // destroy path if relative url points to root
        if ( $rel[0] ==  '/' ) {
            $path = '';
        }

        // dirty absolute URL
        $abs = $host . $path . "/" . $rel;

        // replace '//' or  '/./' or '/foo/../' with '/'
        $abs = preg_replace( "/(\/\.?\/)/", "/", $abs );
        $abs = preg_replace( "/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs );

        // absolute URL is ready!
        return $scheme . '://' . $abs;
    }
}