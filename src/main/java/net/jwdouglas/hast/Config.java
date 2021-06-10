package net.jwdouglas.hast;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Map;
import java.util.logging.Logger;

import org.yaml.snakeyaml.Yaml;
import org.yaml.snakeyaml.error.YAMLException;

public class Config {
	private static Logger     log    = TheLogger.getLogger();
	private static GetterYAML config = null;
	
	private static boolean setConfigs(File file) throws FileNotFoundException {
		log.config("Checking global configuration.");
		config = new GetterYAML(file);
		if(config.load()) {
			
			// Check each setting on configuration file
			if(!config.hasString( "server.name"))         return false;
			if(!config.hasString( "server.password"))     return false;
			if(!config.hasString( "server.log"))          return false;
			if(!config.hasBoolean("server.check.update")) return false;
			
			if(!config.hasString( "database.mode"))       return false;
			if(!config.hasString( "database.host"))       return false;
			if(!config.hasInt(    "database.port"))       return false;
			if(!config.hasString( "database.name"))       return false;
			if(!config.hasString( "database.user"))       return false;
			if(!config.hasString( "database.pass"))       return false;

			if(!config.hasBoolean("http.use"))            return false;
			if(!config.hasInt(    "http.port"))           return false;
			if(!config.hasInt(    "https.port"))          return false;
			
			if(!config.hasString( "mqtt.host"))           return false;
			if(!config.hasInt(    "mqtt.port"))           return false;
			if(!config.hasString( "mqtt.user"))           return false;
			if(!config.hasString( "mqtt.pass"))           return false;
			

			if(!config.hasBoolean("mdns.use"))            return false;
			if(!config.hasString( "mdns.nick"))           return false;
			
			log.info("All settings has been loaded.");
			return true;
		}else {
			return false;
		}
	}
	
	public static boolean eula() {
		log.config("Checking EULA file");
		File eulaFile = new File("eula.yml");
		if(eulaFile.exists() && eulaFile.isFile()) {
			try {
				GetterYAML eula = new GetterYAML(eulaFile);
				eula.load();
				if(!eula.getBoolean("agree")) {
					log.warning("You need to agree EULA terms on eula.yml file.");
					return false;
				}else {
					return true;
				}
			}catch(YAMLException e) {
				log.severe("Can't read the eula file. Please check it or delete it.");
				log.severe(e.getMessage());
				return false;
			}
		}else {
			String seula = "";
			seula += "# ---------------------------------------------------------- #\r\n";
			seula += "#             End User License Agreement (EULA)              #\r\n";
			seula += "#                                                            #\r\n";
			seula += "#              Read our EULA page before agree!              #\r\n";
			seula += "#                                                            #\r\n";
			seula += "#                                                            #\r\n";
			seula += "#               http://hast.jwdouglas.net/eula               #\r\n";
			seula += "#                                                            #\r\n";
			seula += "#       On EULA you will find our license information,       #\r\n";
			seula += "#   our privace policy and LGPD terms (for Brazil polices).  #\r\n";
			seula += "#                                                            #\r\n";
			seula += "# ---------------------------------------------------------- #\r\n";
			seula += "\r\n";
			seula += "agree: false";
			seula += "\r\n";
			FileWriter eulaWriter;
			try {
				eulaWriter = new FileWriter(eulaFile);
				eulaWriter.write(seula);
				eulaWriter.close();
				log.warning("EULA file has been created on this server directory, please accept it.");
			} catch (IOException e) {
				log.severe("Can't create the EULA file, please check write and read system permissions.");
				log.severe(e.getMessage());
			}
			return false;
		}
	}

