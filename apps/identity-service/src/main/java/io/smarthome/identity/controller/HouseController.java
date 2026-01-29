package io.smarthome.identity.controller;

import io.smarthome.identity.model.House;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.UUID;

@RestController
@RequestMapping("/api/v1/houses")
public class HouseController {

    // В реальности здесь будет инъекция сервиса и репозитория
    @GetMapping
    public ResponseEntity<List<House>> getUserHouses() {
        // Логика получения домов текущего пользователя из таблицы user_houses 
        return ResponseEntity.ok(List.of(
            new House(UUID.randomUUID(), "Моя Дача", "Москва", "ул. Лесная 1")
        ));
    }

    @PostMapping
    public ResponseEntity<House> createHouse(@RequestBody House house) {
        // Логика создания записи в таблице houses 
        house.setId(UUID.randomUUID());
        return ResponseEntity.status(201).body(house);
    }
}