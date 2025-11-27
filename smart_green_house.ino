#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>

// =======================
// KONFIGURASI WIFI & SERVER
// =======================
const char* ssid = "nanda";
const char* password = "";

const char* serverName = "http://192.168.43.126/smart_green_house"; 
const char* LOG_ENDPOINT = "/sensor/log/";
const char* CONTROL_ENDPOINT = "/sensor/getControlStatus.php";

// =======================
// KONFIGURASI SENSOR
// =======================
#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

#define SOIL_PIN 34
#define ADC_MAX_VALUE 4095

// =======================
// KONFIGURASI RELAY
// =======================
#define RELAY_KIPAS1 14
#define RELAY_KIPAS2 27
#define RELAY_POMPA  26

// =======================
// LCD I2C
// =======================
LiquidCrystal_I2C lcd(0x27, 16, 2);

// =======================
// SETUP
// =======================
void setup() {
    Serial.begin(115200);

    // LCD
    lcd.init();
    lcd.backlight();
    lcd.setCursor(0,0);
    lcd.print("Smart GreenHouse");
    delay(1500);
    lcd.clear();

    // Sensor
    dht.begin();

    // Relay
    pinMode(RELAY_KIPAS1, OUTPUT);
    pinMode(RELAY_KIPAS2, OUTPUT);
    pinMode(RELAY_POMPA, OUTPUT);

    digitalWrite(RELAY_KIPAS1, HIGH);
    digitalWrite(RELAY_KIPAS2, HIGH);
    digitalWrite(RELAY_POMPA, HIGH);

    // WiFi
    lcd.setCursor(0,0);
    lcd.print("WiFi Connecting");

    WiFi.begin(ssid, password);
    int dot = 0;

    while (WiFi.status() != WL_CONNECTED) {
        delay(400);
        lcd.setCursor(14 + dot, 1);
        lcd.print(".");
        dot++;
        if (dot > 2) {
            dot = 0;
            lcd.setCursor(14,1);
            lcd.print("   ");
        }
    }

    lcd.clear();
    lcd.print("WiFi Connected");
    Serial.println(WiFi.localIP());
    delay(1000);
}

// =======================
// GET STATUS KONTROL MANUAL
// =======================
void getControlStatus() {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(serverName) + CONTROL_ENDPOINT);

    int code = http.GET();
    if (code == HTTP_CODE_OK) {
        String response = http.getString();
        StaticJsonDocument<96> doc;

        if (!deserializeJson(doc, response)) {
            bool kipas1 = doc["kipas1"] | false;
            bool kipas2 = doc["kipas2"] | false;
            bool pompa  = doc["pompa"]  | false;

            digitalWrite(RELAY_KIPAS1, kipas1 ? LOW : HIGH);
            digitalWrite(RELAY_KIPAS2, kipas2 ? LOW : HIGH);
            digitalWrite(RELAY_POMPA,  pompa  ? LOW : HIGH);
        }
    }

    http.end();
}

// =======================
// KIRIM LOG SENSOR
// =======================
void sendSensorLog(float suhu, float kelembaban, int soil) {
    if (WiFi.status() != WL_CONNECTED) return;
    if (isnan(suhu) || isnan(kelembaban)) return;

    HTTPClient http;
    http.begin(String(serverName) + LOG_ENDPOINT);
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<128> doc;
    doc["suhu"] = suhu;
    doc["kelembaban"] = kelembaban;
    doc["soil"] = soil;

    String payload;
    serializeJson(doc, payload);

    http.POST(payload);
    http.end();
}

// =======================
// LOOP UTAMA
// =======================
void loop() {
    float suhu = dht.readTemperature();
    float kelembaban = dht.readHumidity();
    int soilValue = analogRead(SOIL_PIN);

    int soil = (ADC_MAX_VALUE - soilValue) * 100 / ADC_MAX_VALUE;
    soil = constrain(soil, 0, 100);

    // LCD Output
    lcd.clear();
    lcd.setCursor(0,0);
    lcd.print("T:");
    lcd.print(suhu, 1);
    lcd.print((char)223);
    lcd.print("C H:");
    lcd.print(kelembaban, 0);
    lcd.print("%");

    lcd.setCursor(0,1);
    lcd.print("Soil:");
    lcd.print(soil);
    lcd.print("%");

    // Serial Output
    Serial.println("==== SENSOR ====");
    Serial.print("Suhu      : "); Serial.println(suhu);
    Serial.print("Kelembaban: "); Serial.println(kelembaban);
    Serial.print("Soil      : "); Serial.println(soil);

    // Kirim ke server
    sendSensorLog(suhu, kelembaban, soil);

    // Ambil kontrol dari server
    getControlStatus();

    delay(5000);
}
