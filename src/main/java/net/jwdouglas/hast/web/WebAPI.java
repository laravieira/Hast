package net.jwdouglas.hast.web;

import java.util.Date;

public class WebAPI {
	private User   user = null;
	private String id   = null;
	
    public WebAPI() {
    	id = new Date().getTime()+"";
    }

    public User setUser(String email) {
    	user = new User(email);
    	return user;
    }
    
    public User getUser(String email) {
    	return user;
    }
    
    public String getID() {
    	return id;
    }
}
