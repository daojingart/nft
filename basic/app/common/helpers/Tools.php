<?php


namespace app\common\helpers;

use Bl\Tools\EncrypTool;
use think\Env;

class Tools
{

    /**
     * 通用MIME
     *
     * @var array
     */
    public static $mime_types = [
        'apk'     => 'application/vnd.android.package-archive',
        '3gp'     => 'video/3gpp',
        'ai'      => 'application/postscript',
        'aif'     => 'audio/x-aiff',
        'aifc'    => 'audio/x-aiff',
        'aiff'    => 'audio/x-aiff',
        'asc'     => 'text/plain',
        'atom'    => 'application/atom+xml',
        'au'      => 'audio/basic',
        'avi'     => 'video/x-msvideo',
        'bcpio'   => 'application/x-bcpio',
        'bin'     => 'application/octet-stream',
        'bmp'     => 'image/bmp',
        'cdf'     => 'application/x-netcdf',
        'cgm'     => 'image/cgm',
        'class'   => 'application/octet-stream',
        'cpio'    => 'application/x-cpio',
        'cpt'     => 'application/mac-compactpro',
        'csh'     => 'application/x-csh',
        'css'     => 'text/css',
        'dcr'     => 'application/x-director',
        'dif'     => 'video/x-dv',
        'dir'     => 'application/x-director',
        'djv'     => 'image/vnd.djvu',
        'djvu'    => 'image/vnd.djvu',
        'dll'     => 'application/octet-stream',
        'dmg'     => 'application/octet-stream',
        'dms'     => 'application/octet-stream',
        'doc'     => 'application/msword',
        'dtd'     => 'application/xml-dtd',
        'dv'      => 'video/x-dv',
        'dvi'     => 'application/x-dvi',
        'dxr'     => 'application/x-director',
        'eps'     => 'application/postscript',
        'etx'     => 'text/x-setext',
        'exe'     => 'application/octet-stream',
        'ez'      => 'application/andrew-inset',
        'flv'     => 'video/x-flv',
        'gif'     => 'image/gif',
        'gram'    => 'application/srgs',
        'grxml'   => 'application/srgs+xml',
        'gtar'    => 'application/x-gtar',
        'gz'      => 'application/x-gzip',
        'hdf'     => 'application/x-hdf',
        'hqx'     => 'application/mac-binhex40',
        'htm'     => 'text/html',
        'html'    => 'text/html',
        'ice'     => 'x-conference/x-cooltalk',
        'ico'     => 'image/x-icon',
        'ics'     => 'text/calendar',
        'ief'     => 'image/ief',
        'ifb'     => 'text/calendar',
        'iges'    => 'model/iges',
        'igs'     => 'model/iges',
        'jnlp'    => 'application/x-java-jnlp-file',
        'jp2'     => 'image/jp2',
        'jpe'     => 'image/jpeg',
        'jpeg'    => 'image/jpeg',
        'jpg'     => 'image/jpg',
        'js'      => 'application/x-javascript',
        'kar'     => 'audio/midi',
        'latex'   => 'application/x-latex',
        'lha'     => 'application/octet-stream',
        'lzh'     => 'application/octet-stream',
        'm3u'     => 'audio/x-mpegurl',
        'm4a'     => 'audio/mp4a-latm',
        'm4p'     => 'audio/mp4a-latm',
        'm4u'     => 'video/vnd.mpegurl',
        'm4v'     => 'video/x-m4v',
        'mac'     => 'image/x-macpaint',
        'man'     => 'application/x-troff-man',
        'mathml'  => 'application/mathml+xml',
        'me'      => 'application/x-troff-me',
        'mesh'    => 'model/mesh',
        'mid'     => 'audio/midi',
        'midi'    => 'audio/midi',
        'mif'     => 'application/vnd.mif',
        'mov'     => 'video/quicktime',
        'movie'   => 'video/x-sgi-movie',
        'mp2'     => 'audio/mpeg',
        'mp3'     => 'audio/mpeg',
        'mp4'     => 'video/mp4',
        'mpe'     => 'video/mpeg',
        'mpeg'    => 'video/mpeg',
        'mpg'     => 'video/mpeg',
        'mpga'    => 'audio/mpeg',
        'ms'      => 'application/x-troff-ms',
        'msh'     => 'model/mesh',
        'mxu'     => 'video/vnd.mpegurl',
        'nc'      => 'application/x-netcdf',
        'oda'     => 'application/oda',
        'ogg'     => 'application/ogg',
        'ogv'     => 'video/ogv',
        'pbm'     => 'image/x-portable-bitmap',
        'pct'     => 'image/pict',
        'pdb'     => 'chemical/x-pdb',
        'pdf'     => 'application/pdf',
        'pgm'     => 'image/x-portable-graymap',
        'pgn'     => 'application/x-chess-pgn',
        'pic'     => 'image/pict',
        'pict'    => 'image/pict',
        'png'     => 'image/png',
        'pnm'     => 'image/x-portable-anymap',
        'pnt'     => 'image/x-macpaint',
        'pntg'    => 'image/x-macpaint',
        'ppm'     => 'image/x-portable-pixmap',
        'ppt'     => 'application/vnd.ms-powerpoint',
        'ps'      => 'application/postscript',
        'qt'      => 'video/quicktime',
        'qti'     => 'image/x-quicktime',
        'qtif'    => 'image/x-quicktime',
        'ra'      => 'audio/x-pn-realaudio',
        'ram'     => 'audio/x-pn-realaudio',
        'ras'     => 'image/x-cmu-raster',
        'rdf'     => 'application/rdf+xml',
        'rgb'     => 'image/x-rgb',
        'rm'      => 'application/vnd.rn-realmedia',
        'roff'    => 'application/x-troff',
        'rtf'     => 'text/rtf',
        'rtx'     => 'text/richtext',
        'sgm'     => 'text/sgml',
        'sgml'    => 'text/sgml',
        'sh'      => 'application/x-sh',
        'shar'    => 'application/x-shar',
        'silo'    => 'model/mesh',
        'sit'     => 'application/x-stuffit',
        'skd'     => 'application/x-koan',
        'skm'     => 'application/x-koan',
        'skp'     => 'application/x-koan',
        'skt'     => 'application/x-koan',
        'smi'     => 'application/smil',
        'smil'    => 'application/smil',
        'snd'     => 'audio/basic',
        'so'      => 'application/octet-stream',
        'spl'     => 'application/x-futuresplash',
        'src'     => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc'  => 'application/x-sv4crc',
        'svg'     => 'image/svg+xml',
        'swf'     => 'application/x-shockwave-flash',
        't'       => 'application/x-troff',
        'tar'     => 'application/x-tar',
        'tcl'     => 'application/x-tcl',
        'tex'     => 'application/x-tex',
        'texi'    => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tif'     => 'image/tiff',
        'tiff'    => 'image/tiff',
        'tr'      => 'application/x-troff',
        'tsv'     => 'text/tab-separated-values',
        'txt'     => 'text/plain',
        'ustar'   => 'application/x-ustar',
        'vcd'     => 'application/x-cdlink',
        'vrml'    => 'model/vrml',
        'vxml'    => 'application/voicexml+xml',
        'wav'     => 'audio/x-wav',
        'wbmp'    => 'image/vnd.wap.wbmp',
        'wbxml'   => 'application/vnd.wap.wbxml',
        'webm'    => 'video/webm',
        'wml'     => 'text/vnd.wap.wml',
        'wmlc'    => 'application/vnd.wap.wmlc',
        'wmls'    => 'text/vnd.wap.wmlscript',
        'wmlsc'   => 'application/vnd.wap.wmlscriptc',
        'wmv'     => 'video/x-ms-wmv',
        'wrl'     => 'model/vrml',
        'xbm'     => 'image/x-xbitmap',
        'xht'     => 'application/xhtml+xml',
        'xhtml'   => 'application/xhtml+xml',
        'xls'     => 'application/vnd.ms-excel',
        'xml'     => 'application/xml',
        'xpm'     => 'image/x-xpixmap',
        'xsl'     => 'application/xml',
        'xslt'    => 'application/xslt+xml',
        'xul'     => 'application/vnd.mozilla.xul+xml',
        'xwd'     => 'image/x-xwindowdump',
        'xyz'     => 'chemical/x-xyz',
        'ipa'     => 'application/octet-stream',
        'zip'     => 'application/zip',
    ];

