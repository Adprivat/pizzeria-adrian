# 🍕 Pizza Bestellsystem - Pizzeria Adriano
## Lernprojekt: HTML, CSS, PHP & CSV-Verarbeitung

Dieses Projekt ist eine **vollständige Webanwendung** zur Demonstration von:
- **Frontend-Entwicklung** mit HTML5 & CSS3
- **Backend-Entwicklung** mit PHP
- **Datenpersistierung** mit CSV-Dateien
- **Session-Management** für Warenkorb-Funktionalität
- **Responsive Design** für Mobile & Desktop

## 🎯 Lernziele & Technische Konzepte

### **HTML5 & Semantic Web**
- Strukturierte Formulargestaltung
- Barrierefreie Navigation
- Responsive Meta-Tags

### **CSS3 & Design**
- Grid & Flexbox Layout
- Mobile-First Design
- CSS-Variablen und Transitions

### **PHP-Programmierung**
- Session-Management (`$_SESSION`)
- Formular-Verarbeitung (`$_POST`)
- Dateioperationen (CSV lesen/schreiben)
- Sicherheit (`htmlspecialchars()`)

### **CSV-Datenverarbeitung**
- Strukturierte Datenspeicherung ohne Datenbank
- Relationale Datenverknüpfung über IDs
- Datei-I/O Operationen in PHP

## 🛠️ Anwendungsarchitektur

### **Frontend (Client-Side)**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   index.php     │───▶│   cart.php      │───▶│ confirmation.php│
│ (Produktauswahl)│    │ (Warenkorb &    │    │ (Bestätigung)   │
│                 │    │  Kundendaten)   │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   admin.php     │
                    │ (Verwaltung)    │
                    └─────────────────┘
```

### **Backend (Server-Side)**
- **PHP Sessions:** Warenkorb-Verwaltung zwischen Seitenaufrufen
- **CSV-Dateien:** Persistente Datenspeicherung ohne Datenbank
- **Form Processing:** Sichere Verarbeitung von Benutzereingaben

### **Datenmodell (CSV-basiert)**
```
orders.csv (Hauptbestellungen)
├── order_number (Primary Key)
├── customer_data (Name, E-Mail, Adresse...)
└── total_amount

order_items.csv (Bestellpositionen)
├── order_number (Foreign Key → orders.csv)
├── pizza_details (Name, Größe, Extras)
└── pricing_info (Grundpreis, Extras, Gesamtpreis)
```

## 🧩 Funktionale Komponenten

### **1. Produktkatalog (`index.php`)**
- **Dynamische Preisanzeige** pro Größe
- **Formular-Validierung** für Produktoptionen
- **Session-Integration** für Warenkorb-Management

### **2. Warenkorb-System (`cart.php`)**
- **Session-basierte Datenhaltung** (temporär)
- **CRUD-Operationen** (Hinzufügen/Entfernen von Artikeln)
- **Eingabevalidierung** für Kundendaten

### **3. Bestellabwicklung**
- **CSV-Schreiboperationen** für persistente Speicherung
- **Transaktionale Datenverarbeitung** (Orders + Items)
- **Automatische ID-Generierung** für Bestellnummern

### **4. Admin-Interface (`admin.php`)**
- **CSV-Leseoperationen** mit strukturierter Ausgabe
- **Datenverknüpfung** zwischen Orders und Items
- **Tabellarische Darstellung** von Geschäftsdaten

## 📚 Wichtige Code-Konzepte

### **PHP Session-Management**
```php
// Session initialisieren
session_start();

// Warenkorb in Session speichern
$_SESSION['cart'][] = $item_data;

// Session-Daten zwischen Seiten übertragen
$order = $_SESSION['last_order'];
```

### **CSV-Datenverarbeitung**
```php
// CSV schreiben
$fp = fopen('data/orders.csv', 'a');
fputcsv($fp, $order_data);
fclose($fp);

