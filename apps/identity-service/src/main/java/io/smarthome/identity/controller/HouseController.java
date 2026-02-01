package io.smarthome.identity.controller;

import io.smarthome.identity.model.DeviceOwnership;
import io.smarthome.identity.model.House;
import io.smarthome.identity.repository.DeviceOwnershipRepository;
import io.smarthome.identity.repository.HouseRepository;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;
import java.util.UUID;

@RestController
@RequestMapping("/api/v1/houses")
public class HouseController {

    @Autowired
    private HouseRepository houseRepository;

    @Autowired
    private DeviceOwnershipRepository deviceOwnershipRepository;

    @Autowired
    private JdbcTemplate jdbcTemplate; // Для прямой записи в таблицу связей

    // 1. Получение всех домов (для проверки)
    @GetMapping
    public List<House> getAllHouses() {
        return houseRepository.findAll();
    }

    // 2. Создание дома (Admin)
    @PostMapping
    public House createHouse(@RequestBody House house) {
        return houseRepository.save(house);
    }

    // 3. Привязка пользователя к дому (Admin)
    // Соответствует стрелке в UML: POST /api/v1/houses/{id}/assign-user
    @PostMapping("/{houseId}/assign-user")
    public void assignUser(@PathVariable UUID houseId, @RequestBody Map<String, UUID> body) {
        UUID userId = body.get("user_id");
        String sql = "INSERT INTO user_houses (house_id, user_id) VALUES (?, ?) ON CONFLICT DO NOTHING";
        jdbcTemplate.update(sql, houseId, userId);
    }

    // 4. Привязка датчика к дому (User)
    @PostMapping("/{id}/devices")
    public DeviceOwnership linkDevice(@PathVariable UUID id, @RequestBody DeviceOwnership dev) {
        dev.setHouseId(id);
        return deviceOwnershipRepository.save(dev);
    }
}