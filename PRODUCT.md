# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Administrador único del sistema de inventario (`admin`), encargado de controlar el catálogo de productos tecnológicos, gestionar el abastecimiento con proveedores, clasificar artículos por familias y auditar las entradas y salidas de existencias desde una interfaz web centralizada.

## Product Purpose

TechStock es un sistema web de gestión de inventario para productos tecnológicos que centraliza el registro de artículos, categorías, proveedores y movimientos de stock. Permite conocer las existencias reales en todo momento, detectar de inmediato los productos cuyo stock es igual o inferior al umbral mínimo configurado y generar reportes consolidados y exportables en CSV.

## Positioning

Un centro de control de inventario tecnológico con trazabilidad atómica por movimiento (registrando siempre stock anterior, cantidad operada y stock resultante) y monitoreo determinista de déficit de stock (`stock <= stock_minimo`) sincronizado en tiempo real entre el panel principal, el catálogo, el centro de alertas y los reportes.

## Operating Context

Operación administrativa diaria de almacén y catálogo tecnológico:
- Consulta rápida de indicadores globales (total de productos, categorías, proveedores, unidades físicas, valorización estimada en `S/` y conteo de alertas).
- Mantenimiento de catálogo con códigos SKU únicos, precios en soles (`S/`), existencias enteras y umbrales mínimos por producto.
- Importación masiva de catálogo vía archivos CSV con validación estricta por fila (encabezados, campos obligatorios, SKU duplicados, montos numéricos no negativos y referencias válidas) y exportación de reportes a CSV.
- Registro transaccional de entradas y salidas con bloqueo de salidas que excedan el stock disponible.

## Capabilities and Constraints

- **Stack tecnológico:** Laravel 12 (PHP 8.2+), Blade y Tailwind CSS v4, con migraciones estructuradas para MySQL.
- **Módulos del alcance completo (9 pantallas):**
  1. **Autenticación:** Inicio y cierre de sesión privado para el administrador (cuenta inicial de demostración `admin` / `1234`).
  2. **Panel principal (Dashboard):** Indicadores clave, distribución de productos por categoría, lista de productos que necesitan atención inmediata y movimientos recientes.
  3. **Productos y existencias:** Listado con búsqueda por nombre o SKU, filtro por categoría, filtro rápido de stock bajo, CRUD completo e importación de archivo CSV con reporte de filas procesadas y errores.
  4. **Categorías:** CRUD de categorías con descripción, conteo de productos asociados y protección de integridad referencial al eliminar.
  5. **Proveedores:** CRUD de proveedores con nombre, correo, teléfono, conteo de productos asociados y protección de integridad al eliminar.
  6. **Movimientos de stock:** Formulario de registro de Entrada/Salida con producto, cantidad entera positiva y motivo opcional; tabla de historial con tipo, cantidad, stock anterior, stock posterior, motivo y fecha.
  7. **Alertas (Stock bajo mínimo):** Listado focalizado de productos con `stock <= stock_minimo`, mostrando stock actual, mínimo configurado, déficit calculado y acceso directo para registrar entrada de reposición.
  8. **Reportes del inventario:** Analítica de inventario (total de productos, unidades totales, valor estimado en `S/`, productos en stock bajo), distribución por categoría, ranking de mayores existencias y exportación a CSV.
  9. **Mi perfil:** Actualización de información personal del administrador (nombre completo, usuario, correo, edad opcional) y cambio de contraseña validando la contraseña actual y confirmación.
- **Idioma:** Español en toda la interfaz, mensajes de validación y reportes.

## Evidence on Hand

- Documentación técnica de Análisis y Diseño de Sistemas (*TechStock – Sistema de Gestión de Inventario*), que especifica requerimientos funcionales (`RF-01` a `RF-16`), no funcionales (`RNF-01` a `RNF-10`), reglas de negocio (`RN-01` a `RN-10`), diccionario de datos preliminar y matriz de pruebas (`PT-01` a `PT-16`).
- Datos de demostración documentados en las capturas:
  - **Categorías:** Computadoras, Consolas, Periféricos, Televisores.
  - **Proveedores:** Falabella (`falabella@gmail.com`), Ripley (`ripley@gmail.com`), Tottus (`tottus@gmail.com`).
  - **Productos:** `Dell Ryzen7` (`COM-00001-01`, Computadoras, Falabella, `S/ 2,729.99`, stock `3`, mín `5`), `Lenovo Loq` (`COM-00002-02`, Computadoras, Ripley, `S/ 4,500.00`, stock `50`, mín `5`), `Nintendo Switch` (`CON-00001-01`, Consolas, Tottus, `S/ 3,200.00`, stock `150`, mín `150`), `Play Station 5` (`CON-00002-02`, Consolas, Falabella, `S/ 2,500.00`, stock `0`, mín `5`), `Samsung Oled` (`TV-00001-01`, Televisores, Ripley, `S/ 4,399.90`, stock `98`, mín `10`), `Teclado RGB Alienware` (`PER-00001-01`, Periféricos, Tottus, `S/ 250.00`, stock `129`, mín `15`), `Toshiba Satellite` (`COM-00003-03`, Computadoras, Ripley, `S/ 6,499.00`, stock `463`, mín `20`).

## Product Principles

1. **Integridad transaccional ante todo:** Ningún movimiento puede dejar existencias negativas ni desincronizar el stock actual respecto al historial de entradas y salidas.
2. **Criterio único de alerta:** Todo el sistema evalúa el umbral de atención con la misma regla (`stock <= stock_minimo`) y muestra el déficit exacto para accionar la reposición sin fricción.
3. **Claridad operativa e inmediata:** Cada pantalla prioriza la lectura rápida de códigos SKU, cantidades, estados de disponibilidad y acciones directas sin pasos ocultos.
4. **Validación transparente:** Tanto los formularios individuales como la importación CSV comunican con exactitud qué datos se procesaron y qué filas o campos requieren corrección.
