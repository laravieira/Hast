package net.jwdouglas.hast.spring;

import org.springframework.boot.context.event.ApplicationFailedEvent;
import org.springframework.boot.context.event.ApplicationReadyEvent;
import org.springframework.boot.context.event.ApplicationStartingEvent;
import org.springframework.context.ApplicationListener;

import net.jwdouglas.hast.TheLogger;

public class Listenners {}

class ListennerSuccess implements ApplicationListener<ApplicationReadyEvent> {
	
	@Override
	public void onApplicationEvent(ApplicationReadyEvent event) {
		TheLogger.getLogger().info("This is a log reseted.");
	}
}

class ListennerFail implements ApplicationListener<ApplicationFailedEvent> {

	@Override
	public void onApplicationEvent(ApplicationFailedEvent event) {
		TheLogger.getLogger().info("Spring fail on start.");
	}
}

class ListennerStarting implements ApplicationListener<ApplicationStartingEvent> {
	
	@Override
	public void onApplicationEvent(ApplicationStartingEvent event) {
		TheLogger.getLogger().info("Spring is starting.");
	}
	
}
