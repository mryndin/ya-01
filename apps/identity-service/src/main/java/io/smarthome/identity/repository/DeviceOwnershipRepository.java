package io.smarthome.identity.repository;

import io.smarthome.identity.model.DeviceOwnership;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface DeviceOwnershipRepository extends JpaRepository<DeviceOwnership, Integer> {
    // Используем Integer, так как ID датчика в монолите — это SERIAL (число)
}