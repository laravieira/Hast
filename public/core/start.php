<?PHP

include "logger.php";
include "language.php";
include "update.php";
include "database.php";
include "notification.php";

// Default file
$default_file = 
"# ---------------------------------------------------------------- #
# |                    INTELIGENCY HOME SYSTEM                   | #
# |                         Config File                          | #
# |                                                              | #
# |    All editable settings are below up there are write how    | #
# | use them. If have any error on this file, defaults settings  | #
# | will be loaded and a notification will appear on dashboard.  | #
# | To restore all settings, delete this file and a new default  | #
# | file will be create.                                         | #
# |                                                              | #
# | For more information, contact support: contato@jwdouglas.net | #
# |                                                              | #
# | Version of this reliase: 1.0                                 | #
# ---------------------------------------------------------------- #

# The language of all texts, titles, buttons, this file will stay in english.
# To choose us english language, write \"en-us\"
# Para escolher português brasileiro, escreva \"pt-br\"
system_language: en-us

# The name of system (This name will appear on titles, headers and footers of pages).
system_name: Kaza Cultura

# The link to access the dashboard, keep the '/' (bar) character on end link.
system_url: http://kazacultura.lh/
system_update_check: true
system_update_auto: false

# Set timezone, for timezone list, visit http://php.net/manual/pt_BR/timezones.php
system_timezone: America/Sao_Paulo

# ---------------------------------------------------------------- #
#           It's not recommend you edit the lines below            #
#    If you want to procede you need to know what are you doing!   #
# ---------------------------------------------------------------- #

# Database type, only suported is mysql.
database_type: mysql

# Database access, host information, database name, database user and your password. In this database are all information about user, system, sensors settings...
database_general_host: localhost
database_general_name: kazacultura_general
database_general_user: root
database_general_pass:

# Database to save all sensors data, settings information will not save here.
database_data_host: localhost
database_data_name: kazacultura_sensors
database_data_user: root
database_data_pass:


";

// Try to read settings
if(file_exists("config.yml")) {
    $GLOBALS["config"] = yaml_parse_file("config.yml");
    if($GLOBALS["config"] === false) {
        $GLOBALS["config"] = yaml_parse($default_file);
        date_default_timezone_set($GLOBALS["config"]["system_timezone"]);
        loggerEcho("Starting system...");
        loggerEcho("Fail try to open config.yml file, loaded with default settings.");
    }else {
        date_default_timezone_set($GLOBALS["config"]["system_timezone"]);
        loggerEcho("Starting system...");
        loggerEcho("Settings from config.yml has been loaded.");
    }
}else {
    $GLOBALS["config"] = yaml_parse($default_file);
    date_default_timezone_set($GLOBALS["config"]["system_timezone"]);
    loggerEcho("The config.yml file don't exist, trying to replace with default config file.");
    $file = fopen("config.yml", "w");
    fwrite($file, $default_file);
    fclose($file);
    loggerEcho("Default settings has been loaded.");
}

//echo "<br>...".$GLOBALS["config"]["file_jsZip"]."...<br>";
//$file = fopen("variables.json", "w");
//fwrite($file, json_encode($GLOBALS["config"]));
//fclose($file);
//loggerEcho("All variables settings has been setted.");

// Try to load language pack
loadLanguage();

// Check databases
if(checkDatabases())
    loggerEcho("Databases are ok.");
else
    loggerEcho("Something is wrong with databases, please check them.");

saveLog();

?>