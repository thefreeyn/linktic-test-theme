/**
 * Google Apps Script — LinkTIC Contact Form to Google Sheets
 *
 * INSTRUCCIONES DE DESPLIEGUE:
 * 1. Abre Google Sheets y crea una hoja con las columnas:
 *    Fecha | Nombre | Correo | Teléfono | Empresa | Mensaje
 * 2. Ve a Extensiones > Apps Script
 * 3. Pega este código y guarda
 * 4. Haz clic en "Implementar" > "Nueva implementación"
 *    - Tipo: Aplicación web
 *    - Ejecutar como: Yo (tu cuenta)
 *    - Acceso: Cualquier usuario
 * 5. Copia la URL de implementación
 * 6. En functions.php reemplaza LINKTIC_SHEETS_WEBHOOK con esa URL
 */

const SHEET_NAME = "Contactos";

function doPost(e) {
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = ss.getSheetByName(SHEET_NAME) || ss.getActiveSheet();

    // Parse JSON body sent from WordPress
    var data = JSON.parse(e.postData.contents);

    // Append row: Fecha, Nombre, Correo, Teléfono, Empresa, Mensaje
    sheet.appendRow([
      data.fecha    || new Date().toISOString(),
      data.nombre   || "",
      data.correo   || "",
      data.telefono || "",
      data.empresa  || "",
      data.mensaje  || ""
    ]);

    return ContentService
      .createTextOutput(JSON.stringify({ success: true }))
      .setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    return ContentService
      .createTextOutput(JSON.stringify({ success: false, error: err.message }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Test function — ejecutar desde el editor para probar sin formulario
function testDoPost() {
  var mockEvent = {
    postData: {
      contents: JSON.stringify({
        nombre:   "Test Usuario",
        correo:   "test@linktic.com",
        telefono: "300 000 0000",
        empresa:  "LinkTIC",
        mensaje:  "Prueba de integración",
        fecha:    new Date().toISOString()
      })
    }
  };
  Logger.log(doPost(mockEvent).getContent());
}