    /**
     * 根据MIME获取后缀名
     *
     * @param $mime string 文件MIME
     * @return string 文件后缀
     */
    public static function mime2suffix($mime)
    {
        $tmp = array_flip(self::$mime_types);
        if (array_key_exists(strtolower($mime), $tmp)) {
            return $tmp[$mime];
        }

        return 'file';
    }

    /**
     * 命令行下面输出字符串
     * @param        $msg
     * @param string $level
     * @param bool   $newLine  换行
     * @param bool   $showTime 显示时间
     * @param string $style    success/error/info
     */
    public static function show_msg($msg, $level = 'crontab', $newLine = true, $showTime = true, $style = '')
    {
        if (!IS_CLI) {
            return;
        }
        // trace($msg, $level);
        $msg = IS_WIN && version_compare(PHP_VERSION, '7.1.0', '<') ? mb_convert_encoding($msg, 'gbk', 'utf8') : $msg;
        if ($showTime) {
            $msg = date("Y-m-d H:i:s") . " " . $msg;
        }
        $styles = [
            'success' => "\033[0;32m%s\033[0m",
            'error'   => "\033[31;31;5m%s\033[0m",
            'info'    => "\033[33;33m%s\033[0m",
        ];
        if ($newLine) {
            $msg .= PHP_EOL;
        }
        $format = '%s';
        if (isset($styles[$style]) && !IS_WIN) {
            $format = $styles[$style];
        }
        printf($format, $msg);
    }


