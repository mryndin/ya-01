package io.smarthome.identity.repository;

import io.smarthome.identity.model.House;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;
import java.util.UUID;

@Repository
public interface HouseRepository extends JpaRepository<House, UUID> {
    // Базовых методов JpaRepository достаточно для CRUD операций с домами
}