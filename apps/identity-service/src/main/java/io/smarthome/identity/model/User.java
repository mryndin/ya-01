package io.smarthome.identity.model;

import jakarta.persistence.*;
import lombok.*;
import java.util.UUID;

@Data
@Entity
@Table(name = "users")
@NoArgsConstructor
@AllArgsConstructor
public class User {
    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private UUID id;
    
    @Column(unique = true, nullable = false)
    private String login;
    
    @Column(name = "password_hash", nullable = false)
    private String password;
    
    private String role; // ADMIN или USER
}