// CSV lesen
$handle = fopen('data/orders.csv', 'r');
while (($data = fgetcsv($handle)) !== FALSE) {
    $orders[] = array_combine($headers, $data);
}
```

### **Sichere Datenverarbeitung**
```php
// Input-Sanitizing
$name = trim($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// Output-Escaping
echo htmlspecialchars($customer_name);
```

## 🚀 Installation & Setup

### **Voraussetzungen**
- PHP 7.4+ (mit Standard-Extensions)
- Webserver (Apache/Nginx) oder PHP Built-in Server
- Schreibrechte für `data/` Verzeichnis

### **Lokale Entwicklungsumgebung**
```bash
# 1. In Projektverzeichnis wechseln
cd .\Pizza

# 2. PHP Built-in Server starten
php -S localhost:8000

# 3. Browser öffnen
http://localhost:8000
```

### **Produktionsumgebung**
1. Dateien auf Webserver hochladen
2. `data/` Verzeichnis erstellen (755 Berechtigung)
3. PHP-Fehlerbehandlung konfigurieren
4. HTTPS für Kundendaten aktivieren

## 📁 Projektstruktur & Dateierklärung

```
Pizza/
├── 📄 index.php          # Frontend: Produktkatalog & Bestellung
├── 📄 cart.php           # Backend: Warenkorb & Checkout-Prozess
├── 📄 confirmation.php   # Frontend: Bestellbestätigung
├── 📄 admin.php          # Backend: Datenauswertung & Verwaltung
├── 🎨 style.css          # Styling: Responsive Design & UX
├── 📖 README.md          # Dokumentation & Lernressource
├── 📁 data/              # Datenpersistierung
│   ├── orders.csv        # Hauptbestellungen (auto-generiert)
│   └── order_items.csv   # Bestellpositionen (auto-generiert)
└── 📁 img/               # Statische Assets
    ├── pizza-margherita-nach-italienischer-art.jpg
    ├── pizza-salami-mozzarella.jpg
    ├── pizza-hawai.jpg
    ├── pizza-tonno.jpg
    ├── pizza-capricciosa.jpg
    └── pizza-al-funghi-e-pancetta.jpg
```

## 🎓 Lernaktivitäten & Übungen

### **Anfänger-Level**
1. **HTML-Struktur analysieren:** Verstehen Sie die Formular-Elemente in `index.php`
2. **CSS-Grid erkunden:** Analysieren Sie das responsive Layout
3. **PHP-Basics:** Verfolgen Sie den Datenfluss von `$_POST` zu `$_SESSION`

### **Fortgeschritten-Level**
1. **Session-Debugging:** Implementieren Sie `var_dump($_SESSION)` für Debugging
2. **CSV-Manipulation:** Erweitern Sie die Datenstruktur um neue Felder
3. **Sicherheit verbessern:** Implementieren Sie CSRF-Schutz

### **Experten-Level**
1. **Datenbankintegration:** Ersetzen Sie CSV durch MySQL/SQLite
2. **API-Entwicklung:** Erstellen Sie REST-Endpoints für mobile Apps
3. **Testing:** Schreiben Sie Unit-Tests für die Business-Logik

## 💡 Erweiterungsideen für Produktivnutzung

### **Skalierung & Performance**
- **Datenbank-Migration:** MySQL/PostgreSQL für bessere Performance
- **Caching:** Redis/Memcached für Session-Storage
- **CDN-Integration:** Für statische Assets (Bilder)

### **Sicherheit & Compliance**
- **Benutzerauthentifizierung:** Login-System für Admin-Bereich
- **GDPR-Compliance:** Datenschutz-Features implementieren
- **Payment-Integration:** Stripe/PayPal für echte Zahlungen

### **Business Logic**
- **Inventarverwaltung:** Lagerbestand und Verfügbarkeit
- **Liefergebietsverwaltung:** PLZ-basierte Lieferkosten
- **Rabattsystem:** Gutscheine und Aktionen

### **Monitoring & Analytics**
- **Logging:** Strukturierte Logs für Debugging
- **Metriken:** Verkaufszahlen und beliebte Produkte
- **Error-Tracking:** Sentry/Bugsnag Integration

## ⚡ Quick Start Guide

```bash
# 1. Projekt klonen/herunterladen
git clone <repository-url>
cd Pizza

# 2. Server starten
php -S localhost:8000

# 3. Anwendung testen
# Browser: http://localhost:8000
# - Pizza auswählen und konfigurieren
# - Warenkorb füllen
# - Bestellung abschließen
# - Admin-Panel prüfen: http://localhost:8000/admin.php
```

## 🔧 Debugging & Troubleshooting

### **Häufige Probleme**
```php
// Session-Probleme debuggen
ini_set('display_errors', 1);
session_start();
var_dump($_SESSION); // Session-Inhalt anzeigen

// CSV-Schreibfehler prüfen
if (!is_writable('data/')) {
    echo "Fehler: data/ Verzeichnis nicht beschreibbar";
}

// PHP-Errors aktivieren
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
```

### **Performance-Monitoring**
```php
// Ausführungszeit messen
$start_time = microtime(true);
// ... Code ausführen ...
$end_time = microtime(true);
echo "Ausführungszeit: " . ($end_time - $start_time) . " Sekunden";
```

## 📈 Nächste Schritte nach dem Lernen
1. **Framework-Migration:** Laravel, Symfony, oder CodeIgniter
2. **Frontend-Frameworks:** React, Vue.js mit REST-API Backend
3. **Microservices:** Aufteilen in separate Services
4. **Cloud-Deployment:** AWS, Azure, oder Google Cloud
5. **DevOps-Pipeline:** Docker, CI/CD, automatische Tests

## 📚 Weiterführende Ressourcen

### **Dokumentation**
- [PHP Official Documentation](https://www.php.net/docs.php)
- [HTML5 Specification](https://html.spec.whatwg.org/)
- [CSS Grid Guide](https://css-tricks.com/snippets/css/complete-guide-grid/)

### **Best Practices**
- [OWASP Security Guidelines](https://owasp.org/www-project-top-ten/)
- [PSR PHP Standards](https://www.php-fig.org/psr/)
- [Web Content Accessibility Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

### **Tools & Testing**
- [PHPUnit](https://phpunit.de/) - Unit Testing Framework
- [Composer](https://getcomposer.org/) - Dependency Management
- [Xdebug](https://xdebug.org/) - Debugging & Profiling

---

> **🎯 Lernziel erreicht:** Sie haben erfolgreich eine vollständige Webanwendung mit HTML, CSS, PHP und CSV-Verarbeitung implementiert und verstehen die Grundlagen für professionelle Webentwicklung!

**Made with ❤️ for learning web development**
