package net.jwdouglas.hast.mdns;

import java.io.IOException;
import java.net.InetAddress;

import javax.jmdns.JmDNS;
import javax.jmdns.ServiceInfo;

import net.jwdouglas.hast.Server;

public class ServiceMDNS {
    private static JmDNS       jmdns       = null;
    private static ServiceInfo serviceInfo = ServiceInfo.create("_http._tcp.local.", "raspserv", 1234, "path=index.html");
	
	public static void load() {
        try {
        	jmdns = JmDNS.create(InetAddress.getLocalHost(), "hast");
        	jmdns.registerService(serviceInfo);
		} catch (IOException e) {
			e.printStackTrace();
			Server.stop();
		}
	}

	public static void stop() {
		if(jmdns != null) {
			try {
				jmdns.unregisterAllServices();
				jmdns.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}
}
