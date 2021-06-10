package net.jwdouglas.hast.user.service;

import java.util.logging.Logger;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.stereotype.Service;

import net.jwdouglas.hast.TheLogger;

@Service
public class SecurityServiceImpl implements SecurityService {

	@Autowired
	private AuthenticationManager authManager;
	@Autowired
	private UserDetailsService userDetailsService;
	private static final Logger log = TheLogger.getConsoleLogger();
	
	@Override
	public String findLoggedInUsername() {
		Object userDetails = SecurityContextHolder.getContext().getAuthentication().getDetails();
		if(userDetails instanceof UserDetails) {
			return ((UserDetails)userDetails).getUsername();
		}
		return null;
	}

	@Override
	public void autoLogin(String username, String password) {
		UserDetails userDetails = userDetailsService.loadUserByUsername(username);
		UsernamePasswordAuthenticationToken upat = new UsernamePasswordAuthenticationToken(userDetails,  password, userDetails.getAuthorities());
		authManager.authenticate(upat);
		if(upat.isAuthenticated()) {
			SecurityContextHolder.getContext().setAuthentication(upat);
			log.info("Auto login "+username+" successfully!");
		}
	}
			

}
