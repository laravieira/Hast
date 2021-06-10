package net.jwdouglas.hast.user.service;

import net.jwdouglas.hast.user.model.User;

public interface UserService {
	void save(User user);
	User findByUsername(String username);
}
