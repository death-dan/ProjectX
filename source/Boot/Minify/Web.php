<?php
if (strpos(url(), "localhost")) {

    /**
     * CSS
     */
    $minCss = new MatthiasMullie\Minify\CSS();
    $minCss->add(__DIR__ . "/../../../shared/styles/styles.css");
    $minCss->add(__DIR__ . "/../../../shared/styles/boot.css");

    //Theme CSS
    $cssDir = scandir(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/css");
    foreach ($cssDir as $css) {
        $cssFile = __DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/css/{$css}";
        if (is_file($cssFile) && pathinfo($cssFile)['extension'] == "css") {
            $minCss->add($cssFile);
        }
    }

    //Minify CSS
    $minCss->minify(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/style.css");


    /**
     * JS
     */
    $minJs = new MatthiasMullie\Minify\JS();
    $minJs->add(__DIR__ . "/../../../shared/scripts/jquery.min.js");
    $minJs->add(__DIR__ . "/../../../shared/scripts/jquery.form.js");
    $minJs->add(__DIR__ . "/../../../shared/scripts/jquery-ui.js");
    $minJs->add(__DIR__ . "/../../../shared/scripts/tracker.js");

    //Theme JS
    $jsDir = scandir(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/js");
    foreach ($jsDir as $js) {
        $jsFile = __DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/js/{$js}";
        if (is_file($jsFile) && pathinfo($jsFile)['extension'] == "js") {
            $minJs->add($jsFile);
        }
    }

    //Minify JS
    $minJs->minify(__DIR__ . "/../../../themes/" . CONF_VIEW_THEME . "/assets/scripts.js");
}