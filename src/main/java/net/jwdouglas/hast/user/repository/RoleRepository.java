package net.jwdouglas.hast.user.repository;

import org.springframework.data.jpa.repository.JpaRepository;

import net.jwdouglas.hast.user.model.Role;

public interface RoleRepository extends JpaRepository<Role, Long> {
}
