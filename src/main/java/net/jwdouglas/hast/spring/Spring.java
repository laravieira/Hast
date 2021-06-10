package net.jwdouglas.hast.spring;

import java.io.PrintStream;
import java.util.Properties;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.Banner;
import org.springframework.boot.Banner.Mode;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.builder.SpringApplicationBuilder;
import org.springframework.context.ConfigurableApplicationContext;
import org.springframework.context.annotation.Bean;
import org.springframework.core.env.Environment;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.config.annotation.authentication.builders.AuthenticationManagerBuilder;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.annotation.web.configuration.EnableWebSecurity;
import org.springframework.security.config.annotation.web.configuration.WebSecurityConfigurerAdapter;
import org.springframework.security.core.userdetails.UserDetailsService;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;

import net.jwdouglas.hast.user.service.UserService;

@SpringBootApplication
@EnableWebSecurity
public class Spring extends WebSecurityConfigurerAdapter {

	@Autowired
	private        UserDetailsService             userDetailsService;
	@Autowired
	private        UserService                    userService;
	private static ConfigurableApplicationContext spring;

	public static boolean load() {
		
		
		Properties properties = new Properties(System.getProperties());
		properties.setProperty("org.springframework.boot.logging.LoggingSystem", "none");
		System.setProperties(properties);

		SpringApplicationBuilder sab = new SpringApplicationBuilder(Spring.class);
		sab.addCommandLineProperties(false);

		sab.properties("server.port:80");
		//sab.properties("server.session.cookie.name:USESSIONID");
		//sab.properties("security.basic.enabled:false");
		sab.properties("spring.jpa.hibernate.ddl-auto=create");
		sab.properties("spring.jpa.show-sql=true");
		sab.properties("spring.mvc.view.prefix: /");
		sab.properties("spring.mvc.view.suffix: .jsp");
		sab.properties("spring.messages.basename=validation");
		sab.properties("NotEmpty=This field is required.");
		sab.properties("Size.userForm.username=Please use between 6 and 32 characters.");
		sab.properties("Duplicate.userForm.username=Someone already has that username.");
		sab.properties("Size.userForm.password=Try one with at least 8 characters.");
		sab.properties("Diff.userForm.passwordConfirm=These passwords don't match.");
		sab.registerShutdownHook(true);
		
		sab.listeners(new ListennerSuccess());
		sab.listeners(new ListennerFail());
		sab.listeners(new ListennerStarting());
		
		sab.bannerMode(Mode.OFF);
		sab.banner(getBanner());

		spring = sab.run();
		return true;
	}
	
	public static void stop() {
		spring.close();
	}

//    public void configure(HttpSecurity http) throws Exception {
//    	String[] access = {"/", "/api/user/login", "/login.html", "/login", "/login/"};
//        http.antMatcher("/**").authorizeRequests().antMatchers(access).permitAll().anyRequest().authenticated();
//    }
	
	@Bean
	protected UserDetailsService userDetailsService() {
		return super.userDetailsService();
	}
	
	@Bean
	public BCryptPasswordEncoder bCryptPasswordEncoder() {
		return new BCryptPasswordEncoder();
	}
	
	@Bean
	protected UserService userService() {
		return userService;
	}
	
	@Bean
	public AuthenticationManager customAuthenticationManager() throws Exception {
		return customAuthenticationManager();
	}
	
	@Autowired
	public void configureGlobal(AuthenticationManagerBuilder auth) throws Exception {
		auth.userDetailsService(userDetailsService).passwordEncoder(bCryptPasswordEncoder());
	}
	
	@Override
	protected void configure(HttpSecurity http) throws Exception {
		http
			.authorizeRequests()
				.antMatchers("/resources/**", "/registration").permitAll()
				.anyRequest().authenticated()
				.and()
			.formLogin()
				.loginPage("/login")
				.permitAll()
				.and()
			.logout()
				.permitAll();
	}
    
	public static Banner getBanner() {
		return new Banner() {			
			public void printBanner(Environment environment, Class<?> sourceClass, PrintStream out) {
				out.println();
				out.println("  ___     ___     _______      _________   ___________  ");
				out.println(" |   |   |   |   /       \\    /  _______| |           | ");
				out.println(" |   |   |   |  /   / \\   \\  |  /         |___     ___| ");
				out.println(" |   |___|   | |   |___|   | |  \\______       |   |     ");
				out.println(" |    ___    | |    ___    |  \\______  \\      |   |     ");
				out.println(" |   |   |   | |   |   |   |         \\  |     |   |     ");
				out.println(" |   |   |   | |   |   |   |  _______/  |     |   |     ");
				out.println(" |___|   |___| |___|   |___| |_________/      |___|     ");
				out.println(" Home Assistant for Sensors and Triggers v0.0.1-SNAPSHOT");
				out.println();
			}
		};
	}

}