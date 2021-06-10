package net.jwdouglas.hast.web;

public class User {
	
	private static String  _name  = null;
	private static String  email = null;
	private boolean logged = false;
	
	public User(String _email) {
		email = _email;
	}
	
    public boolean doLogin(String pass) {
    	if(pass.equals("a1234")) {
    		logged = true;
    		return true;
    	}else {
    		return false;
    	}
    }
    
    public String getEmail() {
    	return email;
    }
    
    public String getName() {
    	return _name;
    }

    public boolean isLogged() {
    	return logged;
    }
}
