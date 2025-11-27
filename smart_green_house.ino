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
const char* LOG_ENDPOINT = "/sensor/log/log.php";
const char* CONTROL_ENDPOINT = "/sensor/getControlStatus";

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

#define RELAY_ON  1
#define RELAY_OFF 0

// =======================
// LCD I2C
// =======================
LiquidCrystal_I2C lcd(0x27, 16, 2);

String modeSystem = "auto";  
bool kipasManual = false;
bool pompaManual = false;

float batasSuhu = 30;   // default auto
int   batasSoil = 40;   // default auto

// Status relay untuk dikirim log
bool currentKipasStatus = false;
bool currentPompaStatus = false;

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

    dht.begin();

    // Relay
    pinMode(RELAY_KIPAS1, OUTPUT);
    pinMode(RELAY_KIPAS2, OUTPUT);
    pinMode(RELAY_POMPA, OUTPUT);

    // Semua relay OFF (HIGH karena active LOW)
    digitalWrite(RELAY_KIPAS1, RELAY_OFF);
    digitalWrite(RELAY_KIPAS2, RELAY_OFF);
    digitalWrite(RELAY_POMPA, RELAY_OFF);

    // WiFi
    lcd.print("WiFi Connecting");
    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED) {
        delay(300);
    }

    lcd.clear();
    lcd.print("WiFi Connected");
    Serial.println(WiFi.localIP());
    delay(1000);
}

// =======================
// GET STATUS KONTROL
// =======================
void getControlStatus(float suhu, int soil) {
    if (WiFi.status() != WL_CONNECTED) return;

    HTTPClient http;
    http.begin(String(serverName) + CONTROL_ENDPOINT);

    int code = http.GET();
    if (code == HTTP_CODE_OK) {
        String response = http.getString();
        StaticJsonDocument<128> doc;

        if (deserializeJson(doc, response) == DeserializationError::Ok) {
            
            modeSystem = String((const char*)doc["mode"]);
            batasSuhu  = doc["batas_suhu"] | 30;
            batasSoil  = doc["batas_soil"] | 40;

            kipasManual = doc["kipas"] | 0;
            pompaManual = doc["pompa"] | 0;

            bool kipasON;
            bool pompaON;

            // =============================
            // MODE AUTO
            // =============================
            if (modeSystem == "auto") {
                kipasON = (suhu > batasSuhu);
                pompaON = (soil < batasSoil);

            // =============================
            // MODE MANUAL
            // =============================
            } else {
                kipasON = kipasManual;
                pompaON = pompaManual;
            }

            // Eksekusi relay
            digitalWrite(RELAY_KIPAS1, kipasON ? RELAY_ON : RELAY_OFF);
            digitalWrite(RELAY_KIPAS2, kipasON ? RELAY_ON : RELAY_OFF);
            digitalWrite(RELAY_POMPA,  pompaON ? RELAY_ON : RELAY_OFF);

            // Simpan untuk log
            currentKipasStatus = kipasON;
            currentPompaStatus = pompaON;
        }
    }

    http.end();
}

// =======================
// KIRIM LOG SENSOR (GET)
// =======================
void sendSensorLog(float suhu, float kelembaban, int soil) {
    if (WiFi.status() != WL_CONNECTED) return;
    if (isnan(suhu) || isnan(kelembaban)) return;

    HTTPClient http;

    String url = String(serverName) + LOG_ENDPOINT + 
                 "?suhu=" + suhu + 
                 "&kelembaban=" + kelembaban + 
                 "&soil=" + soil +
                 "&kipas_status=" + (currentKipasStatus ? 1 : 0) +
                 "&pompa_status=" + (currentPompaStatus ? 1 : 0);

    http.begin(url);
    http.GET();
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
    lcd.print(" H:");
    lcd.print(kelembaban, 0);

    lcd.setCursor(0,1);
    lcd.print("Soil:");
    lcd.print(soil);
    lcd.print("%");

    // Serial
    Serial.println("==== SENSOR ====");
    Serial.print("Suhu: "); Serial.println(suhu);
    Serial.print("Kelembaban: "); Serial.println(kelembaban);
    Serial.print("Soil: "); Serial.println(soil);

    // Kirim ke server
    sendSensorLog(suhu, kelembaban, soil);

    // Ambil kontrol dari server
    getControlStatus(suhu, soil);

    delay(5000);
}
