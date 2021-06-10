package net.jwdouglas.hast.spring;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.config.annotation.web.configuration.WebSecurityConfigurerAdapter;
import org.springframework.ui.Model;
import org.springframework.validation.BindingResult;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.ModelAttribute;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RestController;

import net.jwdouglas.hast.user.model.User;
import net.jwdouglas.hast.user.service.SecurityService;
import net.jwdouglas.hast.user.service.UserService;
import net.jwdouglas.hast.user.validator.UserValidator;

@RestController
public class WebManager extends WebSecurityConfigurerAdapter {
	@Autowired
	private UserService userService;
	@Autowired
	private SecurityService securityService;
	@Autowired
	private UserValidator userValidate;

	@GetMapping({"/", "/welcome"})
	public String welcome(Model model) {
		return "welcome";
	}

	@GetMapping("/login")
	public String login(Model model, String error, String logout) {
		if(error != null) {
			model.addAttribute("error", "Your username with your password are invalid.");
		}
		if(logout != null) {
			model.addAttribute("message", "You have been logged out soccessfully.");
		}
		return "login";
	}
	
	@GetMapping("/registration")
	public String registration(Model model) {
		model.addAttribute("userForm", new User());
		return "registration";
	}

	@PostMapping("/registration")
	public String registration(@ModelAttribute("userForm") User userForm, BindingResult bindingResult) {
		userValidate.validate(userForm, bindingResult);
		if(bindingResult.hasErrors()) {
			return "registration";
		}
		userService.save(userForm);
		securityService.autoLogin(userForm.getUsername(), userForm.getPasswordConfirm());
		return "redirect:/welcome";
	}
	
//    @PostMapping("/api/user/login")
//    public ResponseEntity<User> postRequest(@RequestParam("email") String email, @RequestParam("pass") String pass) throws IOException {
//    	User user = new WebAPI().setUser(email);
//    	if(user.doLogin(pass)) {
//    		return ResponseEntity.ok(user);
//    	}else {
//    		return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(user);
//    	}
//    }

}