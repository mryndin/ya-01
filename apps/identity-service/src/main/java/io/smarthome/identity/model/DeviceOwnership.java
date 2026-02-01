package io.smarthome.identity.model;

import jakarta.persistence.*;
import lombok.*;
import java.util.UUID;

@Data
@Entity
@Table(name = "device_ownership")
public class DeviceOwnership {
    @Id
    private Integer deviceId; // ID из таблицы sensors монолита
    
    private UUID houseId;
    private String authKey;
}