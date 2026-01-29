package io.smarthome.identity.repository;

import io.smarthome.identity.model.User;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.Optional;
import java.util.UUID;

@Repository
public interface UserRepository extends JpaRepository<User, UUID> {
    // Автоматически создаст запрос: SELECT * FROM users WHERE login = ?
    Optional<User> findByLogin(String login);
}