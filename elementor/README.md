# Elementor Integration für Advanced Gift Card

## Übersicht

Diese Elementor-Integration ermöglicht es Ihnen, das Advanced Gift Card Plugin nativ in Elementor zu verwenden. Sie erhalten Zugriff auf drei leistungsstarke Widgets, die Sie per Drag & Drop in Ihre Seiten einfügen können.

## Voraussetzungen

- WordPress 6.5 oder höher
- WooCommerce 4.0 oder höher
- Elementor 3.0.0 oder höher
- PHP 7.4 oder höher
- Advanced Gift Card Plugin 1.7.1 oder höher

## Verfügbare Widgets

### 1. Gift Cards Grid Widget

Zeigt alle Geschenkkarten-Produkte in einem anpassbaren Grid-Layout an.

**Einstellungen:**
- **Spalten**: Wählen Sie zwischen 1-6 Spalten
- **Produkte pro Seite**: Bestimmen Sie, wie viele Produkte angezeigt werden
- **Sortierung**: Nach Datum, Titel, Preis, Beliebtheit, Bewertung oder zufällig
- **Reihenfolge**: Aufsteigend oder absteigend
- **Nicht vorrätige ausblenden**: Optional nicht vorrätige Produkte verstecken
- **Paginierung anzeigen**: Seitennavigation ein-/ausschalten

**Styling-Optionen:**
- Spaltenabstand
- Zeilenabstand

**Verwendung:**
1. Öffnen Sie den Elementor-Editor
2. Suchen Sie nach "Gift Cards Grid" in der Widget-Suche
3. Ziehen Sie das Widget auf Ihre Seite
4. Passen Sie die Einstellungen an

### 2. Single Gift Card Widget

Zeigt ein einzelnes Geschenkkarten-Produkt mit vollständigem Formular an.

**Einstellungen:**
- **Geschenkkarte auswählen**: Wählen Sie ein spezifisches Geschenkkarten-Produkt
- **Produktbild anzeigen**: Zeigt das Produktbild
- **Produkttitel anzeigen**: Zeigt den Produkttitel
- **Preis anzeigen**: Zeigt den Produktpreis
- **Beschreibung anzeigen**: Zeigt die Produktbeschreibung
- **Bildergalerie anzeigen**: Zeigt die Geschenkkarten-Bildergalerie

**Styling-Optionen:**
- Titelfarbe
- Titel-Typografie
- Preisfarbe

**Verwendung:**
1. Öffnen Sie den Elementor-Editor
2. Suchen Sie nach "Single Gift Card" in der Widget-Suche
3. Ziehen Sie das Widget auf Ihre Seite
4. Wählen Sie ein Geschenkkarten-Produkt aus
5. Passen Sie Anzeige- und Styling-Optionen an

### 3. Gift Card Gallery Widget

Zeigt Geschenkkarten-Bilder aus ausgewählten Galerie-Kategorien an.

**Einstellungen:**
- **Galerie-Kategorien auswählen**: Wählen Sie eine oder mehrere Kategorien
- **Spalten**: 2-6 Spalten
- **Bilder pro Kategorie**: Begrenzen Sie die Anzahl der angezeigten Bilder
- **Kategorietitel anzeigen**: Zeigt den Kategorienamen
- **Lightbox aktivieren**: Öffnet Bilder in einer Lightbox

**Styling-Optionen:**
- Spaltenabstand
- Zeilenabstand
- Kategorietitel-Farbe
- Kategorietitel-Typografie
- Bild-Rahmenradius
- Bild-Schatten

**Verwendung:**
1. Öffnen Sie den Elementor-Editor
2. Suchen Sie nach "Gift Card Gallery" in der Widget-Suche
3. Ziehen Sie das Widget auf Ihre Seite
4. Wählen Sie Galerie-Kategorien aus
5. Passen Sie Layout und Styling an

## Widget-Kategorie

Alle Widgets befinden sich in der **"Gift Cards"** Kategorie im Elementor Widget-Panel.

## Dateien

```
elementor/
├── class-afgc-elementor-integration.php  # Hauptintegrationsklasse
├── widgets/
│   ├── class-afgc-gift-cards-grid.php    # Grid Widget
│   ├── class-afgc-gift-card-single.php   # Single Gift Card Widget
│   └── class-afgc-gallery.php             # Gallery Widget
├── assets/
│   ├── css/
│   │   └── elementor-widgets.css          # Widget-Styles
│   └── js/
│       └── elementor-widgets.js           # Widget-Scripts
└── README.md                               # Diese Datei
```

## Technische Details

### Hooks & Filter

Die Integration nutzt folgende Elementor-Hooks:

- `elementor/loaded` - Prüft ob Elementor aktiv ist
- `elementor/elements/categories_registered` - Registriert Widget-Kategorie
- `elementor/widgets/register` - Registriert Widgets
- `elementor/frontend/after_enqueue_styles` - Lädt Widget-Styles
- `elementor/frontend/after_register_scripts` - Registriert Widget-Scripts

### Widget-Handler

JavaScript-Handler sind für jedes Widget verfügbar:

- `frontend/element_ready/afgc-gift-cards-grid.default`
- `frontend/element_ready/afgc-gift-card-single.default`
- `frontend/element_ready/afgc-gift-card-gallery.default`

## Kompatibilität

Die Elementor-Integration ist vollständig kompatibel mit:

- WooCommerce Product Loop Templates
- WooCommerce Blocks
- WPML (Mehrsprachigkeit)
- WordPress Multisite
- High-Performance Order Storage (HPOS)

## Fehlerbehebung

### Widgets werden nicht angezeigt

1. Stellen Sie sicher, dass Elementor mindestens Version 3.0.0 ist
2. Überprüfen Sie, ob das Advanced Gift Card Plugin aktiv ist
3. Leeren Sie den Elementor-Cache (Elementor > Tools > Regenerate Files)

### Styles werden nicht geladen

1. Leeren Sie Browser-Cache
2. Regenerieren Sie Elementor CSS-Dateien
3. Überprüfen Sie Datei-Berechtigungen für das assets-Verzeichnis

### JavaScript-Fehler

1. Öffnen Sie die Browser-Konsole (F12)
2. Suchen Sie nach Fehlermeldungen
3. Stellen Sie sicher, dass jQuery geladen ist

## Änderungsprotokoll

### Version 1.7.1
- Initiale Elementor-Integration
- Gift Cards Grid Widget hinzugefügt
- Single Gift Card Widget hinzugefügt
- Gift Card Gallery Widget hinzugefügt
- Responsive Layouts für alle Widgets
- Lightbox-Unterstützung für Gallery Widget

## Support

Für Support und Fragen besuchen Sie bitte:
- [Addify Support](https://woocommerce.com/vendor/addify/)
- Plugin-Dokumentation

## Entwickler-Hinweise

### Eigene Widgets erstellen

Sie können die vorhandenen Widgets als Vorlage verwenden:

```php
class Custom_AFGC_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom-afgc-widget';
    }

    public function get_categories() {
        return array( 'afgc-gift-cards' );
    }

    // Weitere Methoden...
}
```

### Widget-Styles anpassen

Styles können über Elementor-Selektoren angepasst werden:

```php
$this->add_control(
    'custom_color',
    array(
        'label'     => 'Farbe',
        'type'      => \Elementor\Controls_Manager::COLOR,
        'selectors' => array(
            '{{WRAPPER}} .element' => 'color: {{VALUE}};',
        ),
    )
);
```

## Lizenz

GNU General Public License v3.0
