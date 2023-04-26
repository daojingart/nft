<?php
$cdnurl = function_exists('config') ? config('view_replace_str.__CDN__') : '';
$publicurl = function_exists('config') ? (config('view_replace_str.__PUBLIC__')?:'/') : '/';
$debug = function_exists('config') ? config('app_debug') : false;

$lang = [
    'An error occurred' => '发生错误',
    'Home' => '返回主页',
    'Previous Page' => '返回上一页',
    'The page you are looking for is temporarily unavailable' => config('admin_error_message'),
    'You can return to the previous page and try again' => '你可以返回上一页重试'
];

$langSet = '';

if (isset($_GET['lang'])) {
    $langSet = strtolower($_GET['lang']);
} elseif (isset($_COOKIE['think_var'])) {
    $langSet = strtolower($_COOKIE['think_var']);
} elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
preg_match('/^([a-z\d\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
    $langSet     = strtolower($matches[1]);
}
$langSet = $langSet && in_array($langSet, ['zh-cn', 'en']) ? $langSet : 'zh-cn';
$langSet == 'en' && $lang = array_combine(array_keys($lang), array_keys($lang));

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title><?=$lang['An error occurred']?></title>
    <meta name="robots" content="noindex,nofollow" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="<?php echo $cdnurl;?>/assets/img/favicon.ico" />
    <style>
        * {-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;}
        html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,caption,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video {margin:0;padding:0;border:0;outline:0;vertical-align:baseline;background:transparent;}
        article,aside,details,figcaption,figure,footer,header,hgroup,nav,section {display:block;}
        html {font-size:16px;line-height:24px;width:100%;height:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;overflow-y:scroll;overflow-x:hidden;}
        img {vertical-align:middle;max-width:100%;height:auto;border:0;-ms-interpolation-mode:bicubic;}
        body {min-height:100%;background:#f4f6f8;text-rendering:optimizeLegibility;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;font-family:"Helvetica Neue",Helvetica,"PingFang SC","Hiragino Sans GB","Microsoft YaHei",微软雅黑,Arial,sans-serif;}
        .clearfix {clear:both;zoom:1;}
        .clearfix:before,.clearfix:after {content:"\0020";display:block;height:0;visibility:hidden;}
        .clearfix:after {clear:both;}
        body.error-page-wrapper,.error-page-wrapper.preview {background-position:center center;background-repeat:no-repeat;background-size:cover;position:relative;}
        .error-page-wrapper .content-container {border-radius:2px;text-align:center;box-shadow:0 0 30px rgba(99,99,99,0.06);padding:50px;background-color:#fff;width:100%;max-width:560px;position:absolute;left:50%;top:40%;margin-top:-220px;margin-left:-280px;}
        .error-page-wrapper .content-container.in {left:0px;opacity:1;}
        .error-page-wrapper .head-line {transition:color .2s linear;font-size:40px;line-height:40px;letter-spacing:-1px;margin-bottom:20px;color:#777;}
        .error-page-wrapper .subheader {transition:color .2s linear;font-size:32px;line-height:46px;color:#494949;}
        .error-page-wrapper .hr {height:1px;background-color:#eee;width:80%;max-width:350px;margin:25px auto;}
        .error-page-wrapper .context {transition:color .2s linear;font-size:16px;line-height:27px;color:#aaa;}
        .error-page-wrapper .context p {margin:0;}
        .error-page-wrapper .context p:nth-child(n+2) {margin-top:16px;}
        .error-page-wrapper .buttons-container {margin-top:35px;overflow:hidden;}
        .error-page-wrapper .buttons-container a {transition:text-indent .2s ease-out,color .2s linear,background-color .2s linear;text-indent:0px;font-size:14px;text-transform:uppercase;text-decoration:none;color:#fff;background-color:#2ecc71;border-radius:99px;padding:8px 0 8px;text-align:center;display:inline-block;overflow:hidden;position:relative;width:45%;}
        .error-page-wrapper .buttons-container a:hover {text-indent:15px;}
        .error-page-wrapper .buttons-container a:nth-child(1) {float:left;}
        .error-page-wrapper .buttons-container a:nth-child(2) {float:right;}
        @media screen and (max-width:580px) {
            .error-page-wrapper {padding:30px 5%;}
            .error-page-wrapper .content-container {padding:37px;position:static;left:0;margin-top:0;margin-left:0;}
            .error-page-wrapper .head-line {font-size:36px;}
            .error-page-wrapper .subheader {font-size:27px;line-height:37px;}
            .error-page-wrapper .hr {margin:30px auto;width:215px;}
        }
        @media screen and (max-width:450px) {
            .error-page-wrapper {padding:30px;}
            .error-page-wrapper .head-line {font-size:32px;}
            .error-page-wrapper .hr {margin:25px auto;width:180px;}
            .error-page-wrapper .context {font-size:15px;line-height:22px;}
            .error-page-wrapper .context p:nth-child(n+2) {margin-top:10px;}
            .error-page-wrapper .buttons-container {margin-top:29px;}
            .error-page-wrapper .buttons-container a {float:none !important;width:65%;margin:0 auto;font-size:13px;padding:9px 0;}
            .error-page-wrapper .buttons-container a:nth-child(2) {margin-top:12px;}
        }
    </style>
</head>
<body class="error-page-wrapper">
<div class="content-container">
    <div class="head-line">
        <svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024" height="280px"  width="280px">
            <title>404</title>
            <defs>
                <linearGradient id="linearGradient-1" y2="-2.47145299e-14%" x2="50%" y1="100%" x1="50%">
                    <stop offset="0%" stop-opacity="0.1" stop-color="#FFFFFF"></stop>
                    <stop offset="13%" stop-opacity="0.19" stop-color="#FBFDFF"></stop>
                    <stop offset="41%" stop-opacity="0.43" stop-color="#F1F7FF"></stop>
                    <stop offset="81%" stop-opacity="0.82" stop-color="#E1EEFF"></stop>
                    <stop offset="100%" stop-color="#D9E9FF"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-2" y2="0.0703648757%" x2="50%" y1="100.221895%" x1="50%">
                    <stop offset="0%" stop-opacity="0.1" stop-color="#FFFFFF"></stop>
                    <stop offset="100%" stop-color="#D9E9FF"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-3" y2="0.00693856876%" x2="50.0935971%" y1="100%" x1="50.0935971%">
                    <stop offset="0%" stop-opacity="0.1" stop-color="#FFFFFF"></stop>
                    <stop offset="14%" stop-opacity="0.27" stop-color="#F8FBFF"></stop>
                    <stop offset="37%" stop-opacity="0.52" stop-color="#EDF5FF"></stop>
                    <stop offset="58%" stop-opacity="0.73" stop-color="#E4F0FF"></stop>
                    <stop offset="76%" stop-opacity="0.88" stop-color="#DEECFF"></stop>
                    <stop offset="91%" stop-opacity="0.97" stop-color="#DAEAFF"></stop>
                    <stop offset="100%" stop-color="#D9E9FF"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-4" y2="-0.0811277354%" x2="49.9839796%" y1="99.9311993%" x1="49.9839796%">
                    <stop offset="0%" stop-opacity="0.1" stop-color="#FFFFFF"></stop>
                    <stop offset="9%" stop-opacity="0.13" stop-color="#FEFEFF"></stop>
                    <stop offset="23%" stop-opacity="0.21" stop-color="#FBFCFF"></stop>
                    <stop offset="40%" stop-opacity="0.33" stop-color="#F5F9FF"></stop>
                    <stop offset="59%" stop-opacity="0.51" stop-color="#EEF5FF"></stop>
                    <stop offset="79%" stop-opacity="0.74" stop-color="#E4EFFF"></stop>
                    <stop offset="100%" stop-color="#D9E9FF"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-5" y2="100%" x2="50%" y1="0%" x1="50%">
                    <stop offset="0%" stop-color="#5BAAFD"></stop>
                    <stop offset="100%" stop-color="#AAD2FE"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-6" y2="55.3330291%" x2="59.0652557%" y1="14.9053365%" x1="50%">
                    <stop offset="0%" stop-color="#AFD5FE"></stop>
                    <stop offset="100%" stop-color="#3798FC"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-7" y2="100%" x2="50%" y1="0%" x1="50%">
                    <stop offset="0%" stop-color="#4BA2FC"></stop>
                    <stop offset="100%" stop-color="#F9FCFF"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-8" y2="54.246146%" x2="47.6845985%" y1="48.5049189%" x1="50%">
                    <stop offset="0%" stop-color="#FFFFFF"></stop>
                    <stop offset="100%" stop-color="#F7F7F7"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-9" y2="100%" x2="50%" y1="0%" x1="50%">
                    <stop offset="0%" stop-color="#DBEAFF"></stop>
                    <stop offset="100%" stop-opacity="0" stop-color="#FCFCFC"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-10" y2="100%" x2="50%" y1="0%" x1="50%">
                    <stop offset="0%" stop-color="#94C7FD"></stop>
                    <stop offset="100%" stop-color="#4AA1FC"></stop>
                </linearGradient>
                <linearGradient id="linearGradient-11" y2="100%" x2="50%" y1="0%" x1="50%">
                    <stop offset="0%" stop-color="#9ECCFD"></stop>
                    <stop offset="100%" stop-color="#3798FC"></stop>
                </linearGradient>
            </defs>
            <g fill-rule="evenodd" fill="none" stroke-width="1" stroke="none" id="404">
                <rect height="1024" width="1024" y="0" x="0" fill="#FFFFFF"></rect>
                <rect height="1024" width="1024" y="0" x="0" opacity="0" fill-rule="nonzero" fill="#D8D8D8" id="矩形"></rect>
                <ellipse ry="161" rx="511.5" cy="740" cx="511.5" fill-rule="nonzero" fill="url(#linearGradient-1)" id="椭圆形"></ellipse>
                <polygon points="199.689759 592 204 640 177 636.835786" fill-rule="nonzero" fill="#E4EFFF" id="路径"></polygon>
                <polygon points="200 592 230 629.885815 204.547188 640" fill-rule="nonzero" fill="#F3F8FF" id="路径"></polygon>
                <polygon points="99.4733067 605 105 666 71 661.931463" fill-rule="nonzero" fill="#E4EFFF" id="路径"></polygon>
                <polygon points="99 605 138 653.233212 105 666" fill-rule="nonzero" fill="#F3F8FF" id="路径"></polygon>
                <polygon points="147.629482 562 157 665 94 658.583983" fill-rule="nonzero" fill="#E4EFFF" id="路径"></polygon>
                <polygon points="147 562 214 642.944939 156.526873 665" fill-rule="nonzero" fill="#F3F8FF" id="路径"></polygon>
                <path fill-rule="nonzero" fill="url(#linearGradient-2)" id="路径" d="M167.226695,341.222807 C163.480576,321.746389 146.08963,307.737138 125.988877,308.003741 C115.19993,307.943587 104.784755,311.897648 96.822034,319.076764 C92.2984185,317.145972 87.4052017,316.20168 82.4772246,316.308508 C67.025185,316.043956 53.4620894,326.403184 49.8575214,341.222807 C37.9307462,343.823137 29.3304483,354.09619 29,366.137107 C29.4585953,380.789727 41.8172108,392.334144 56.6790255,391.992614 L160.320975,391.992614 C175.182789,392.334144 187.541405,380.789727 188,366.137107 C187.688822,354.117261 179.125664,343.847106 167.226695,341.222807 L167.226695,341.222807 Z"></path>
                <path fill-rule="nonzero" fill="url(#linearGradient-3)" id="路径" d="M767.894611,204.336433 C762.536654,196.115984 753.472954,191.013642 743.619099,190.670714 C737.903428,181.978635 728.08888,176.816394 717.637588,177.004996 C709.252264,176.915815 701.185414,180.190583 695.263843,186.087699 C692.550344,185.230902 689.721014,184.790673 686.873688,184.782234 C673.701145,184.782234 662.793945,193.837161 661.703226,205.280812 C651.661814,207.65777 644.431924,216.36957 644,226.612664 C644,238.945142 655.382644,249 669.170462,249 L765.82504,249 C779.808631,249 790.995504,238.945142 790.995504,226.612664 C791.247208,214.891255 780.899351,205.197484 767.894611,204.336433 Z"></path>
                <path fill-rule="nonzero" fill="url(#linearGradient-4)" id="路径" d="M948.379644,171.742851 C938.847302,152.167323 918.946836,139.710452 897.098611,139.643039 C884.929877,139.669114 873.093282,143.597853 863.342582,150.847162 C851.739988,128.313014 826.541826,116.167009 801.594253,121.083265 C776.646683,125.999521 757.994084,146.78691 755.885893,172.022954 C729.0491,180.455135 712.396536,207.114063 716.665047,234.811219 C720.93356,262.508375 744.847818,282.9685 772.988945,283 L931.867321,283 C960.171215,283.053292 984.230271,262.424683 988.379199,234.545632 C992.528127,206.666579 975.510913,179.976968 948.407776,171.854893 L948.379644,171.742851 Z"></path>
                <polygon points="904 605 899.151453 669 863 658.522625" fill-rule="nonzero" fill="#E4EFFF" id="路径"></polygon>
                <polygon points="903.81884 605 934 661.390417 899 669" fill-rule="nonzero" fill="#F3F8FF" id="路径"></polygon>
                <g fill-rule="nonzero" transform="translate(264.000000, 283.000000)" id="编组-7">
                    <g transform="translate(246.859435, 240.672712) rotate(-15.000000) translate(-246.859435, -240.672712) translate(45.177617, 46.134934)" id="编组-3">
                        <path fill="url(#linearGradient-5)" id="形状结合" d="M202.235615,0.0796794163 C208.688732,0.0796794163 213.920017,5.33894614 213.920017,11.8265805 C213.920017,17.5090882 209.906587,22.2491951 204.574027,23.3381882 L204.572495,40.0191428 L199.898734,40.0191428 L199.898553,23.3384638 C194.56532,22.2499953 190.551212,17.5095679 190.551212,11.8265805 C190.551212,5.33894614 195.782497,0.0796794163 202.235615,0.0796794163 Z"></path>
                        <ellipse ry="82.2283073" rx="98.1489844" cy="112.849929" cx="202.235615" fill="url(#linearGradient-6)" id="椭圆形"></ellipse>
                        <ellipse ry="70.4814062" rx="200.97173" cy="166.885674" cx="202.235615" fill="url(#linearGradient-7)" id="椭圆形"></ellipse>
                        <path fill="url(#linearGradient-8)" id="形状结合" d="M351.216598,179.052682 C351.419311,175.16544 357.225545,172.311605 364.185369,172.678169 C371.145192,173.044733 376.623033,176.493377 376.420322,180.380619 C376.217609,184.267861 370.411375,187.121696 363.451551,186.755132 C356.491728,186.388568 351.013887,182.939924 351.216598,179.052682 Z M55.4421222,167.592519 C62.3595405,166.738624 68.3495642,169.178465 68.8215985,173.042009 C69.2936328,176.905554 64.0683052,180.729833 57.1508869,181.583729 C50.2334685,182.437624 44.2434449,179.997782 43.7714106,176.134238 C43.2993763,172.270693 48.5247039,168.446414 55.4421222,167.592519 Z M249.00205,144.634639 C249.339904,138.155902 259.016962,133.39951 270.616666,134.01045 C282.21637,134.62139 291.346107,140.369129 291.008254,146.847866 C290.670397,153.326602 280.993342,158.082995 269.393638,157.472055 C257.793932,156.861114 248.664195,151.113375 249.00205,144.634639 Z M147.875847,129.31169 C159.475551,128.700749 169.152608,133.457142 169.490463,139.935878 C169.828318,146.414615 160.69858,152.162354 149.098876,152.773294 C137.499171,153.384235 127.822114,148.627842 127.48426,142.149105 C127.146405,135.670369 136.276142,129.92263 147.875847,129.31169 Z"></path>
                        <ellipse ry="25.8431824" rx="107.184923" cy="188.030096" cx="202.235615" fill="#FFFFFF" id="椭圆形复制-6"></ellipse>
                        <path fill="url(#linearGradient-9)" id="路径-2" d="M132.112542,176.993646 L11.4781608,358.119119 C130.557912,390.70539 253.700248,396.361118 380.905168,375.086306 C381.508897,375.207226 345.110296,309.133347 271.709369,176.864667 L200.075527,176.930852 L132.112542,176.993646 Z"></path>
                        <g transform="translate(100.814647, 194.687691)" id="编组-8">
                            <polygon points="60.24994 40.8473381 31.1998956 84.6474524 39.2021068 86.2457485 42.3626442 70.1607687 67.2098464 75.1596949 64.0493092 91.2446747 73.4636753 93.1490275 69.6306835 112.838675 60.2163171 110.934322 57.0221573 127.121321 32.1749549 122.122395 35.369115 105.935396 0.166109755 98.8620857 3.66287435 80.9747722 34.595792 35.6783806" fill="url(#linearGradient-10)" id="路径"></polygon>
                            <path fill="url(#linearGradient-11)" id="形状" d="M137.769299,52.8553632 C130.647723,80.1541249 114.878517,92.0844402 93.5816104,86.3771423 C72.0473187,80.601082 64.2474962,62.3789866 71.369073,35.0802249 C78.4906501,7.78146334 94.158119,-4.1488519 115.692411,1.59282719 C136.989317,7.33450628 144.890876,25.5566015 137.769299,52.8553632 Z M97.040662,41.9564874 C92.496418,59.4221939 92.5642425,66.2984561 98.4310653,67.8456153 C104.162239,69.3927742 107.62129,63.4448072 112.165535,45.979101 C116.743691,28.3758691 116.641955,21.637132 110.910781,20.1243543 C105.010046,18.542814 101.618819,24.3532557 97.040662,41.9564874 L97.040662,41.9564874 Z M182.024813,40.9594294 L161.813099,90.193468 L170.053781,90.193468 L170.053781,73.6216757 L195.589721,73.6216757 L195.589721,90.193468 L205.288631,90.193468 L205.288631,110.478442 L195.589721,110.478442 L195.589721,127.18776 L170.053781,127.18776 L170.053781,110.478442 L133.835476,110.478442 L133.835476,92.0500589 L155.641066,40.9594294 L182.024813,40.9594294 L182.024813,40.9594294 Z"></path>
                        </g>
                    </g>
                </g>
            </g>
        </svg>
    </div>
    <div class="subheader">
        <?=$debug?$message:$lang['The page you are looking for is temporarily unavailable']?>
    </div>
    <div class="hr"></div>
    <div class="context">

        <p>
            <?=$lang['You can return to the previous page and try again']?>
        </p>

    </div>
    <div class="buttons-container">
        <a href="<?=$publicurl?>"><?=$lang['Home']?></a>
        <a href="javascript:" onclick="history.go(-1)"><?=$lang['Previous Page']?></a>
    </div>
</div>
</body>
</html>
