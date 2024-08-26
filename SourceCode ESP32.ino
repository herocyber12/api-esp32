#include <Arduino.h>
#include <PZEM004Tv30.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// ESP32 initialization
#define PZEM_RX_PIN 16
#define PZEM_TX_PIN 17
#define PZEM_SERIAL Serial2
#define RELAY_PIN 5

PZEM004Tv30 pzem(PZEM_SERIAL, PZEM_RX_PIN, PZEM_TX_PIN);

// WiFi credentials
const char* ssid = "a";
const char* password = "12345678";
const char* serverUrl = "https://00c0-182-1-112-125.ngrok-free.app/api";

void setup() {
    // Initialize serial for debugging
    Serial.begin(115200);

    // Initialize the relay pin as output
    pinMode(RELAY_PIN, OUTPUT);
    digitalWrite(RELAY_PIN, LOW);  // Ensure relay is off at start

    // Initialize WiFi
    WiFi.begin(ssid, password);
    Serial.print("Connecting to WiFi...");
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println(" connected");
    pzem.resetEnergy();

    HTTPClient http;
    if(WiFi.status() == WL_CONNECTED){
      // Check the relay control status from the server
        String url = String(serverUrl) + "/relay-control/latest";
        http.begin(url);
        int httpResponseCode = http.GET();

        if (httpResponseCode > 0) {
            String response = http.getString();

            StaticJsonDocument<200> doc;
            DeserializationError error = deserializeJson(doc, response);
            if (error) {
                Serial.print("Failed to parse JSON: ");
                Serial.println(error.f_str());
                return;
            }

            const char* state = doc["state"];

            // Check if the server wants to turn the lamp on or off
            if (strcmp(state, "on") == 0) {
                digitalWrite(RELAY_PIN, HIGH);  // Turn relay on
                Serial.println("Relay ON");
            } else if (strcmp(state, "off") == 0) {
                digitalWrite(RELAY_PIN, LOW);   // Turn relay off
                Serial.println("Relay OFF");
            }
        } else {
            Serial.print("Error code: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    }
    Serial.print("Custom Address: ");
    Serial.println(pzem.readAddress(), HEX);
}

void loop() {
    // Print the custom address of the PZEM
    
    HTTPClient http;
    Serial.print("Custom Address: ");
    Serial.println(pzem.readAddress(), HEX);

    // Read data from the sensor
    float voltage = pzem.voltage();
    float current = pzem.current();
    float power = pzem.power();
    float energy = pzem.energy();
    float frequency = pzem.frequency();
    float pf = pzem.pf();
    if(WiFi.status() == WL_CONNECTED){
      // Check the relay control status from the server
        String url = String(serverUrl) + "/relay-control/latest";
        http.begin(url);
        int httpResponseCode = http.GET();

        if (httpResponseCode > 0) {
            String response = http.getString();

            StaticJsonDocument<200> doc;
            DeserializationError error = deserializeJson(doc, response);
            if (error) {
                Serial.print("Failed to parse JSON: ");
                Serial.println(error.f_str());
                return;
            }

            const char* state = doc["state"];

            // Check if the server wants to turn the lamp on or off
            if (strcmp(state, "on") == 0) {
                digitalWrite(RELAY_PIN, HIGH);  // Turn relay on
                Serial.println("Relay ON");
            } else if (strcmp(state, "off") == 0) {
                digitalWrite(RELAY_PIN, LOW);   // Turn relay off
                Serial.println("Relay OFF");
            }
        } else {
            Serial.print("Error code: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    }
    // Check if the data is valid and print to the serial console
    if (isnan(voltage) || isnan(current) || isnan(power) || isnan(energy) || isnan(frequency) || isnan(pf)) {
        Serial.println("Error reading sensor data");
    } else {
        Serial.print("Voltage: ");    Serial.print(voltage);    Serial.println(" V");
        Serial.print("Current: ");    Serial.print(current);    Serial.println(" A");
        Serial.print("Power: ");      Serial.print(power);      Serial.println(" W");
        Serial.print("Energy: ");     Serial.print(energy, 3);  Serial.println(" kWh");
        Serial.print("Frequency: ");  Serial.print(frequency, 1); Serial.println(" Hz");
        Serial.print("PF: ");         Serial.println(pf);
    }

    // Send data to the server
    if (WiFi.status() == WL_CONNECTED) {
        
        String url = String(serverUrl) + "/sensor-data";
        http.begin(url);
        
        http.addHeader("Content-Type", "application/json");
        String payload = "{\"energy\":" + String(energy, 4) + ",\"voltage\":" + String(voltage, 2) + ",\"current\":" + String(current, 2) + ",\"power\":" + String(power, 2) + "}";
        int httpResponseCode = http.POST(payload);
        
        if (httpResponseCode > 0) {
            Serial.print("Success Code : ");
            Serial.println(httpResponseCode);
        } else {
          
            Serial.print("Error code: ");
            Serial.println(httpResponseCode);
        }
        http.end();
    } else {
        Serial.println("WiFi not connected");
    }

    Serial.println("");
    delay(2000);  // Wait 2 seconds before the next loop
}
