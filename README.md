# LinkTIC Test Theme – Prueba Técnica WordPress + Elementor

Tema hijo de **Hello Elementor** desarrollado como prueba técnica para LinkTIC. Replica la estructura y estilo de [linktic.com](https://linktic.com) usando Elementor como constructor visual.

## Características

- **Landing page** completa con 14 secciones (Hero, Soluciones, Clientes, Métricas, Certificaciones, Proyectos, Partners, Contacto, Footer)
- **Custom Post Type "Libros"** con metabox de autor y taxonomía "Géneros"
- **Formulario de contacto** con envío AJAX y almacenamiento en base de datos
- **Barra de accesibilidad** (zoom en 3 niveles + modo alto contraste) con persistencia en localStorage
- **Popup informativo** que aparece una vez por sesión
- **Template Elementor** exportable/importable (`elementor-templates/home-template.json`)
- **Diseño responsive** con breakpoints: 1024px, 768px, 390px

## Requisitos

- WordPress 6.x
- PHP 8.0+
- Tema padre: Hello Elementor
- Plugin: Elementor (gratuito)

## Instalación

1. Instalar y activar **Hello Elementor** como tema padre
2. Instalar y activar **Elementor**
3. Copiar esta carpeta en `wp-content/themes/linktic-test-theme/`
4. Activar el tema desde **Apariencia → Temas**
5. Al activar, se crean automáticamente:
   - Página "Inicio" (configurada como portada)
   - Libro de ejemplo "Cien Años de Soledad"
   - 5 géneros literarios
6. Importar el template Elementor en la página Inicio

## Estructura de archivos

```
linktic-test-theme/
├── style.css                          # Tema hijo (metadata)
├── functions.php                      # CPT, taxonomía, metabox, AJAX, accesibilidad
├── single-libros.php                  # Template detalle de libro
├── assets/
│   ├── css/custom.css                 # Estilos personalizados + responsive
│   └── js/
│       ├── accessibility.js           # Zoom, contraste, formulario AJAX
│       └── popup.js                   # Popup informativo
└── elementor-templates/
    └── home-template.json             # Template exportable de la landing
```

## Paleta de colores

| Color       | Hex       |
|-------------|-----------|
| Blue 100    | `#001D33` |
| Blue 70     | `#0094FF` |
| Blue 50     | `#01D9FF` |
| Blue 30     | `#B7E6FC` |
| Light       | `#DEF0FF` |
| Red         | `#E85846` |
| Yellow      | `#FFC046` |
| Green       | `#1CCE02` |

## Autor

Prueba técnica – LinkTIC 2026
