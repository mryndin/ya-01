package io.smarthome.identity.model;

import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;
import java.util.UUID;

@Data
@NoArgsConstructor
@AllArgsConstructor
public class House {
    private UUID id;
    private String name;
    private String city;
    private String address;
}