	public static boolean load() {
		log.config("Loading global cofiguration.");
		File confFile = new File("config.yml");
		if(confFile.exists() && confFile.isFile()) {
			try {
				if(!setConfigs(confFile)) {
					log.severe("Can't read the config file. Please check it or delete it.");
					return false;
				}else return true;
			} catch (YAMLException | FileNotFoundException e) {
				log.severe("Can't read the config file. Please check it or delete it.");
				log.severe(e.getMessage());
				return false;
			}
		}else {
			
			String sconf = "";
			sconf += "# ---------------------------------------------------------- #\r\n";
			sconf += "#                                                            #\r\n";
			sconf += "#                  HAST Server Config File                   #\r\n";
			sconf += "#                                                            #\r\n";
			sconf += "#                                                            #\r\n";
			sconf += "#       For help check http://hast.jwdouglas.net/Doc/        #\r\n";
			sconf += "#                                                            #\r\n";
			sconf += "# ---------------------------------------------------------- #\r\n";
			sconf += "\r\n";
			sconf += "\r\n";
			sconf += "server.name: HAST Server\r\n";
			sconf += "server.password: hastpassword\r\n";
			sconf += "server.log: normal # [debug, normal]\r\n";
			sconf += "server.check.update: true";
			sconf += "\r\n";
			sconf += "database.mode: memory # [file, memory, mysql]\r\n";
			sconf += "database.host: localhost\r\n";
			sconf += "database.port: 3306\r\n";
			sconf += "database.name: hastserver\r\n";
			sconf += "database.user: root\r\n";
			sconf += "database.pass: \r\n";
			sconf += "\r\n";
			sconf += "http.use: true\r\n";
			sconf += "http.port: 8080\r\n";
			sconf += "https.port: 8443\r\n";
			sconf += "\r\n";
			sconf += "mqtt.host: localhost\r\n";
			sconf += "mqtt.port: 1883\r\n";
			sconf += "mqtt.user: hastserver\r\n";
			sconf += "mqtt.pass: \r\n";
			sconf += "\r\n";
			sconf += "mdns.use: true\r\n";
			sconf += "mdns.nick: hastserver\r\n";
			
			try {
				FileWriter confWriter = new FileWriter(confFile);
				confWriter.write(sconf, 0, sconf.length());
				confWriter.close();
				log.warning("Config file has been created on this server directory.");
				log.warning("Setup config file and restart this server to apply new configs.");
				if(!setConfigs(confFile)) {
					return false;
				}else return true;
			} catch (IOException e) {
				log.severe("Can't create the config file, please check write and read system permissions.");
				log.severe(e.getMessage());
				return false;
			}
		}
	}

	public String getServerName() {
		return config.getString("server.name");
	}
	public String getServerPassword() {
		return config.getString("server.password");
	}
	public String getLogLevel() {
		return config.getString("server.log");
	}
	public String getDBMode() {
		return config.getString("database.mode");
	}
	public String getDBHost() {
		return config.getString("database.host");
	}
	public int getDBPort() {
		return config.getInt("database.port");
	}
	public String getDBName() {
		return config.getString("database.name");
	}
	public String getDBUser() {
		return config.getString("database.user");
	}
	public String getDBPass() {
		return config.getString("database.pass");
	}
	public boolean getHTTPUse() {
		return config.getBoolean("http.use");
	}
	public int getHTTPPort() {
		return config.getInt("http.port");
	}
	public int getHTTPSPort() {
		return config.getInt("https.port");
	}
	public String getMQTTHost() {
		return config.getString("mqtt.host");
	}
	public int getMQTTPort() {
		return config.getInt("mqtt.port");
	}
	public String getMQTTUser() {
		return config.getString("mqtt.user");
	}
	public String getMQTTPass() {
		return config.getString("mqtt.pass");
	}
	public boolean getMDNSUse() {
		return config.getBoolean("mdns.use");
	}
	public String getMDNSNick() {
		return config.getString("mdns.nick");
	}
	//private Double doNotUseThis1() {
	//	return config.getDouble("");
	//}

}

class GetterYAML {
	private static Map<String, Object> yaml  = null;
	private        File                _file = null;
	
	public GetterYAML(File file) {
		if(file.exists() && file.isFile())
			_file = file;
	}
	
	public boolean load() {
		if(_file == null) return false;
		try {
			yaml = new Yaml().load(new FileReader(_file));
			return true;
		} catch (YAMLException | FileNotFoundException e) {
			return false;
		}
	}
	
	public boolean hasString(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return true;
			if(object instanceof String)
				return true;
			else return false;
		}else return false;
	}
	
	public boolean hasBoolean(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return false;
			if(object instanceof Boolean)
				return true;
			else return false;
		}else return false;
	}
	
	public boolean hasInt(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return false;
			if(object instanceof Integer)
				return true;
			else return false;
		}else return false;
	}
	
	public boolean hasDouble(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return false;
			if(object instanceof Double)
				return true;
			else return false;
		}else return false;
	}

	public String getString(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return "";
			if(object instanceof String)
				return (String)object;
			else return "";
		}else return "";
	}
	
	public boolean getBoolean(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return false;
			if(object instanceof Boolean)
				return (boolean)object;
			else return false;
		}else return false;
	}

	public int getInt(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return 0;
			if(object instanceof Integer)
				return (int)object;
			else return 0;
		}else return 0;
	}
	
	public double getDouble(String key) {
		if(yaml.containsKey(key)) {
			Object object = yaml.get(key);
			if(object == null) return 0;
			if(object instanceof Double)
				return (double)object;
			else return 0;
		}else return 0;
	}

}
