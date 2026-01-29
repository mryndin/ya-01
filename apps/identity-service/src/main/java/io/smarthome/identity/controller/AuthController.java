package io.smarthome.identity.controller;

import io.smarthome.identity.repository.UserRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Map;

@RestController
@RequestMapping("/auth")
public class AuthController {

    @Autowired
    private UserRepository userRepository;

    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody Map<String, String> credentials) {
        String login = credentials.get("login");
        String password = credentials.get("password");

        return userRepository.findByLogin(login)
            .filter(user -> user.getPassword().equals(password))
            .map(user -> ResponseEntity.ok(Map.of(
                "token", "mock-jwt-token",
                "role", user.getRole()
            )))
            .orElse(ResponseEntity.status(401).build());
    }
}