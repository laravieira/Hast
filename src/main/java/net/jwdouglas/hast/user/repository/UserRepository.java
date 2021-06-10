package net.jwdouglas.hast.user.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import net.jwdouglas.hast.user.model.User;

public interface UserRepository extends JpaRepository<User, Long> {
	
	User findByUsername(String username);

}
