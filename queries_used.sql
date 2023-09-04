CREATE TABLE Localities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    locality_ref VARCHAR(255),
    locality_name VARCHAR(255),
    status INT(1) NULL
);

CREATE TABLE StopPoints (
    id INT PRIMARY KEY AUTO_INCREMENT,
    atco_code VARCHAR(255),
    common_name VARCHAR(255),
    longitude VARCHAR(255),
    latitude VARCHAR(255),
    stop_type VARCHAR(255),
    timing_status VARCHAR(255),
    notes VARCHAR(255),
    administrative_area_ref VARCHAR(255),
    locality_id INT,
    FOREIGN KEY (locality_id) REFERENCES Localities(id),
    status INT(1) NULL
);

CREATE TABLE RouteSections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    status INT(1) NULL
);

CREATE TABLE Routes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    description VARCHAR(255),
    route_section_id INT,
    FOREIGN KEY (route_section_id) REFERENCES RouteSections(id),
    status INT(1) NULL
);

CREATE TABLE RouteLinks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    route_section_id INT,
    from_stop_point_id INT,
    to_stop_point_id INT,
    distance INT(5),
    direction VARCHAR(255),
    status INT(1) NULL,
    FOREIGN KEY (route_section_id) REFERENCES RouteSections(id),
    FOREIGN KEY (from_stop_point_id) REFERENCES StopPoints(id),
    FOREIGN KEY (to_stop_point_id) REFERENCES StopPoints(id)
);

CREATE TABLE RouteLinksMapping (
    id INT PRIMARY KEY AUTO_INCREMENT,
    latitude VARCHAR(255),
    longitude VARCHAR(255),
    routelink_id INT,
    FOREIGN KEY (routelink_id) REFERENCES routelinks(id),
    status INT(1) NULL
);

CREATE TABLE JourneyPatternSection (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    status INT(1) NULL
);

CREATE TABLE JourneyPatternTimingLinks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    routelink_ref VARCHAR(255),
    runtime VARCHAR(255),
    journeypatternsection_id INT,
    routelink_id INT,
    FOREIGN KEY (journeypatternsection_id) REFERENCES journeypatternsection(id),
    FOREIGN KEY (routelink_id) REFERENCES RouteLinks(id),
    status INT(1) NULL
);

CREATE TABLE JourneyPatternTimingLinkSequence (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_sequence_number INT(5),
    to_sequence_number INT(5),
    from_activity VARCHAR(255),
    to_activity VARCHAR(255),
    from_dynamic_destination VARCHAR(255),
    to_dynamic_destination VARCHAR(255),
    from_stop_point_id INT,
    to_stop_point_id INT,
    from_timing_status VARCHAR(255),
    to_timing_status VARCHAR(255),
    journeypatterntiminglink_id INT,
    FOREIGN KEY (from_stop_point_id) REFERENCES StopPoints(id),
    FOREIGN KEY (to_stop_point_id) REFERENCES StopPoints(id),
    FOREIGN KEY (journeypatterntiminglink_id) REFERENCES journeypatterntiminglinks(id),
    status INT(1) NULL
);

CREATE TABLE Operators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    national_operator_code VARCHAR(255),
    operator_code VARCHAR(255),
    operator_short_name VARCHAR(255),
    operator_name_on_licence VARCHAR(255),
    trading_name VARCHAR(255),
    licence_number VARCHAR(255),
    licence_classification VARCHAR(255),
    address1 VARCHAR(255),
    address2 VARCHAR(255),
    address3 VARCHAR(255),
    address4 VARCHAR(255),
    status INT(1) NULL
);

CREATE TABLE Garages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    garage_code VARCHAR(255),
    garage_name VARCHAR(255),
    longitude VARCHAR(255),
    latitude VARCHAR(255),
    operator_id INT,
    FOREIGN KEY(operator_id) REFERENCES Operators(id),
    status INT(1) NULL
);

CREATE TABLE Services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_code VARCHAR(255),
    private_code VARCHAR(255),
    line_id VARCHAR(255),
    line_name VARCHAR(255),
    start_date date,
    end_date date,
    operator_id INT,
    mode VARCHAR(255),
    status INT(1),
    FOREIGN KEY (operator_id)  REFERENCES Operators(id)
);

CREATE TABLE StandardServices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    origin VARCHAR(255),
    destination VARCHAR(255),
    service_id INT,
    status INT(1),
    FOREIGN KEY (service_id) REFERENCES Services(id)
);

CREATE TABLE journeypatterns (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    destination_display VARCHAR(255),
    direction VARCHAR(255),
    standard_service_id INT,
    route_id INT,
    journey_pattern_section_id INT,
    FOREIGN KEY (route_id) REFERENCES Routes(id),
    FOREIGN KEY (journey_pattern_section_id ) REFERENCES JourneyPatternSection (id),
    FOREIGN KEY (standard_service_id) REFERENCES StandardServices(id),
    status INT(1) NULL

);

CREATE TABLE VehicleJourneys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    private_code VARCHAR(255),
    description VARCHAR(255),
    block_number VARCHAR(255),
    ticket_machine_service_code VARCHAR(255),
    journey_code VARCHAR(255),
    layover_point_duration VARCHAR(255),
    layover_point_name VARCHAR(255),
    layover_latitude VARCHAR(255),
    layover_longitude VARCHAR(255),
    vehicle_journey_code VARCHAR(255),
    garage_id INT,
    service_id INT,
    line_ref VARCHAR(255),
    journey_pattern_id INT,
    departure_time VARCHAR(10),
    FOREIGN KEY (garage_id) REFERENCES Garages(id),
    FOREIGN KEY (service_id) REFERENCES Services(id),
    FOREIGN KEY (journey_pattern_id) REFERENCES JourneyPatterns(id),
    status INT(1) NULL
);

