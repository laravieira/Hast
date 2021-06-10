<?PHP

$GLOBALS["LANG"] = array();

function loadLanguage($root, $lang) {
    $GLOBALS["LANG"] = yaml_parse_file($root."core/languages/".$lang.".lang.yml");
    if($GLOBALS["LANG"])
        return true;
    else
        return false;
}

function lang($key) {
    return $GLOBALS["LANG"][$key];
}

function echoLang($key) {
    echo lang($key);
}



?>