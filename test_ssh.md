test ssh
#include <Arduino.h>
#include <Wire.h>
#include <Adafruit_MPU6050.h>
#include <Adafruit_Sensor.h>
#include <WiFi.h>
#include <WebServer.h>
#include <WebSocketsServer.h>
#include <LittleFS.h>

IPAddress local_IP(10,168,125,65);
IPAddress gateway(10,168,125,63);
IPAddress subnet(255, 255, 255, 0);
IPAddress primaryDNS(1,1,1,1);

const char* ssid = "JTI-POLINEMA";
const char* pass = "jtifast!";

Adafruit_MPU6050 mpu;
WebServer server(80);
WebSocketsServer webSocket(81);

constexpr uint8_t led1 = 25;
constexpr uint8_t led2 = 26;
constexpr uint8_t buzzer = 27;

float rollFiltered = 0;
float pitchFiltered = 0;

void setup() {
  Serial.begin(115200);

  if(!LittleFS.begin(true)) {
    Serial.println("littlefs mount failed");
    return;
  }

  WiFi.begin(ssid, pass);
  if(!WiFi.config(local_IP, gateway, subnet)) {
    Serial.println("static ip failed");
  }

  while(WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("your local ip : ");
  Serial.println(WiFi.localIP());
  
  webSocket.begin();
  
  pinMode(led1, OUTPUT);
  pinMode(led2, OUTPUT);
  pinMode(buzzer, OUTPUT);

  if (!mpu.begin()) {
      Serial.println("MPU6050 not found");
      while (true);
  }

  Serial.println("MPU6050 Ready");
  
  server.on("/", []() {
    File file = LittleFS.open("/index.html", "r");
    server.streamFile(file, "text/html");
    file.close();
  });

  server.on("/ijazah-jokowi.jpg", []() {
    File file = LittleFS.open("/ijazah-jokowi.jpg", "r");
    server.streamFile(file, "image/jpeg");
    file.close();
  });

  server.on("/test", []() {
    server.send(200, "text/plain", "ESP32 WEB OK");
  });
  
  server.begin();
}

void loop() {
  server.handleClient();
  webSocket.loop();
  sensors_event_t accel, gyro, temp;

  mpu.getEvent(&accel, &gyro, &temp);
  
  // roll = arctan(y/z)
  float roll =
    atan2(
      accel.acceleration.y, 
      accel.acceleration.z) 
      * 180.0 / PI;
  // pitch = arctan(-x / sqrt(y^2+z^2))
  float pitch =
    atan2(
      -accel.acceleration.x,
      sqrt(accel.acceleration.y * accel.acceleration.y +accel.acceleration.z * accel.acceleration.z)) 
      * 180.0 / PI;

  // Simple smoothing
  rollFiltered  = rollFiltered  * 0.9 + roll  * 0.1;
  pitchFiltered = pitchFiltered * 0.9 + pitch * 0.1;

  bool rollActive  = abs(rollFiltered) >= 50;
  bool pitchActive = abs(pitchFiltered) >= 50;

  digitalWrite(led1, rollActive);
  digitalWrite(led2, pitchActive);

  digitalWrite(buzzer, (rollActive || pitchActive));

  Serial.printf("%.2f,%.2f,%.2f,%.2f,%.2f\n",
    rollFiltered,
    pitchFiltered,
    accel.acceleration.x,
    accel.acceleration.y,
    accel.acceleration.z
  );

  static unsigned long lastSend = 0;
  if(millis() - lastSend > 20) {
    String json =
    "{"
    "\"roll\":" + String(rollFiltered,2) + ","
    "\"pitch\":" + String(pitchFiltered,2) + ","
    "\"x\":" + String(accel.acceleration.x,2) + ","
    "\"y\":" + String(accel.acceleration.y,2) + ","
    "\"z\":" + String(accel.acceleration.z,2) +
    "}";

    webSocket.broadcastTXT(json);
    lastSend = millis();
  }
  delay(20);
}
{
    "_readme": [
        "This file locks the dependencies of your project to a known state",
        "Read more about it at https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies",
        "This file is @generated automatically"
    ],
    "content-hash": "e1e667ee634826b7574a206de1a37084",
    "packages": [
        {
            "name": "brick/math",
            "version": "0.14.8",
            "source": {
                "type": "git",
                "url": "https://github.com/brick/math.git",
                "reference": "63422359a44b7f06cae63c3b429b59e8efcc0629"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/brick/math/zipball/63422359a44b7f06cae63c3b429b59e8efcc0629",
                "reference": "63422359a44b7f06cae63c3b429b59e8efcc0629",
                "shasum": ""
            },
            "require": {
                "php": "^8.2"
            },
            "require-dev": {
                "php-coveralls/php-coveralls": "^2.2",
                "phpstan/phpstan": "2.1.22",
                "phpunit/phpunit": "^11.5"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Brick\\Math\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "description": "Arbitrary-precision arithmetic library",
            "keywords": [
                "Arbitrary-precision",
                "BigInteger",
                "BigRational",
                "arithmetic",
                "bigdecimal",
                "bignum",
                "bignumber",
                "brick",
                "decimal",
                "integer",
                "math",
                "mathematics",
                "rational"
            ],
            "support": {
                "issues": "https://github.com/brick/math/issues",
                "source": "https://github.com/brick/math/tree/0.14.8"
            },
            "funding": [
                {
                    "url": "https://github.com/BenMorel",
                    "type": "github"
                }
            ],
            "time": "2026-02-10T14:33:43+00:00"
        },
        {
            "name": "carbonphp/carbon-doctrine-types",
            "version": "3.2.0",
            "source": {
                "type": "git",
                "url": "https://github.com/CarbonPHP/carbon-doctrine-types.git",
                "reference": "18ba5ddfec8976260ead6e866180bd5d2f71aa1d"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/CarbonPHP/carbon-doctrine-types/zipball/18ba5ddfec8976260ead6e866180bd5d2f71aa1d",
                "reference": "18ba5ddfec8976260ead6e866180bd5d2f71aa1d",
                "shasum": ""
            },
            "require": {
                "php": "^8.1"
            },
            "conflict": {
                "doctrine/dbal": "<4.0.0 || >=5.0.0"
            },
            "require-dev": {
                "doctrine/dbal": "^4.0.0",
                "nesbot/carbon": "^2.71.0 || ^3.0.0",
                "phpunit/phpunit": "^10.3"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Carbon\\Doctrine\\": "src/Carbon/Doctrine/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "KyleKatarn",
                    "email": "kylekatarnls@gmail.com"
                }
            ],
            "description": "Types to use Carbon in Doctrine",
            "keywords": [
                "carbon",
                "date",
                "datetime",
                "doctrine",
                "time"
            ],
            "support": {
                "issues": "https://github.com/CarbonPHP/carbon-doctrine-types/issues",
                "source": "https://github.com/CarbonPHP/carbon-doctrine-types/tree/3.2.0"
            },
            "funding": [
                {
                    "url": "https://github.com/kylekatarnls",
                    "type": "github"
                },
                {
                    "url": "https://opencollective.com/Carbon",
                    "type": "open_collective"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/nesbot/carbon",
                    "type": "tidelift"
                }
            ],
            "time": "2024-02-09T16:56:22+00:00"
        },
        {
            "name": "clue/redis-protocol",
            "version": "v0.3.2",
            "source": {
                "type": "git",
                "url": "https://github.com/clue/redis-protocol.git",
                "reference": "6f565332f5531b7722d1e9c445314b91862f6d6c"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/clue/redis-protocol/zipball/6f565332f5531b7722d1e9c445314b91862f6d6c",
                "reference": "6f565332f5531b7722d1e9c445314b91862f6d6c",
                "shasum": ""
            },
            "require": {
                "php": ">=5.3"
            },
            "require-dev": {
                "phpunit/phpunit": "^9.6 || ^5.7 || ^4.8.36"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Clue\\Redis\\Protocol\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Christian Lück",
                    "email": "christian@lueck.tv"
                }
            ],
            "description": "A streaming Redis protocol (RESP) parser and serializer written in pure PHP.",
            "homepage": "https://github.com/clue/redis-protocol",
            "keywords": [
                "parser",
                "protocol",
                "redis",
                "resp",
                "serializer",
                "streaming"
            ],
            "support": {
                "issues": "https://github.com/clue/redis-protocol/issues",
                "source": "https://github.com/clue/redis-protocol/tree/v0.3.2"
            },
            "funding": [
                {
                    "url": "https://clue.engineering/support",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/clue",
                    "type": "github"
                }
            ],
            "time": "2024-08-07T11:06:28+00:00"
        },
        {
            "name": "clue/redis-react",
            "version": "v2.8.0",
            "source": {
                "type": "git",
                "url": "https://github.com/clue/reactphp-redis.git",
                "reference": "84569198dfd5564977d2ae6a32de4beb5a24bdca"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/clue/reactphp-redis/zipball/84569198dfd5564977d2ae6a32de4beb5a24bdca",
                "reference": "84569198dfd5564977d2ae6a32de4beb5a24bdca",
                "shasum": ""
            },
            "require": {
                "clue/redis-protocol": "^0.3.2",
                "evenement/evenement": "^3.0 || ^2.0 || ^1.0",
                "php": ">=5.3",
                "react/event-loop": "^1.2",
                "react/promise": "^3.2 || ^2.0 || ^1.1",
                "react/promise-timer": "^1.11",
                "react/socket": "^1.16"
            },
            "require-dev": {
                "clue/block-react": "^1.5",
                "phpunit/phpunit": "^9.6 || ^5.7 || ^4.8.36"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Clue\\React\\Redis\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Christian Lück",
                    "email": "christian@clue.engineering"
                }
            ],
            "description": "Async Redis client implementation, built on top of ReactPHP.",
            "homepage": "https://github.com/clue/reactphp-redis",
            "keywords": [
                "async",
                "client",
                "database",
                "reactphp",
                "redis"
            ],
            "support": {
                "issues": "https://github.com/clue/reactphp-redis/issues",
                "source": "https://github.com/clue/reactphp-redis/tree/v2.8.0"
            },
            "funding": [
                {
                    "url": "https://clue.engineering/support",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/clue",
                    "type": "github"
                }
            ],
            "time": "2025-01-03T16:18:33+00:00"
        },
        {
            "name": "dflydev/dot-access-data",
            "version": "v3.0.3",
            "source": {
                "type": "git",
                "url": "https://github.com/dflydev/dflydev-dot-access-data.git",
                "reference": "a23a2bf4f31d3518f3ecb38660c95715dfead60f"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/dflydev/dflydev-dot-access-data/zipball/a23a2bf4f31d3518f3ecb38660c95715dfead60f",
                "reference": "a23a2bf4f31d3518f3ecb38660c95715dfead60f",
                "shasum": ""
            },
            "require": {
                "php": "^7.1 || ^8.0"
            },
            "require-dev": {
                "phpstan/phpstan": "^0.12.42",
                "phpunit/phpunit": "^7.5 || ^8.5 || ^9.3",
                "scrutinizer/ocular": "1.6.0",
                "squizlabs/php_codesniffer": "^3.5",
                "vimeo/psalm": "^4.0.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-main": "3.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Dflydev\\DotAccessData\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Dragonfly Development Inc.",
                    "email": "info@dflydev.com",
                    "homepage": "http://dflydev.com"
                },
                {
                    "name": "Beau Simensen",
                    "email": "beau@dflydev.com",
                    "homepage": "http://beausimensen.com"
                },
                {
                    "name": "Carlos Frutos",
                    "email": "carlos@kiwing.it",
                    "homepage": "https://github.com/cfrutos"
                },
                {
                    "name": "Colin O'Dell",
                    "email": "colinodell@gmail.com",
                    "homepage": "https://www.colinodell.com"
                }
            ],
            "description": "Given a deep data structure, access data by dot notation.",
            "homepage": "https://github.com/dflydev/dflydev-dot-access-data",
            "keywords": [
                "access",
                "data",
                "dot",
                "notation"
            ],
            "support": {
                "issues": "https://github.com/dflydev/dflydev-dot-access-data/issues",
                "source": "https://github.com/dflydev/dflydev-dot-access-data/tree/v3.0.3"
            },
            "time": "2024-07-08T12:26:09+00:00"
        },
        {
            "name": "doctrine/inflector",
            "version": "2.1.0",
            "source": {
                "type": "git",
                "url": "https://github.com/doctrine/inflector.git",
                "reference": "6d6c96277ea252fc1304627204c3d5e6e15faa3b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/doctrine/inflector/zipball/6d6c96277ea252fc1304627204c3d5e6e15faa3b",
                "reference": "6d6c96277ea252fc1304627204c3d5e6e15faa3b",
                "shasum": ""
            },
            "require": {
                "php": "^7.2 || ^8.0"
            },
            "require-dev": {
                "doctrine/coding-standard": "^12.0 || ^13.0",
                "phpstan/phpstan": "^1.12 || ^2.0",
                "phpstan/phpstan-phpunit": "^1.4 || ^2.0",
                "phpstan/phpstan-strict-rules": "^1.6 || ^2.0",
                "phpunit/phpunit": "^8.5 || ^12.2"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Doctrine\\Inflector\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Guilherme Blanco",
                    "email": "guilhermeblanco@gmail.com"
                },
                {
                    "name": "Roman Borschel",
                    "email": "roman@code-factory.org"
                },
                {
                    "name": "Benjamin Eberlei",
                    "email": "kontakt@beberlei.de"
                },
                {
                    "name": "Jonathan Wage",
                    "email": "jonwage@gmail.com"
                },
                {
                    "name": "Johannes Schmitt",
                    "email": "schmittjoh@gmail.com"
                }
            ],
            "description": "PHP Doctrine Inflector is a small library that can perform string manipulations with regard to upper/lowercase and singular/plural forms of words.",
            "homepage": "https://www.doctrine-project.org/projects/inflector.html",
            "keywords": [
                "inflection",
                "inflector",
                "lowercase",
                "manipulation",
                "php",
                "plural",
                "singular",
                "strings",
                "uppercase",
                "words"
            ],
            "support": {
                "issues": "https://github.com/doctrine/inflector/issues",
                "source": "https://github.com/doctrine/inflector/tree/2.1.0"
            },
            "funding": [
                {
                    "url": "https://www.doctrine-project.org/sponsorship.html",
                    "type": "custom"
                },
                {
                    "url": "https://www.patreon.com/phpdoctrine",
                    "type": "patreon"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/doctrine%2Finflector",
                    "type": "tidelift"
                }
            ],
            "time": "2025-08-10T19:31:58+00:00"
        },
        {
            "name": "doctrine/lexer",
            "version": "3.0.1",
            "source": {
                "type": "git",
                "url": "https://github.com/doctrine/lexer.git",
                "reference": "31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/doctrine/lexer/zipball/31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd",
                "reference": "31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd",
                "shasum": ""
            },
            "require": {
                "php": "^8.1"
            },
            "require-dev": {
                "doctrine/coding-standard": "^12",
                "phpstan/phpstan": "^1.10",
                "phpunit/phpunit": "^10.5",
                "psalm/plugin-phpunit": "^0.18.3",
                "vimeo/psalm": "^5.21"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Doctrine\\Common\\Lexer\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Guilherme Blanco",
                    "email": "guilhermeblanco@gmail.com"
                },
                {
                    "name": "Roman Borschel",
                    "email": "roman@code-factory.org"
                },
                {
                    "name": "Johannes Schmitt",
                    "email": "schmittjoh@gmail.com"
                }
            ],
            "description": "PHP Doctrine Lexer parser library that can be used in Top-Down, Recursive Descent Parsers.",
            "homepage": "https://www.doctrine-project.org/projects/lexer.html",
            "keywords": [
                "annotations",
                "docblock",
                "lexer",
                "parser",
                "php"
            ],
            "support": {
                "issues": "https://github.com/doctrine/lexer/issues",
                "source": "https://github.com/doctrine/lexer/tree/3.0.1"
            },
            "funding": [
                {
                    "url": "https://www.doctrine-project.org/sponsorship.html",
                    "type": "custom"
                },
                {
                    "url": "https://www.patreon.com/phpdoctrine",
                    "type": "patreon"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/doctrine%2Flexer",
                    "type": "tidelift"
                }
            ],
            "time": "2024-02-05T11:56:58+00:00"
        },
        {
            "name": "dragonmantank/cron-expression",
            "version": "v3.6.0",
            "source": {
                "type": "git",
                "url": "https://github.com/dragonmantank/cron-expression.git",
                "reference": "d61a8a9604ec1f8c3d150d09db6ce98b32675013"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/dragonmantank/cron-expression/zipball/d61a8a9604ec1f8c3d150d09db6ce98b32675013",
                "reference": "d61a8a9604ec1f8c3d150d09db6ce98b32675013",
                "shasum": ""
            },
            "require": {
                "php": "^8.2|^8.3|^8.4|^8.5"
            },
            "replace": {
                "mtdowling/cron-expression": "^1.0"
            },
            "require-dev": {
                "phpstan/extension-installer": "^1.4.3",
                "phpstan/phpstan": "^1.12.32|^2.1.31",
                "phpunit/phpunit": "^8.5.48|^9.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "3.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Cron\\": "src/Cron/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Chris Tankersley",
                    "email": "chris@ctankersley.com",
                    "homepage": "https://github.com/dragonmantank"
                }
            ],
            "description": "CRON for PHP: Calculate the next or previous run date and determine if a CRON expression is due",
            "keywords": [
                "cron",
                "schedule"
            ],
            "support": {
                "issues": "https://github.com/dragonmantank/cron-expression/issues",
                "source": "https://github.com/dragonmantank/cron-expression/tree/v3.6.0"
            },
            "funding": [
                {
                    "url": "https://github.com/dragonmantank",
                    "type": "github"
                }
            ],
            "time": "2025-10-31T18:51:33+00:00"
        },
        {
            "name": "egulias/email-validator",
            "version": "4.0.4",
            "source": {
                "type": "git",
                "url": "https://github.com/egulias/EmailValidator.git",
                "reference": "d42c8731f0624ad6bdc8d3e5e9a4524f68801cfa"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/egulias/EmailValidator/zipball/d42c8731f0624ad6bdc8d3e5e9a4524f68801cfa",
                "reference": "d42c8731f0624ad6bdc8d3e5e9a4524f68801cfa",
                "shasum": ""
            },
            "require": {
                "doctrine/lexer": "^2.0 || ^3.0",
                "php": ">=8.1",
                "symfony/polyfill-intl-idn": "^1.26"
            },
            "require-dev": {
                "phpunit/phpunit": "^10.2",
                "vimeo/psalm": "^5.12"
            },
            "suggest": {
                "ext-intl": "PHP Internationalization Libraries are required to use the SpoofChecking validation"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "4.0.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Egulias\\EmailValidator\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Eduardo Gulias Davis"
                }
            ],
            "description": "A library for validating emails against several RFCs",
            "homepage": "https://github.com/egulias/EmailValidator",
            "keywords": [
                "email",
                "emailvalidation",
                "emailvalidator",
                "validation",
                "validator"
            ],
            "support": {
                "issues": "https://github.com/egulias/EmailValidator/issues",
                "source": "https://github.com/egulias/EmailValidator/tree/4.0.4"
            },
            "funding": [
                {
                    "url": "https://github.com/egulias",
                    "type": "github"
                }
            ],
            "time": "2025-03-06T22:45:56+00:00"
        },
        {
            "name": "evenement/evenement",
            "version": "v3.0.2",
            "source": {
                "type": "git",
                "url": "https://github.com/igorw/evenement.git",
                "reference": "0a16b0d71ab13284339abb99d9d2bd813640efbc"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/igorw/evenement/zipball/0a16b0d71ab13284339abb99d9d2bd813640efbc",
                "reference": "0a16b0d71ab13284339abb99d9d2bd813640efbc",
                "shasum": ""
            },
            "require": {
                "php": ">=7.0"
            },
            "require-dev": {
                "phpunit/phpunit": "^9 || ^6"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "Evenement\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Igor Wiedler",
                    "email": "igor@wiedler.ch"
                }
            ],
            "description": "Événement is a very simple event dispatching library for PHP",
            "keywords": [
                "event-dispatcher",
                "event-emitter"
            ],
            "support": {
                "issues": "https://github.com/igorw/evenement/issues",
                "source": "https://github.com/igorw/evenement/tree/v3.0.2"
            },
            "time": "2023-08-08T05:53:35+00:00"
        },
        {
            "name": "fruitcake/php-cors",
            "version": "v1.4.0",
            "source": {
                "type": "git",
                "url": "https://github.com/fruitcake/php-cors.git",
                "reference": "38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/fruitcake/php-cors/zipball/38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379",
                "reference": "38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379",
                "shasum": ""
            },
            "require": {
                "php": "^8.1",
                "symfony/http-foundation": "^5.4|^6.4|^7.3|^8"
            },
            "require-dev": {
                "phpstan/phpstan": "^2",
                "phpunit/phpunit": "^9",
                "squizlabs/php_codesniffer": "^4"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "1.3-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Fruitcake\\Cors\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Fruitcake",
                    "homepage": "https://fruitcake.nl"
                },
                {
                    "name": "Barryvdh",
                    "email": "barryvdh@gmail.com"
                }
            ],
            "description": "Cross-origin resource sharing library for the Symfony HttpFoundation",
            "homepage": "https://github.com/fruitcake/php-cors",
            "keywords": [
                "cors",
                "laravel",
                "symfony"
            ],
            "support": {
                "issues": "https://github.com/fruitcake/php-cors/issues",
                "source": "https://github.com/fruitcake/php-cors/tree/v1.4.0"
            },
            "funding": [
                {
                    "url": "https://fruitcake.nl",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/barryvdh",
                    "type": "github"
                }
            ],
            "time": "2025-12-03T09:33:47+00:00"
        },
        {
            "name": "graham-campbell/result-type",
            "version": "v1.1.4",
            "source": {
                "type": "git",
                "url": "https://github.com/GrahamCampbell/Result-Type.git",
                "reference": "e01f4a821471308ba86aa202fed6698b6b695e3b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/GrahamCampbell/Result-Type/zipball/e01f4a821471308ba86aa202fed6698b6b695e3b",
                "reference": "e01f4a821471308ba86aa202fed6698b6b695e3b",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0",
                "phpoption/phpoption": "^1.9.5"
            },
            "require-dev": {
                "phpunit/phpunit": "^8.5.41 || ^9.6.22 || ^10.5.45 || ^11.5.7"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "GrahamCampbell\\ResultType\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                }
            ],
            "description": "An Implementation Of The Result Type",
            "keywords": [
                "Graham Campbell",
                "GrahamCampbell",
                "Result Type",
                "Result-Type",
                "result"
            ],
            "support": {
                "issues": "https://github.com/GrahamCampbell/Result-Type/issues",
                "source": "https://github.com/GrahamCampbell/Result-Type/tree/v1.1.4"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/graham-campbell/result-type",
                    "type": "tidelift"
                }
            ],
            "time": "2025-12-27T19:43:20+00:00"
        },
        {
            "name": "guzzlehttp/guzzle",
            "version": "7.10.1",
            "source": {
                "type": "git",
                "url": "https://github.com/guzzle/guzzle.git",
                "reference": "b777df1776c667e287664dda75b0298ad8ae3a14"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/guzzle/guzzle/zipball/b777df1776c667e287664dda75b0298ad8ae3a14",
                "reference": "b777df1776c667e287664dda75b0298ad8ae3a14",
                "shasum": ""
            },
            "require": {
                "ext-json": "*",
                "guzzlehttp/promises": "^2.3",
                "guzzlehttp/psr7": "^2.8",
                "php": "^7.2.5 || ^8.0",
                "psr/http-client": "^1.0",
                "symfony/deprecation-contracts": "^2.2 || ^3.0"
            },
            "provide": {
                "psr/http-client-implementation": "1.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "ext-curl": "*",
                "guzzle/client-integration-tests": "3.0.2",
                "guzzlehttp/test-server": "^0.3.2",
                "php-http/message-factory": "^1.1",
                "phpunit/phpunit": "^8.5.52 || ^9.6.34",
                "psr/log": "^1.1 || ^2.0 || ^3.0"
            },
            "suggest": {
                "ext-curl": "Required for CURL handler support",
                "ext-intl": "Required for Internationalized Domain Name (IDN) support",
                "psr/log": "Required for using the Log middleware"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": false
                }
            },
            "autoload": {
                "files": [
                    "src/functions_include.php"
                ],
                "psr-4": {
                    "GuzzleHttp\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "Jeremy Lindblom",
                    "email": "jeremeamia@gmail.com",
                    "homepage": "https://github.com/jeremeamia"
                },
                {
                    "name": "George Mponos",
                    "email": "gmponos@gmail.com",
                    "homepage": "https://github.com/gmponos"
                },
                {
                    "name": "Tobias Nyholm",
                    "email": "tobias.nyholm@gmail.com",
                    "homepage": "https://github.com/Nyholm"
                },
                {
                    "name": "Márk Sági-Kazár",
                    "email": "mark.sagikazar@gmail.com",
                    "homepage": "https://github.com/sagikazarmark"
                },
                {
                    "name": "Tobias Schultze",
                    "email": "webmaster@tubo-world.de",
                    "homepage": "https://github.com/Tobion"
                }
            ],
            "description": "Guzzle is a PHP HTTP client library",
            "keywords": [
                "client",
                "curl",
                "framework",
                "http",
                "http client",
                "psr-18",
                "psr-7",
                "rest",
                "web service"
            ],
            "support": {
                "issues": "https://github.com/guzzle/guzzle/issues",
                "source": "https://github.com/guzzle/guzzle/tree/7.10.1"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://github.com/Nyholm",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/guzzlehttp/guzzle",
                    "type": "tidelift"
                }
            ],
            "time": "2026-05-19T18:01:31+00:00"
        },
        {
            "name": "guzzlehttp/promises",
            "version": "2.3.1",
            "source": {
                "type": "git",
                "url": "https://github.com/guzzle/promises.git",
                "reference": "d2d8dfae4757f384d630fdffc2d8d6618d8f4c5e"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/guzzle/promises/zipball/d2d8dfae4757f384d630fdffc2d8d6618d8f4c5e",
                "reference": "d2d8dfae4757f384d630fdffc2d8d6618d8f4c5e",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "phpunit/phpunit": "^8.5.52 || ^9.6.34"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": false
                }
            },
            "autoload": {
                "psr-4": {
                    "GuzzleHttp\\Promise\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "Tobias Nyholm",
                    "email": "tobias.nyholm@gmail.com",
                    "homepage": "https://github.com/Nyholm"
                },
                {
                    "name": "Tobias Schultze",
                    "email": "webmaster@tubo-world.de",
                    "homepage": "https://github.com/Tobion"
                }
            ],
            "description": "Guzzle promises library",
            "keywords": [
                "promise"
            ],
            "support": {
                "issues": "https://github.com/guzzle/promises/issues",
                "source": "https://github.com/guzzle/promises/tree/2.3.1"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://github.com/Nyholm",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/guzzlehttp/promises",
                    "type": "tidelift"
                }
            ],
            "time": "2026-05-19T18:30:48+00:00"
        },
        {
            "name": "guzzlehttp/psr7",
            "version": "2.10.0",
            "source": {
                "type": "git",
                "url": "https://github.com/guzzle/psr7.git",
                "reference": "d5ddaf5743c42a61cb6100f83dc9d5a2bafe75ca"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/guzzle/psr7/zipball/d5ddaf5743c42a61cb6100f83dc9d5a2bafe75ca",
                "reference": "d5ddaf5743c42a61cb6100f83dc9d5a2bafe75ca",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0",
                "psr/http-factory": "^1.0",
                "psr/http-message": "^1.1 || ^2.0",
                "ralouphie/getallheaders": "^3.0"
            },
            "provide": {
                "psr/http-factory-implementation": "1.0",
                "psr/http-message-implementation": "1.0"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "http-interop/http-factory-tests": "1.1.0",
                "jshttp/mime-db": "1.54.0.1",
                "phpunit/phpunit": "^8.5.52 || ^9.6.34"
            },
            "suggest": {
                "laminas/laminas-httphandlerrunner": "Emit PSR-7 responses"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": false
                }
            },
            "autoload": {
                "psr-4": {
                    "GuzzleHttp\\Psr7\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "George Mponos",
                    "email": "gmponos@gmail.com",
                    "homepage": "https://github.com/gmponos"
                },
                {
                    "name": "Tobias Nyholm",
                    "email": "tobias.nyholm@gmail.com",
                    "homepage": "https://github.com/Nyholm"
                },
                {
                    "name": "Márk Sági-Kazár",
                    "email": "mark.sagikazar@gmail.com",
                    "homepage": "https://github.com/sagikazarmark"
                },
                {
                    "name": "Tobias Schultze",
                    "email": "webmaster@tubo-world.de",
                    "homepage": "https://github.com/Tobion"
                },
                {
                    "name": "Márk Sági-Kazár",
                    "email": "mark.sagikazar@gmail.com",
                    "homepage": "https://sagikazarmark.hu"
                }
            ],
            "description": "PSR-7 message implementation that also provides common utility methods",
            "keywords": [
                "http",
                "message",
                "psr-7",
                "request",
                "response",
                "stream",
                "uri",
                "url"
            ],
            "support": {
                "issues": "https://github.com/guzzle/psr7/issues",
                "source": "https://github.com/guzzle/psr7/tree/2.10.0"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://github.com/Nyholm",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/guzzlehttp/psr7",
                    "type": "tidelift"
                }
            ],
            "time": "2026-05-19T17:32:11+00:00"
        },
        {
            "name": "guzzlehttp/uri-template",
            "version": "v1.0.5",
            "source": {
                "type": "git",
                "url": "https://github.com/guzzle/uri-template.git",
                "reference": "4f4bbd4e7172148801e76e3decc1e559bdee34e1"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/guzzle/uri-template/zipball/4f4bbd4e7172148801e76e3decc1e559bdee34e1",
                "reference": "4f4bbd4e7172148801e76e3decc1e559bdee34e1",
                "shasum": ""
            },
            "require": {
                "php": "^7.2.5 || ^8.0",
                "symfony/polyfill-php80": "^1.24"
            },
            "require-dev": {
                "bamarni/composer-bin-plugin": "^1.8.2",
                "phpunit/phpunit": "^8.5.44 || ^9.6.25",
                "uri-template/tests": "1.0.0"
            },
            "type": "library",
            "extra": {
                "bamarni-bin": {
                    "bin-links": true,
                    "forward-command": false
                }
            },
            "autoload": {
                "psr-4": {
                    "GuzzleHttp\\UriTemplate\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Graham Campbell",
                    "email": "hello@gjcampbell.co.uk",
                    "homepage": "https://github.com/GrahamCampbell"
                },
                {
                    "name": "Michael Dowling",
                    "email": "mtdowling@gmail.com",
                    "homepage": "https://github.com/mtdowling"
                },
                {
                    "name": "George Mponos",
                    "email": "gmponos@gmail.com",
                    "homepage": "https://github.com/gmponos"
                },
                {
                    "name": "Tobias Nyholm",
                    "email": "tobias.nyholm@gmail.com",
                    "homepage": "https://github.com/Nyholm"
                }
            ],
            "description": "A polyfill class for uri_template of PHP",
            "keywords": [
                "guzzlehttp",
                "uri-template"
            ],
            "support": {
                "issues": "https://github.com/guzzle/uri-template/issues",
                "source": "https://github.com/guzzle/uri-template/tree/v1.0.5"
            },
            "funding": [
                {
                    "url": "https://github.com/GrahamCampbell",
                    "type": "github"
                },
                {
                    "url": "https://github.com/Nyholm",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/guzzlehttp/uri-template",
                    "type": "tidelift"
                }
            ],
            "time": "2025-08-22T14:27:06+00:00"
        },
        {
            "name": "laravel/framework",
            "version": "v13.11.1",
            "source": {
                "type": "git",
                "url": "https://github.com/laravel/framework.git",
                "reference": "6b70133ea3552afc37307ffb85b9efa48dc187d1"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/laravel/framework/zipball/6b70133ea3552afc37307ffb85b9efa48dc187d1",
                "reference": "6b70133ea3552afc37307ffb85b9efa48dc187d1",
                "shasum": ""
            },
            "require": {
                "brick/math": "^0.14.2 || ^0.15 || ^0.16 || ^0.17",
                "composer-runtime-api": "^2.2",
                "doctrine/inflector": "^2.0.5",
                "dragonmantank/cron-expression": "^3.4",
                "egulias/email-validator": "^4.0",
                "ext-ctype": "*",
                "ext-filter": "*",
                "ext-hash": "*",
                "ext-mbstring": "*",
                "ext-openssl": "*",
                "ext-session": "*",
                "ext-tokenizer": "*",
                "fruitcake/php-cors": "^1.3",
                "guzzlehttp/guzzle": "^7.8.2",
                "guzzlehttp/promises": "^2.0.3",
                "guzzlehttp/uri-template": "^1.0",
                "laravel/prompts": "^0.3.0",
                "laravel/serializable-closure": "^2.0.10",
                "league/commonmark": "^2.8.1",
                "league/flysystem": "^3.25.1",
                "league/flysystem-local": "^3.25.1",
                "league/uri": "^7.5.1",
                "monolog/monolog": "^3.0",
                "nesbot/carbon": "^3.8.4",
                "nunomaduro/termwind": "^2.0",
                "php": "^8.3",
                "psr/container": "^1.1.1 || ^2.0.1",
                "psr/log": "^1.0 || ^2.0 || ^3.0",
                "psr/simple-cache": "^1.0 || ^2.0 || ^3.0",
                "ramsey/uuid": "^4.7",
                "symfony/console": "^7.4.0 || ^8.0.0",
                "symfony/error-handler": "^7.4.0 || ^8.0.0",
                "symfony/finder": "^7.4.0 || ^8.0.0",
                "symfony/http-foundation": "^7.4.0 || ^8.0.0",
                "symfony/http-kernel": "^7.4.0 || ^8.0.0",
                "symfony/mailer": "^7.4.0 || ^8.0.0",
                "symfony/mime": "^7.4.0 || ^8.0.0",
                "symfony/polyfill-php84": "^1.36",
                "symfony/polyfill-php85": "^1.36",
                "symfony/polyfill-php86": "^1.36",
                "symfony/process": "^7.4.5 || ^8.0.5",
                "symfony/routing": "^7.4.0 || ^8.0.0",
                "symfony/uid": "^7.4.0 || ^8.0.0",
                "symfony/var-dumper": "^7.4.0 || ^8.0.0",
                "tijsverkoyen/css-to-inline-styles": "^2.2.5",
                "vlucas/phpdotenv": "^5.6.1",
                "voku/portable-ascii": "^2.0.2"
            },
            "conflict": {
                "tightenco/collect": "<5.5.33"
            },
            "provide": {
                "psr/container-implementation": "1.1 || 2.0",
                "psr/log-implementation": "1.0 || 2.0 || 3.0",
                "psr/simple-cache-implementation": "1.0 || 2.0 || 3.0"
            },
            "replace": {
                "illuminate/auth": "self.version",
                "illuminate/broadcasting": "self.version",
                "illuminate/bus": "self.version",
                "illuminate/cache": "self.version",
                "illuminate/collections": "self.version",
                "illuminate/concurrency": "self.version",
                "illuminate/conditionable": "self.version",
                "illuminate/config": "self.version",
                "illuminate/console": "self.version",
                "illuminate/container": "self.version",
                "illuminate/contracts": "self.version",
                "illuminate/cookie": "self.version",
                "illuminate/database": "self.version",
                "illuminate/encryption": "self.version",
                "illuminate/events": "self.version",
                "illuminate/filesystem": "self.version",
                "illuminate/hashing": "self.version",
                "illuminate/http": "self.version",
                "illuminate/json-schema": "self.version",
                "illuminate/log": "self.version",
                "illuminate/macroable": "self.version",
                "illuminate/mail": "self.version",
                "illuminate/notifications": "self.version",
                "illuminate/pagination": "self.version",
                "illuminate/pipeline": "self.version",
                "illuminate/process": "self.version",
                "illuminate/queue": "self.version",
                "illuminate/redis": "self.version",
                "illuminate/reflection": "self.version",
                "illuminate/routing": "self.version",
                "illuminate/session": "self.version",
                "illuminate/support": "self.version",
                "illuminate/testing": "self.version",
                "illuminate/translation": "self.version",
                "illuminate/validation": "self.version",
                "illuminate/view": "self.version",
                "spatie/once": "*"
            },
            "require-dev": {
                "ably/ably-php": "^1.0",
                "aws/aws-sdk-php": "^3.322.9",
                "ext-gmp": "*",
                "fakerphp/faker": "^1.24",
                "guzzlehttp/psr7": "^2.9",
                "laravel/pint": "^1.18",
                "league/flysystem-aws-s3-v3": "^3.25.1",
                "league/flysystem-ftp": "^3.25.1",
                "league/flysystem-path-prefixing": "^3.25.1",
                "league/flysystem-read-only": "^3.25.1",
                "league/flysystem-sftp-v3": "^3.25.1",
                "mockery/mockery": "^1.6.10",
                "opis/json-schema": "^2.4.1",
                "orchestra/testbench-core": "^11.0.0",
                "pda/pheanstalk": "^7.0.0 || ^8.0.0",
                "php-http/discovery": "^1.15",
                "phpstan/phpstan": "^2.0",
                "phpunit/phpunit": "^11.5.50 || ^12.5.8 || ^13.0.3",
                "predis/predis": "^2.3 || ^3.0",
                "rector/rector": "^2.3",
                "resend/resend-php": "^1.0",
                "symfony/cache": "^7.4.0 || ^8.0.0",
                "symfony/http-client": "^7.4.0 || ^8.0.0",
                "symfony/psr-http-message-bridge": "^7.4.0 || ^8.0.0",
                "symfony/translation": "^7.4.0 || ^8.0.0"
            },
            "suggest": {
                "ably/ably-php": "Required to use the Ably broadcast driver (^1.0).",
                "aws/aws-sdk-php": "Required to use the SQS queue driver, DynamoDb failed job storage, and SES mail driver (^3.322.9).",
                "brianium/paratest": "Required to run tests in parallel (^7.0 || ^8.0).",
                "ext-apcu": "Required to use the APC cache driver.",
                "ext-fileinfo": "Required to use the Filesystem class.",
                "ext-ftp": "Required to use the Flysystem FTP driver.",
                "ext-gd": "Required to use Illuminate\\Http\\Testing\\FileFactory::image().",
                "ext-memcached": "Required to use the memcache cache driver.",
                "ext-pcntl": "Required to use all features of the queue worker and console signal trapping.",
                "ext-pdo": "Required to use all database features.",
                "ext-posix": "Required to use all features of the queue worker.",
                "ext-redis": "Required to use the Redis cache and queue drivers (^4.0 || ^5.0 || ^6.0).",
                "fakerphp/faker": "Required to generate fake data using the fake() helper (^1.23).",
                "filp/whoops": "Required for friendly error pages in development (^2.14.3).",
                "laravel/tinker": "Required to use the tinker console command (^2.0).",
                "league/flysystem-aws-s3-v3": "Required to use the Flysystem S3 driver (^3.25.1).",
                "league/flysystem-ftp": "Required to use the Flysystem FTP driver (^3.25.1).",
                "league/flysystem-path-prefixing": "Required to use the scoped driver (^3.25.1).",
                "league/flysystem-read-only": "Required to use read-only disks (^3.25.1)",
                "league/flysystem-sftp-v3": "Required to use the Flysystem SFTP driver (^3.25.1).",
                "mockery/mockery": "Required to use mocking (^1.6).",
                "pda/pheanstalk": "Required to use the beanstalk queue driver (^7.0 || ^8.0).",
                "php-http/discovery": "Required to use PSR-7 bridging features (^1.15).",
                "phpunit/phpunit": "Required to use assertions and run tests (^11.5.50 || ^12.5.8 || ^13.0.3).",
                "predis/predis": "Required to use the predis connector (^2.3 || ^3.0).",
                "psr/http-message": "Required to allow Storage::put to accept a StreamInterface (^1.0).",
                "pusher/pusher-php-server": "Required to use the Pusher broadcast driver (^6.0 || ^7.0).",
                "resend/resend-php": "Required to enable support for the Resend mail transport (^0.10.0 || ^1.0).",
                "spatie/fork": "Required to use the 'fork' concurrency driver (^1.2).",
                "symfony/cache": "Required to PSR-6 cache bridge (^7.4 || ^8.0).",
                "symfony/filesystem": "Required to enable support for relative symbolic links (^7.4 || ^8.0).",
                "symfony/http-client": "Required to enable support for the Symfony API mail transports (^7.4 || ^8.0).",
                "symfony/mailgun-mailer": "Required to enable support for the Mailgun mail transport (^7.4 || ^8.0).",
                "symfony/postmark-mailer": "Required to enable support for the Postmark mail transport (^7.4 || ^8.0).",
                "symfony/psr-http-message-bridge": "Required to use PSR-7 bridging features (^7.4 || ^8.0)."
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "13.0.x-dev"
                }
            },
            "autoload": {
                "files": [
                    "src/Illuminate/Collections/functions.php",
                    "src/Illuminate/Collections/helpers.php",
                    "src/Illuminate/Events/functions.php",
                    "src/Illuminate/Filesystem/functions.php",
                    "src/Illuminate/Foundation/helpers.php",
                    "src/Illuminate/Log/functions.php",
                    "src/Illuminate/Reflection/helpers.php",
                    "src/Illuminate/Support/functions.php",
                    "src/Illuminate/Support/helpers.php"
                ],
                "psr-4": {
                    "Illuminate\\": "src/Illuminate/",
                    "Illuminate\\Support\\": [
                        "src/Illuminate/Macroable/",
                        "src/Illuminate/Collections/",
                        "src/Illuminate/Conditionable/",
                        "src/Illuminate/Reflection/"
                    ]
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Taylor Otwell",
                    "email": "taylor@laravel.com"
                }
            ],
            "description": "The Laravel Framework.",
            "homepage": "https://laravel.com",
            "keywords": [
                "framework",
                "laravel"
            ],
            "support": {
                "issues": "https://github.com/laravel/framework/issues",
                "source": "https://github.com/laravel/framework"
            },
            "time": "2026-05-19T20:24:39+00:00"
        },
        {
            "name": "laravel/prompts",
            "version": "v0.3.18",
            "source": {
                "type": "git",
                "url": "https://github.com/laravel/prompts.git",
                "reference": "a19af51bb144bf87f08397921fa619f85c7d4e72"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/laravel/prompts/zipball/a19af51bb144bf87f08397921fa619f85c7d4e72",
                "reference": "a19af51bb144bf87f08397921fa619f85c7d4e72",
                "shasum": ""
            },
            "require": {
                "composer-runtime-api": "^2.2",
                "ext-mbstring": "*",
                "php": "^8.1",
                "symfony/console": "^6.2|^7.0|^8.0"
            },
            "conflict": {
                "illuminate/console": ">=10.17.0 <10.25.0",
                "laravel/framework": ">=10.17.0 <10.25.0"
            },
            "require-dev": {
                "illuminate/collections": "^10.0|^11.0|^12.0|^13.0",
                "mockery/mockery": "^1.5",
                "pestphp/pest": "^2.3|^3.4|^4.0",
                "phpstan/phpstan": "^1.12.28",
                "phpstan/phpstan-mockery": "^1.1.3"
            },
            "suggest": {
                "ext-pcntl": "Required for the spinner to be animated."
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-main": "0.3.x-dev"
                }
            },
            "autoload": {
                "files": [
                    "src/helpers.php"
                ],
                "psr-4": {
                    "Laravel\\Prompts\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "description": "Add beautiful and user-friendly forms to your command-line applications.",
            "support": {
                "issues": "https://github.com/laravel/prompts/issues",
                "source": "https://github.com/laravel/prompts/tree/v0.3.18"
            },
            "time": "2026-05-19T00:47:18+00:00"
        },
        {
            "name": "laravel/reverb",
            "version": "v1.10.2",
            "source": {
                "type": "git",
                "url": "https://github.com/laravel/reverb.git",
                "reference": "43a5c0a99b1aaba33dc32f97fcf51f182dd8c8ac"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/laravel/reverb/zipball/43a5c0a99b1aaba33dc32f97fcf51f182dd8c8ac",
                "reference": "43a5c0a99b1aaba33dc32f97fcf51f182dd8c8ac",
                "shasum": ""
            },
            "require": {
                "clue/redis-react": "^2.6",
                "guzzlehttp/psr7": "^2.6",
                "illuminate/console": "^10.47|^11.0|^12.0|^13.0",
                "illuminate/contracts": "^10.47|^11.0|^12.0|^13.0",
                "illuminate/http": "^10.47|^11.0|^12.0|^13.0",
                "illuminate/support": "^10.47|^11.0|^12.0|^13.0",
                "laravel/prompts": "^0.1.15|^0.2.0|^0.3.0",
                "php": "^8.2",
                "pusher/pusher-php-server": "^7.2",
                "ratchet/rfc6455": "^0.4",
                "react/promise-timer": "^1.10",
                "react/socket": "^1.14",
                "symfony/console": "^6.0|^7.0|^8.0",
                "symfony/http-foundation": "^6.3|^7.0|^8.0"
            },
            "require-dev": {
                "orchestra/testbench": "^8.36|^9.15|^10.8|^11.0",
                "pestphp/pest": "^2.0|^3.0|^4.0",
                "phpstan/phpstan": "^1.10",
                "ratchet/pawl": "^0.4.1",
                "react/async": "^4.2",
                "react/http": "^1.9"
            },
            "type": "library",
            "extra": {
                "laravel": {
                    "providers": [
                        "Laravel\\Reverb\\ApplicationManagerServiceProvider",
                        "Laravel\\Reverb\\ReverbServiceProvider"
                    ]
                }
            },
            "autoload": {
                "psr-4": {
                    "Laravel\\Reverb\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Taylor Otwell",
                    "email": "taylor@laravel.com"
                },
                {
                    "name": "Joe Dixon",
                    "email": "joe@laravel.com"
                }
            ],
            "description": "Laravel Reverb provides a real-time WebSocket communication backend for Laravel applications.",
            "keywords": [
                "WebSockets",
                "laravel",
                "real-time",
                "websocket"
            ],
            "support": {
                "issues": "https://github.com/laravel/reverb/issues",
                "source": "https://github.com/laravel/reverb/tree/v1.10.2"
            },
            "time": "2026-05-10T15:47:52+00:00"
        },
        {
            "name": "laravel/serializable-closure",
            "version": "v2.0.13",
            "source": {
                "type": "git",
                "url": "https://github.com/laravel/serializable-closure.git",
                "reference": "b566ee0dd251f3c4078bed003a7ce015f5ea6dce"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/laravel/serializable-closure/zipball/b566ee0dd251f3c4078bed003a7ce015f5ea6dce",
                "reference": "b566ee0dd251f3c4078bed003a7ce015f5ea6dce",
                "shasum": ""
            },
            "require": {
                "php": "^8.1"
            },
            "require-dev": {
                "illuminate/support": "^10.0|^11.0|^12.0|^13.0",
                "nesbot/carbon": "^2.67|^3.0",
                "pestphp/pest": "^2.36|^3.0|^4.0",
                "phpstan/phpstan": "^2.0",
                "symfony/var-dumper": "^6.2.0|^7.0.0|^8.0.0"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "2.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Laravel\\SerializableClosure\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Taylor Otwell",
                    "email": "taylor@laravel.com"
                },
                {
                    "name": "Nuno Maduro",
                    "email": "nuno@laravel.com"
                }
            ],
            "description": "Laravel Serializable Closure provides an easy and secure way to serialize closures in PHP.",
            "keywords": [
                "closure",
                "laravel",
                "serializable"
            ],
            "support": {
                "issues": "https://github.com/laravel/serializable-closure/issues",
                "source": "https://github.com/laravel/serializable-closure"
            },
            "time": "2026-04-16T14:03:50+00:00"
        },
        {
            "name": "laravel/tinker",
            "version": "v3.0.2",
            "source": {
                "type": "git",
                "url": "https://github.com/laravel/tinker.git",
                "reference": "4faba77764bd33411735936acdf30446d058c78b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/laravel/tinker/zipball/4faba77764bd33411735936acdf30446d058c78b",
                "reference": "4faba77764bd33411735936acdf30446d058c78b",
                "shasum": ""
            },
            "require": {
                "illuminate/console": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
                "illuminate/contracts": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
                "illuminate/support": "^8.0|^9.0|^10.0|^11.0|^12.0|^13.0",
                "php": "^8.1",
                "psy/psysh": "^0.12.0",
                "symfony/var-dumper": "^5.4|^6.0|^7.0|^8.0"
            },
            "require-dev": {
                "mockery/mockery": "~1.3.3|^1.4.2",
                "phpstan/phpstan": "^1.10",
                "phpunit/phpunit": "^10.5|^11.5"
            },
            "suggest": {
                "illuminate/database": "The Illuminate Database package (^8.0|^9.0|^10.0|^11.0|^12.0|^13.0)."
            },
            "type": "library",
            "extra": {
                "laravel": {
                    "providers": [
                        "Laravel\\Tinker\\TinkerServiceProvider"
                    ]
                },
                "branch-alias": {
                    "dev-master": "3.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Laravel\\Tinker\\": "src/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Taylor Otwell",
                    "email": "taylor@laravel.com"
                }
            ],
            "description": "Powerful REPL for the Laravel framework.",
            "keywords": [
                "REPL",
                "Tinker",
                "laravel",
                "psysh"
            ],
            "support": {
                "issues": "https://github.com/laravel/tinker/issues",
                "source": "https://github.com/laravel/tinker/tree/v3.0.2"
            },
            "time": "2026-03-17T14:54:13+00:00"
        },
        {
            "name": "league/commonmark",
            "version": "2.8.2",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/commonmark.git",
                "reference": "59fb075d2101740c337c7216e3f32b36c204218b"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/commonmark/zipball/59fb075d2101740c337c7216e3f32b36c204218b",
                "reference": "59fb075d2101740c337c7216e3f32b36c204218b",
                "shasum": ""
            },
            "require": {
                "ext-mbstring": "*",
                "league/config": "^1.1.1",
                "php": "^7.4 || ^8.0",
                "psr/event-dispatcher": "^1.0",
                "symfony/deprecation-contracts": "^2.1 || ^3.0",
                "symfony/polyfill-php80": "^1.16"
            },
            "require-dev": {
                "cebe/markdown": "^1.0",
                "commonmark/cmark": "0.31.1",
                "commonmark/commonmark.js": "0.31.1",
                "composer/package-versions-deprecated": "^1.8",
                "embed/embed": "^4.4",
                "erusev/parsedown": "^1.0",
                "ext-json": "*",
                "github/gfm": "0.29.0",
                "michelf/php-markdown": "^1.4 || ^2.0",
                "nyholm/psr7": "^1.5",
                "phpstan/phpstan": "^1.8.2",
                "phpunit/phpunit": "^9.5.21 || ^10.5.9 || ^11.0.0",
                "scrutinizer/ocular": "^1.8.1",
                "symfony/finder": "^5.3 | ^6.0 | ^7.0 || ^8.0",
                "symfony/process": "^5.4 | ^6.0 | ^7.0 || ^8.0",
                "symfony/yaml": "^2.3 | ^3.0 | ^4.0 | ^5.0 | ^6.0 | ^7.0 || ^8.0",
                "unleashedtech/php-coding-standard": "^3.1.1",
                "vimeo/psalm": "^4.24.0 || ^5.0.0 || ^6.0.0"
            },
            "suggest": {
                "symfony/yaml": "v2.3+ required if using the Front Matter extension"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-main": "2.9-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "League\\CommonMark\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Colin O'Dell",
                    "email": "colinodell@gmail.com",
                    "homepage": "https://www.colinodell.com",
                    "role": "Lead Developer"
                }
            ],
            "description": "Highly-extensible PHP Markdown parser which fully supports the CommonMark spec and GitHub-Flavored Markdown (GFM)",
            "homepage": "https://commonmark.thephpleague.com",
            "keywords": [
                "commonmark",
                "flavored",
                "gfm",
                "github",
                "github-flavored",
                "markdown",
                "md",
                "parser"
            ],
            "support": {
                "docs": "https://commonmark.thephpleague.com/",
                "forum": "https://github.com/thephpleague/commonmark/discussions",
                "issues": "https://github.com/thephpleague/commonmark/issues",
                "rss": "https://github.com/thephpleague/commonmark/releases.atom",
                "source": "https://github.com/thephpleague/commonmark"
            },
            "funding": [
                {
                    "url": "https://www.colinodell.com/sponsor",
                    "type": "custom"
                },
                {
                    "url": "https://www.paypal.me/colinpodell/10.00",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/colinodell",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/league/commonmark",
                    "type": "tidelift"
                }
            ],
            "time": "2026-03-19T13:16:38+00:00"
        },
        {
            "name": "league/config",
            "version": "v1.2.0",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/config.git",
                "reference": "754b3604fb2984c71f4af4a9cbe7b57f346ec1f3"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/config/zipball/754b3604fb2984c71f4af4a9cbe7b57f346ec1f3",
                "reference": "754b3604fb2984c71f4af4a9cbe7b57f346ec1f3",
                "shasum": ""
            },
            "require": {
                "dflydev/dot-access-data": "^3.0.1",
                "nette/schema": "^1.2",
                "php": "^7.4 || ^8.0"
            },
            "require-dev": {
                "phpstan/phpstan": "^1.8.2",
                "phpunit/phpunit": "^9.5.5",
                "scrutinizer/ocular": "^1.8.1",
                "unleashedtech/php-coding-standard": "^3.1",
                "vimeo/psalm": "^4.7.3"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-main": "1.2-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "League\\Config\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "BSD-3-Clause"
            ],
            "authors": [
                {
                    "name": "Colin O'Dell",
                    "email": "colinodell@gmail.com",
                    "homepage": "https://www.colinodell.com",
                    "role": "Lead Developer"
                }
            ],
            "description": "Define configuration arrays with strict schemas and access values with dot notation",
            "homepage": "https://config.thephpleague.com",
            "keywords": [
                "array",
                "config",
                "configuration",
                "dot",
                "dot-access",
                "nested",
                "schema"
            ],
            "support": {
                "docs": "https://config.thephpleague.com/",
                "issues": "https://github.com/thephpleague/config/issues",
                "rss": "https://github.com/thephpleague/config/releases.atom",
                "source": "https://github.com/thephpleague/config"
            },
            "funding": [
                {
                    "url": "https://www.colinodell.com/sponsor",
                    "type": "custom"
                },
                {
                    "url": "https://www.paypal.me/colinpodell/10.00",
                    "type": "custom"
                },
                {
                    "url": "https://github.com/colinodell",
                    "type": "github"
                }
            ],
            "time": "2022-12-11T20:36:23+00:00"
        },
        {
            "name": "league/flysystem",
            "version": "3.34.0",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/flysystem.git",
                "reference": "2daaac3b0d4c83ea7ed5d8586e786f5d00f3540e"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/flysystem/zipball/2daaac3b0d4c83ea7ed5d8586e786f5d00f3540e",
                "reference": "2daaac3b0d4c83ea7ed5d8586e786f5d00f3540e",
                "shasum": ""
            },
            "require": {
                "league/flysystem-local": "^3.0.0",
                "league/mime-type-detection": "^1.0.0",
                "php": "^8.0.2"
            },
            "conflict": {
                "async-aws/core": "<1.19.0",
                "async-aws/s3": "<1.14.0",
                "aws/aws-sdk-php": "3.209.31 || 3.210.0",
                "guzzlehttp/guzzle": "<7.0",
                "guzzlehttp/ringphp": "<1.1.1",
                "phpseclib/phpseclib": "3.0.15",
                "symfony/http-client": "<5.2"
            },
            "require-dev": {
                "async-aws/s3": "^1.5 || ^2.0",
                "async-aws/simple-s3": "^1.1 || ^2.0",
                "aws/aws-sdk-php": "^3.295.10",
                "composer/semver": "^3.0",
                "ext-fileinfo": "*",
                "ext-ftp": "*",
                "ext-mongodb": "^1.3|^2",
                "ext-zip": "*",
                "friendsofphp/php-cs-fixer": "^3.5",
                "google/cloud-storage": "^1.23",
                "guzzlehttp/psr7": "^2.6",
                "microsoft/azure-storage-blob": "^1.1",
                "mongodb/mongodb": "^1.2|^2",
                "phpseclib/phpseclib": "^3.0.36",
                "phpstan/phpstan": "^1.10",
                "phpunit/phpunit": "^9.5.11|^10.0",
                "sabre/dav": "^4.6.0"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "League\\Flysystem\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Frank de Jonge",
                    "email": "info@frankdejonge.nl"
                }
            ],
            "description": "File storage abstraction for PHP",
            "keywords": [
                "WebDAV",
                "aws",
                "cloud",
                "file",
                "files",
                "filesystem",
                "filesystems",
                "ftp",
                "s3",
                "sftp",
                "storage"
            ],
            "support": {
                "issues": "https://github.com/thephpleague/flysystem/issues",
                "source": "https://github.com/thephpleague/flysystem/tree/3.34.0"
            },
            "time": "2026-05-14T10:28:08+00:00"
        },
        {
            "name": "league/flysystem-local",
            "version": "3.31.0",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/flysystem-local.git",
                "reference": "2f669db18a4c20c755c2bb7d3a7b0b2340488079"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/flysystem-local/zipball/2f669db18a4c20c755c2bb7d3a7b0b2340488079",
                "reference": "2f669db18a4c20c755c2bb7d3a7b0b2340488079",
                "shasum": ""
            },
            "require": {
                "ext-fileinfo": "*",
                "league/flysystem": "^3.0.0",
                "league/mime-type-detection": "^1.0.0",
                "php": "^8.0.2"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "League\\Flysystem\\Local\\": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Frank de Jonge",
                    "email": "info@frankdejonge.nl"
                }
            ],
            "description": "Local filesystem adapter for Flysystem.",
            "keywords": [
                "Flysystem",
                "file",
                "files",
                "filesystem",
                "local"
            ],
            "support": {
                "source": "https://github.com/thephpleague/flysystem-local/tree/3.31.0"
            },
            "time": "2026-01-23T15:30:45+00:00"
        },
        {
            "name": "league/mime-type-detection",
            "version": "1.16.0",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/mime-type-detection.git",
                "reference": "2d6702ff215bf922936ccc1ad31007edc76451b9"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/mime-type-detection/zipball/2d6702ff215bf922936ccc1ad31007edc76451b9",
                "reference": "2d6702ff215bf922936ccc1ad31007edc76451b9",
                "shasum": ""
            },
            "require": {
                "ext-fileinfo": "*",
                "php": "^7.4 || ^8.0"
            },
            "require-dev": {
                "friendsofphp/php-cs-fixer": "^3.2",
                "phpstan/phpstan": "^0.12.68",
                "phpunit/phpunit": "^8.5.8 || ^9.3 || ^10.0"
            },
            "type": "library",
            "autoload": {
                "psr-4": {
                    "League\\MimeTypeDetection\\": "src"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Frank de Jonge",
                    "email": "info@frankdejonge.nl"
                }
            ],
            "description": "Mime-type detection for Flysystem",
            "support": {
                "issues": "https://github.com/thephpleague/mime-type-detection/issues",
                "source": "https://github.com/thephpleague/mime-type-detection/tree/1.16.0"
            },
            "funding": [
                {
                    "url": "https://github.com/frankdejonge",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/league/flysystem",
                    "type": "tidelift"
                }
            ],
            "time": "2024-09-21T08:32:55+00:00"
        },
        {
            "name": "league/uri",
            "version": "7.8.1",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/uri.git",
                "reference": "08cf38e3924d4f56238125547b5720496fac8fd4"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/uri/zipball/08cf38e3924d4f56238125547b5720496fac8fd4",
                "reference": "08cf38e3924d4f56238125547b5720496fac8fd4",
                "shasum": ""
            },
            "require": {
                "league/uri-interfaces": "^7.8.1",
                "php": "^8.1",
                "psr/http-factory": "^1"
            },
            "conflict": {
                "league/uri-schemes": "^1.0"
            },
            "suggest": {
                "ext-bcmath": "to improve IPV4 host parsing",
                "ext-dom": "to convert the URI into an HTML anchor tag",
                "ext-fileinfo": "to create Data URI from file contennts",
                "ext-gmp": "to improve IPV4 host parsing",
                "ext-intl": "to handle IDN host with the best performance",
                "ext-uri": "to use the PHP native URI class",
                "jeremykendall/php-domain-parser": "to further parse the URI host and resolve its Public Suffix and Top Level Domain",
                "league/uri-components": "to provide additional tools to manipulate URI objects components",
                "league/uri-polyfill": "to backport the PHP URI extension for older versions of PHP",
                "php-64bit": "to improve IPV4 host parsing",
                "rowbot/url": "to handle URLs using the WHATWG URL Living Standard specification",
                "symfony/polyfill-intl-idn": "to handle IDN host via the Symfony polyfill if ext-intl is not present"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "7.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "League\\Uri\\": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Ignace Nyamagana Butera",
                    "email": "nyamsprod@gmail.com",
                    "homepage": "https://nyamsprod.com"
                }
            ],
            "description": "URI manipulation library",
            "homepage": "https://uri.thephpleague.com",
            "keywords": [
                "URN",
                "data-uri",
                "file-uri",
                "ftp",
                "hostname",
                "http",
                "https",
                "middleware",
                "parse_str",
                "parse_url",
                "psr-7",
                "query-string",
                "querystring",
                "rfc2141",
                "rfc3986",
                "rfc3987",
                "rfc6570",
                "rfc8141",
                "uri",
                "uri-template",
                "url",
                "ws"
            ],
            "support": {
                "docs": "https://uri.thephpleague.com",
                "forum": "https://thephpleague.slack.com",
                "issues": "https://github.com/thephpleague/uri-src/issues",
                "source": "https://github.com/thephpleague/uri/tree/7.8.1"
            },
            "funding": [
                {
                    "url": "https://github.com/sponsors/nyamsprod",
                    "type": "github"
                }
            ],
            "time": "2026-03-15T20:22:25+00:00"
        },
        {
            "name": "league/uri-interfaces",
            "version": "7.8.1",
            "source": {
                "type": "git",
                "url": "https://github.com/thephpleague/uri-interfaces.git",
                "reference": "85d5c77c5d6d3af6c54db4a78246364908f3c928"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/thephpleague/uri-interfaces/zipball/85d5c77c5d6d3af6c54db4a78246364908f3c928",
                "reference": "85d5c77c5d6d3af6c54db4a78246364908f3c928",
                "shasum": ""
            },
            "require": {
                "ext-filter": "*",
                "php": "^8.1",
                "psr/http-message": "^1.1 || ^2.0"
            },
            "suggest": {
                "ext-bcmath": "to improve IPV4 host parsing",
                "ext-gmp": "to improve IPV4 host parsing",
                "ext-intl": "to handle IDN host with the best performance",
                "php-64bit": "to improve IPV4 host parsing",
                "rowbot/url": "to handle URLs using the WHATWG URL Living Standard specification",
                "symfony/polyfill-intl-idn": "to handle IDN host via the Symfony polyfill if ext-intl is not present"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-master": "7.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "League\\Uri\\": ""
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Ignace Nyamagana Butera",
                    "email": "nyamsprod@gmail.com",
                    "homepage": "https://nyamsprod.com"
                }
            ],
            "description": "Common tools for parsing and resolving RFC3987/RFC3986 URI",
            "homepage": "https://uri.thephpleague.com",
            "keywords": [
                "data-uri",
                "file-uri",
                "ftp",
                "hostname",
                "http",
                "https",
                "parse_str",
                "parse_url",
                "psr-7",
                "query-string",
                "querystring",
                "rfc3986",
                "rfc3987",
                "rfc6570",
                "uri",
                "url",
                "ws"
            ],
            "support": {
                "docs": "https://uri.thephpleague.com",
                "forum": "https://thephpleague.slack.com",
                "issues": "https://github.com/thephpleague/uri-src/issues",
                "source": "https://github.com/thephpleague/uri-interfaces/tree/7.8.1"
            },
            "funding": [
                {
                    "url": "https://github.com/sponsors/nyamsprod",
                    "type": "github"
                }
            ],
            "time": "2026-03-08T20:05:35+00:00"
        },
        {
            "name": "monolog/monolog",
            "version": "3.10.0",
            "source": {
                "type": "git",
                "url": "https://github.com/Seldaek/monolog.git",
                "reference": "b321dd6749f0bf7189444158a3ce785cc16d69b0"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/Seldaek/monolog/zipball/b321dd6749f0bf7189444158a3ce785cc16d69b0",
                "reference": "b321dd6749f0bf7189444158a3ce785cc16d69b0",
                "shasum": ""
            },
            "require": {
                "php": ">=8.1",
                "psr/log": "^2.0 || ^3.0"
            },
            "provide": {
                "psr/log-implementation": "3.0.0"
            },
            "require-dev": {
                "aws/aws-sdk-php": "^3.0",
                "doctrine/couchdb": "~1.0@dev",
                "elasticsearch/elasticsearch": "^7 || ^8",
                "ext-json": "*",
                "graylog2/gelf-php": "^1.4.2 || ^2.0",
                "guzzlehttp/guzzle": "^7.4.5",
                "guzzlehttp/psr7": "^2.2",
                "mongodb/mongodb": "^1.8 || ^2.0",
                "php-amqplib/php-amqplib": "~2.4 || ^3",
                "php-console/php-console": "^3.1.8",
                "phpstan/phpstan": "^2",
                "phpstan/phpstan-deprecation-rules": "^2",
                "phpstan/phpstan-strict-rules": "^2",
                "phpunit/phpunit": "^10.5.17 || ^11.0.7",
                "predis/predis": "^1.1 || ^2",
                "rollbar/rollbar": "^4.0",
                "ruflin/elastica": "^7 || ^8",
                "symfony/mailer": "^5.4 || ^6",
                "symfony/mime": "^5.4 || ^6"
            },
            "suggest": {
                "aws/aws-sdk-php": "Allow sending log messages to AWS services like DynamoDB",
                "doctrine/couchdb": "Allow sending log messages to a CouchDB server",
                "elasticsearch/elasticsearch": "Allow sending log messages to an Elasticsearch server via official client",
                "ext-amqp": "Allow sending log messages to an AMQP server (1.0+ required)",
                "ext-curl": "Required to send log messages using the IFTTTHandler, the LogglyHandler, the SendGridHandler, the SlackWebhookHandler or the TelegramBotHandler",
                "ext-mbstring": "Allow to work properly with unicode symbols",
                "ext-mongodb": "Allow sending log messages to a MongoDB server (via driver)",
                "ext-openssl": "Required to send log messages using SSL",
                "ext-sockets": "Allow sending log messages to a Syslog server (via UDP driver)",
                "graylog2/gelf-php": "Allow sending log messages to a GrayLog2 server",
                "mongodb/mongodb": "Allow sending log messages to a MongoDB server (via library)",
                "php-amqplib/php-amqplib": "Allow sending log messages to an AMQP server using php-amqplib",
                "rollbar/rollbar": "Allow sending log messages to Rollbar",
                "ruflin/elastica": "Allow sending log messages to an Elastic Search server"
            },
            "type": "library",
            "extra": {
                "branch-alias": {
                    "dev-main": "3.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Monolog\\": "src/Monolog"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Jordi Boggiano",
                    "email": "j.boggiano@seld.be",
                    "homepage": "https://seld.be"
                }
            ],
            "description": "Sends your logs to files, sockets, inboxes, databases and various web services",
            "homepage": "https://github.com/Seldaek/monolog",
            "keywords": [
                "log",
                "logging",
                "psr-3"
            ],
            "support": {
                "issues": "https://github.com/Seldaek/monolog/issues",
                "source": "https://github.com/Seldaek/monolog/tree/3.10.0"
            },
            "funding": [
                {
                    "url": "https://github.com/Seldaek",
                    "type": "github"
                },
                {
                    "url": "https://tidelift.com/funding/github/packagist/monolog/monolog",
                    "type": "tidelift"
                }
            ],
            "time": "2026-01-02T08:56:05+00:00"
        },
        {
            "name": "nesbot/carbon",
            "version": "3.11.4",
            "source": {
                "type": "git",
                "url": "https://github.com/CarbonPHP/carbon.git",
                "reference": "e890471a3494740f7d9326d72ce6a8c559ffee60"
            },
            "dist": {
                "type": "zip",
                "url": "https://api.github.com/repos/CarbonPHP/carbon/zipball/e890471a3494740f7d9326d72ce6a8c559ffee60",
                "reference": "e890471a3494740f7d9326d72ce6a8c559ffee60",
                "shasum": ""
            },
            "require": {
                "carbonphp/carbon-doctrine-types": "<100.0",
                "ext-json": "*",
                "php": "^8.1",
                "psr/clock": "^1.0",
                "symfony/clock": "^6.3.12 || ^7.0 || ^8.0",
                "symfony/polyfill-mbstring": "^1.0",
                "symfony/translation": "^4.4.18 || ^5.2.1 || ^6.0 || ^7.0 || ^8.0"
            },
            "provide": {
                "psr/clock-implementation": "1.0"
            },
            "require-dev": {
                "doctrine/dbal": "^3.6.3 || ^4.0",
                "doctrine/orm": "^2.15.2 || ^3.0",
                "friendsofphp/php-cs-fixer": "^v3.87.1",
                "kylekatarnls/multi-tester": "^2.5.3",
                "phpmd/phpmd": "^2.15.0",
                "phpstan/extension-installer": "^1.4.3",
                "phpstan/phpstan": "^2.1.22",
                "phpunit/phpunit": "^10.5.53",
                "squizlabs/php_codesniffer": "^3.13.4 || ^4.0.0"
            },
            "bin": [
                "bin/carbon"
            ],
            "type": "library",
            "extra": {
                "laravel": {
                    "providers": [
                        "Carbon\\Laravel\\ServiceProvider"
                    ]
                },
                "phpstan": {
                    "includes": [
                        "extension.neon"
                    ]
                },
                "branch-alias": {
                    "dev-2.x": "2.x-dev",
                    "dev-master": "3.x-dev"
                }
            },
            "autoload": {
                "psr-4": {
                    "Carbon\\": "src/Carbon/"
                }
            },
            "notification-url": "https://packagist.org/downloads/",
            "license": [
                "MIT"
            ],
            "authors": [
                {
                    "name": "Brian Nesbitt",
                    "email": "brian@nesbot.com",
                    "homepage": "https://markido.com"
                },
                {
                    "name": "kylekatarnls",
                    "homepage": "https://github.com/kylekatarnls"
                }
            ],
            "description": "An API extension for DateTime that supports 281 different languages.",
            "homepage": "https://carbonphp.github.io/carbon/",
            "keywords": [
                "date",
                "datetime",
                "time"
            ],
            "support": {
                "docs": "https://carbonphp.github.io/carbon/guide/getting-started/introduction.html",
                "issues": "https://github.com/CarbonPHP/carbon/issues",
                "source": "https://github.com/CarbonPHP/carbon"
            },
            "funding": [
                {
                    "url": "https://github.com/sponsors/kylekatarnls",
                    "type": "github"
                },
                {
                    "url": "https://opencollective.com/Carbon#sponsor",
                    "type": "opencollective"
                },
                {
                    "url": "https://tidelift.com/subscription/pkg/packagist-nesbot-carbon?utm_source=packagist-nesbot-carbon&utm_medium=referral&utm_campaign=readme",
                    "type": "tidelift"
                }
            ],
            "time": "2026-04-07T09:57:54+00:00"
        },
