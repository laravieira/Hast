<?PHP

define("LOGGER_INFO",  "[INFO]");
define("LOGGER_WARN",  "[WARN]");
define("LOGGER_ERROR", "[ERROR]");
$GLOBALS["LOGGER"] = array();

function saveLog() {
    $file = fopen("logs/".date("Y-m-d").".log", "a");
    if(PATH_SEPARATOR == ";")
		$enter_path = "\r\n";
	else
		$enter_path = "\n";
    foreach($GLOBALS["LOGGER"] as $line)
        fwrite($file, $line.$enter_path);
    fclose($file);
    $GLOBALS["LOGGER"] = array();
}

function logger($message, $type=LOGGER_INFO) {
    $GLOBALS["LOGGER"][] = "[".date("Y-m-d H:i:s")."]".$type." ".$message;
}

function loggerEcho($message, $type=LOGGER_INFO) {
    logger($message, $type);
    if(PATH_SEPARATOR == ";")
        $enter_path = "\r\n";
    else
        $enter_path = "\n";
    echo "[".date("Y-m-d H:i:s")."]".$type." ".$message.$enter_path;
}

?>