    /**
     * 命令行下面输出绿色字符串
     * @param        $msg
     * @param string $level
     * @param bool   $newLine  换行
     * @param bool   $showTime 显示时间
     */
    public static function show_success($msg, $level = 'crontab', $newLine = true, $showTime = true)
    {
        self::show_msg($msg, $level, $newLine, $showTime, 'success');
    }

    /**
     * 命令行下面输出红色字符串
     * @param        $msg
     * @param string $level
     * @param bool   $newLine  换行
     * @param bool   $showTime 显示时间
     */
    public static function show_error($msg, $level = 'crontab', $newLine = true, $showTime = true)
    {
        self::show_msg($msg, $level, $newLine, $showTime, 'error');
    }

    /**
     * 命令行下面输出黄色字符串
     * @param        $msg
     * @param string $level
     * @param bool   $newLine  换行
     * @param bool   $showTime 显示时间
     */
    public static function show_info($msg, $level = 'crontab', $newLine = true, $showTime = true)
    {
        self::show_msg($msg, $level, $newLine, $showTime, 'info');
    }

    /**
     * 生成订单号
     * @param string $prefix
     * @param string $suffix
     * @return string 最长不能超过32
     */
    public static function genOrderSn(string $prefix = 'BL', string $suffix = ''): string
    {
        return $prefix . date('YmdHis') . random_int(1000000, 9999999) . $suffix;
    }


    /**
     * 加密
     * @param $string
     * @param $key
     * @param $iv
     * @return string
     */
    public static function aes_encrypt($string, $key, $iv = 'e5q3oougyamlxkd5jz35ej')
    {
        $string    = trim($string);
        $key       = substr(md5($key), 0, 16);
        $iv        = substr(md5($iv), 0, 16);
        $encrypted = openssl_encrypt($string, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encrypted);
    }

    /**
     * 解密
     * @param $string
     * @param $key
     * @param $iv
     * @return string
     */
    public static function aes_decrypt($string, $key, $iv = 'e5q3oougyamlxkd5jz35ej')
    {
        $key       = substr(md5($key), 0, 16);
        $iv        = substr(md5($iv), 0, 16);
        $decrypted = base64_decode($string);
        $decrypted = openssl_decrypt($decrypted, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return trim($decrypted);
    }

    /**
     * 取负数
     * @param $num
     * @return float|int
     */
    public static function minus($num)
    {
        return $num * (-1);
    }

    /**
     * 加密字符串
     * @param $string
     * @return string
     */
    public static function str_encrypt($string): string
    {
        if (strpos($string, '-----BEGIN') !== false && strpos($string, '-----END')) {
            $list   = explode("\r\n", $string);
            $prefix = $list[0];
            $suffix = $list[count($list) - 1];
            unset($list[count($list) - 1], $list[0]);
            $string    = implode("\r\n", $list);
            $en_string = "en|" . self::aes_encrypt($string, Env::get('encrypt.key', 'kipsgYPnMkZlxr8B'));
            return $prefix . "\r\n" . $en_string . "\r\n" . $suffix;
        }

        return "en|" . self::aes_encrypt($string, Env::get('encrypt.key', 'kipsgYPnMkZlxr8B'));
    }

    /**
     * 解密字符串
     * @param $string
     * @return string
     */
    public static function str_decrypt($string): string
    {
        $string = str_replace("en|", "", $string);
        if (strpos($string, '-----BEGIN') !== false && strpos($string, '-----END')) {
            $list   = explode("\r\n", $string);
            $prefix = $list[0];
            $suffix = $list[count($list) - 1];
            unset($list[count($list) - 1], $list[0]);
            $string    = implode("\r\n", $list);
            $de_string = self::aes_decrypt($string, Env::get('encrypt.key', 'kipsgYPnMkZlxr8B'));

            return $prefix . "\r\n" . $de_string . "\r\n" . $suffix;
        }
        return self::aes_decrypt($string, Env::get('encrypt.key', 'kipsgYPnMkZlxr8B'));
    }

    /**
     * 解密字符串遮罩
     * @param        $string
     * @param string $mask
     * @return string
     */
    public static function str_decrypt_mask($string, string $mask = '*'): string
    {
        $string = self::str_decrypt($string);
        if (!$string) {
            return "";
        }

        if (strpos($string, '-----BEGIN') !== false && strpos($string, '-----END')) {
            $list   = explode("\r\n", $string);
            $prefix = $list[0];
            $suffix = $list[count($list) - 1];
            unset($list[count($list) - 1], $list[0]);
            $string = implode("\r\n", $list);
            if (empty($string)) {
                return '';
            }
            $de_string = substr($string, 0, 5) . str_repeat($mask, 32) . substr($string, -4, 4);;
            return $prefix . "\r\n" . $de_string . "\r\n" . $suffix;
        }

        return substr($string, 0, 5) . str_repeat($mask, 32) . substr($string, -4, 4);
    }
}