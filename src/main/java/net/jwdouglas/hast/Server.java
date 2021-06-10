package net.jwdouglas.hast;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.logging.Logger;

import net.jwdouglas.hast.mdns.ServiceMDNS;
import net.jwdouglas.hast.spring.Spring;


public class Server {
    private static BufferedReader input = new BufferedReader(new InputStreamReader(System.in));
	private static boolean        stop  = false;
    private static Logger         log   = null;
	
    
    // --------------------------------------------------- //
    //                  Server Begin Here                  //
    // --------------------------------------------------- //
    
	private static void begin() {
		//ServiceMDNS.load();
		Spring.load();
	}
	
	private static void loop() {
		
	}
	
	private static void end() {
		Spring.stop();
		ServiceMDNS.stop();
	}
    
    // --------------------------------------------------- //
    //                   Server End Here                   //
    // --------------------------------------------------- //
    
	
    public static void main(String args[]) throws InterruptedException {
    	TheLogger.load();
    	log = TheLogger.getLogger();
		begin();
    	
		lineReader();
    	log.info("The server is all loaded!");
    	
    	while(!stop) {loop(); Thread.sleep(1);}

    	end();
    	log.info("Server stopped.");
    	TheLogger.close();
    }
    
    private static void lineReader() {
    	Thread line = new Thread(() -> {
    		try {
				while(!stop) {
					if(input.ready()) {
						String[] commandLine = input.readLine().split(" ");
						if(commandLine.length > 0) {
							new Command().onCommand(commandLine);
						}
					}
				}
			} catch (IOException e) {
				e.printStackTrace();
			}
    	});
    	line.setDaemon(true);
    	line.start();
    }
    
    public static void stop() {
    	stop = true;
    }
}
