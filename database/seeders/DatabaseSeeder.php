<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrador',
                'email' => 'admin@techstock.local',
                'age' => null,
                'password' => Hash::make('1234'),
            ]
        );

        $categoriesData = [
            ['nombre' => 'Computadoras', 'descripcion' => 'Equipos portátiles, laptops de alto rendimiento y estaciones de trabajo.'],
            ['nombre' => 'Consolas', 'descripcion' => 'Consolas de videojuegos domésticas, híbridas y ediciones estándar.'],
            ['nombre' => 'Periféricos', 'descripcion' => 'Teclados mecánicos, ratones de precisión, monitores y accesorios.'],
            ['nombre' => 'Televisores', 'descripcion' => 'Pantallas OLED, QLED y televisores inteligentes de alta definición.'],
            ['nombre' => 'Componentes PC', 'descripcion' => 'Procesadores, tarjetas gráficas, memorias RAM, placas madre y unidades SSD NVMe.'],
            ['nombre' => 'Redes y Conectividad', 'descripcion' => 'Routers Wi-Fi 6, switches Gigabit, puntos de acceso empresariales y cableado.'],
            ['nombre' => 'Audio y Sonido', 'descripcion' => 'Audífonos inalámbricos, micrófonos de estudio, barras de sonido y altavoces.'],
            ['nombre' => 'Impresión y Oficina', 'descripcion' => 'Impresoras multifuncionales de sistema continuo, láser corporativas y suministros.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['nombre']] = Category::updateOrCreate(
                ['nombre' => $cat['nombre']],
                ['descripcion' => $cat['descripcion']]
            );
        }

        $suppliersData = [
            ['nombre' => 'Falabella', 'correo' => 'falabella@gmail.com', 'telefono' => '998887776'],
            ['nombre' => 'Ripley', 'correo' => 'ripley@gmail.com', 'telefono' => '988998882'],
            ['nombre' => 'Tottus', 'correo' => 'tottus@gmail.com', 'telefono' => '988882277'],
            ['nombre' => 'Deltron Perú', 'correo' => 'ventas@deltron.com.pe', 'telefono' => '945112233'],
            ['nombre' => 'Impacto Mayorista', 'correo' => 'comercial@impacto.com.pe', 'telefono' => '956223344'],
            ['nombre' => 'Memory Kings', 'correo' => 'corporativo@memorykings.pe', 'telefono' => '967334455'],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[$sup['nombre']] = Supplier::updateOrCreate(
                ['nombre' => $sup['nombre']],
                ['correo' => $sup['correo'], 'telefono' => $sup['telefono']]
            );
        }

        $productsData = [
            // Computadoras
            [
                'nombre' => 'Dell Ryzen7',
                'sku' => 'COM-00001-01',
                'precio' => 2729.99,
                'stock' => 3,
                'stock_minimo' => 5,
                'categoria' => 'Computadoras',
                'proveedor' => 'Falabella',
            ],
            [
                'nombre' => 'Lenovo Loq',
                'sku' => 'COM-00002-02',
                'precio' => 4500.00,
                'stock' => 50,
                'stock_minimo' => 5,
                'categoria' => 'Computadoras',
                'proveedor' => 'Ripley',
            ],
            [
                'nombre' => 'Toshiba Satellite',
                'sku' => 'COM-00003-03',
                'precio' => 6499.00,
                'stock' => 45,
                'stock_minimo' => 10,
                'categoria' => 'Computadoras',
                'proveedor' => 'Ripley',
            ],
            [
                'nombre' => 'ASUS ROG Strix G16 i7 RTX 4060',
                'sku' => 'COM-00004-04',
                'precio' => 5899.00,
                'stock' => 14,
                'stock_minimo' => 4,
                'categoria' => 'Computadoras',
                'proveedor' => 'Deltron Perú',
            ],
            [
                'nombre' => 'MacBook Air 15" Chip M3 16GB',
                'sku' => 'COM-00005-05',
                'precio' => 6299.00,
                'stock' => 8,
                'stock_minimo' => 4,
                'categoria' => 'Computadoras',
                'proveedor' => 'Memory Kings',
            ],
            [
                'nombre' => 'HP Victus 15 Ryzen 5 16GB SSD',
                'sku' => 'COM-00006-06',
                'precio' => 3199.00,
                'stock' => 22,
                'stock_minimo' => 6,
                'categoria' => 'Computadoras',
                'proveedor' => 'Falabella',
            ],

            // Consolas
            [
                'nombre' => 'Nintendo Switch',
                'sku' => 'CON-00001-01',
                'precio' => 3200.00,
                'stock' => 150,
                'stock_minimo' => 150,
                'categoria' => 'Consolas',
                'proveedor' => 'Tottus',
            ],
            [
                'nombre' => 'Play Station 5',
                'sku' => 'CON-00002-02',
                'precio' => 2500.00,
                'stock' => 0,
                'stock_minimo' => 5,
                'categoria' => 'Consolas',
                'proveedor' => 'Falabella',
            ],
            [
                'nombre' => 'Xbox Series X 1TB Carbon Black',
                'sku' => 'CON-00003-03',
                'precio' => 2699.00,
                'stock' => 12,
                'stock_minimo' => 5,
                'categoria' => 'Consolas',
                'proveedor' => 'Ripley',
            ],
            [
                'nombre' => 'Steam Deck OLED 512GB Valve',
                'sku' => 'CON-00004-04',
                'precio' => 2850.00,
                'stock' => 4,
                'stock_minimo' => 5,
                'categoria' => 'Consolas',
                'proveedor' => 'Memory Kings',
            ],

            // Periféricos
            [
                'nombre' => 'Teclado RGB Alienware',
                'sku' => 'PER-00001-01',
                'precio' => 250.00,
                'stock' => 129,
                'stock_minimo' => 15,
                'categoria' => 'Periféricos',
                'proveedor' => 'Tottus',
            ],
            [
                'nombre' => 'Mouse Logitech G Pro X Superlight 2',
                'sku' => 'PER-00002-02',
                'precio' => 589.00,
                'stock' => 34,
                'stock_minimo' => 10,
                'categoria' => 'Periféricos',
                'proveedor' => 'Memory Kings',
            ],
            [
                'nombre' => 'Monitor LG UltraGear 27" IPS 180Hz',
                'sku' => 'PER-00003-03',
                'precio' => 1099.00,
                'stock' => 19,
                'stock_minimo' => 6,
                'categoria' => 'Periféricos',
                'proveedor' => 'Impacto Mayorista',
            ],
            [
                'nombre' => 'Monitor Curvo Samsung Odyssey G5 32"',
                'sku' => 'PER-00004-04',
                'precio' => 1399.00,
                'stock' => 11,
                'stock_minimo' => 5,
                'categoria' => 'Periféricos',
                'proveedor' => 'Falabella',
            ],

            // Televisores
            [
                'nombre' => 'Samsung Oled',
                'sku' => 'TV-00001-01',
                'precio' => 4399.90,
                'stock' => 98,
                'stock_minimo' => 10,
                'categoria' => 'Televisores',
                'proveedor' => 'Ripley',
            ],
            [
                'nombre' => 'LG Smart TV OLED Evo C3 65" 4K',
                'sku' => 'TV-00002-02',
                'precio' => 5499.00,
                'stock' => 9,
                'stock_minimo' => 4,
                'categoria' => 'Televisores',
                'proveedor' => 'Falabella',
            ],
            [
                'nombre' => 'Sony Bravia XR 55" 4K Google TV',
                'sku' => 'TV-00003-03',
                'precio' => 3699.00,
                'stock' => 15,
                'stock_minimo' => 5,
                'categoria' => 'Televisores',
                'proveedor' => 'Tottus',
            ],

            // Componentes PC
            [
                'nombre' => 'Tarjeta Gráfica ASUS Dual RTX 4070 Super 12GB',
                'sku' => 'CMP-00001-01',
                'precio' => 2950.00,
                'stock' => 7,
                'stock_minimo' => 4,
                'categoria' => 'Componentes PC',
                'proveedor' => 'Impacto Mayorista',
            ],
            [
                'nombre' => 'Procesador AMD Ryzen 7 7800X3D AM5',
                'sku' => 'CMP-00002-02',
                'precio' => 1799.00,
                'stock' => 3,
                'stock_minimo' => 5,
                'categoria' => 'Componentes PC',
                'proveedor' => 'Deltron Perú',
            ],
            [
                'nombre' => 'SSD Kingston KC3000 1TB NVMe PCIe 4.0',
                'sku' => 'CMP-00003-03',
                'precio' => 389.00,
                'stock' => 64,
                'stock_minimo' => 15,
                'categoria' => 'Componentes PC',
                'proveedor' => 'Deltron Perú',
            ],
            [
                'nombre' => 'Kit Memoria RAM Corsair Vengeance DDR5 32GB',
                'sku' => 'CMP-00004-04',
                'precio' => 520.00,
                'stock' => 42,
                'stock_minimo' => 10,
                'categoria' => 'Componentes PC',
                'proveedor' => 'Impacto Mayorista',
            ],

            // Redes y Conectividad
            [
                'nombre' => 'Router TP-Link Archer AX55 Wi-Fi 6 Dual Band',
                'sku' => 'RED-00001-01',
                'precio' => 329.00,
                'stock' => 38,
                'stock_minimo' => 8,
                'categoria' => 'Redes y Conectividad',
                'proveedor' => 'Deltron Perú',
            ],
            [
                'nombre' => 'Access Point Ubiquiti UniFi U6 Pro PoE',
                'sku' => 'RED-00002-02',
                'precio' => 780.00,
                'stock' => 16,
                'stock_minimo' => 5,
                'categoria' => 'Redes y Conectividad',
                'proveedor' => 'Memory Kings',
            ],
            [
                'nombre' => 'Switch No Administrable Cisco Business 24 Puertos',
                'sku' => 'RED-00003-03',
                'precio' => 690.00,
                'stock' => 2,
                'stock_minimo' => 4,
                'categoria' => 'Redes y Conectividad',
                'proveedor' => 'Deltron Perú',
            ],

            // Audio y Sonido
            [
                'nombre' => 'Audífonos Sony WH-1000XM5 Noise Cancelling',
                'sku' => 'AUD-00001-01',
                'precio' => 1299.00,
                'stock' => 24,
                'stock_minimo' => 6,
                'categoria' => 'Audio y Sonido',
                'proveedor' => 'Ripley',
            ],
            [
                'nombre' => 'Micrófono HyperX QuadCast S RGB USB',
                'sku' => 'AUD-00002-02',
                'precio' => 599.00,
                'stock' => 18,
                'stock_minimo' => 5,
                'categoria' => 'Audio y Sonido',
                'proveedor' => 'Memory Kings',
            ],

            // Impresión y Oficina
            [
                'nombre' => 'Impresora Multifuncional Epson EcoTank L3250',
                'sku' => 'IMP-00001-01',
                'precio' => 799.00,
                'stock' => 27,
                'stock_minimo' => 8,
                'categoria' => 'Impresión y Oficina',
                'proveedor' => 'Tottus',
            ],
            [
                'nombre' => 'Impresora Láser HP LaserJet Pro 4003dw',
                'sku' => 'IMP-00002-02',
                'precio' => 1250.00,
                'stock' => 4,
                'stock_minimo' => 5,
                'categoria' => 'Impresión y Oficina',
                'proveedor' => 'Deltron Perú',
            ],
        ];

        $products = [];
        foreach ($productsData as $prod) {
            $products[$prod['sku']] = Product::updateOrCreate(
                ['sku' => $prod['sku']],
                [
                    'nombre' => $prod['nombre'],
                    'precio' => $prod['precio'],
                    'stock' => $prod['stock'],
                    'stock_minimo' => $prod['stock_minimo'],
                    'category_id' => $categories[$prod['categoria']]->id,
                    'supplier_id' => $suppliers[$prod['proveedor']]->id,
                ]
            );
        }

        if (StockMovement::count() <= 4) {
            StockMovement::truncate();

            $movementsData = [
                [
                    'sku' => 'CON-00001-01',
                    'tipo' => 'salida',
                    'cantidad' => 10,
                    'stock_anterior' => 160,
                    'stock_posterior' => 150,
                    'motivo' => 'Despacho de pedido mayorista a sucursal central',
                    'fecha' => '2026-10-28 23:47:00',
                ],
                [
                    'sku' => 'COM-00003-03',
                    'tipo' => 'entrada',
                    'cantidad' => 15,
                    'stock_anterior' => 30,
                    'stock_posterior' => 45,
                    'motivo' => 'Reposición de lote trimestral Ripley',
                    'fecha' => '2026-10-28 18:15:00',
                ],
                [
                    'sku' => 'CON-00002-02',
                    'tipo' => 'salida',
                    'cantidad' => 5,
                    'stock_anterior' => 5,
                    'stock_posterior' => 0,
                    'motivo' => 'Liquidación de unidades en vitrina principal',
                    'fecha' => '2026-10-27 16:30:00',
                ],
                [
                    'sku' => 'COM-00001-01',
                    'tipo' => 'salida',
                    'cantidad' => 2,
                    'stock_anterior' => 5,
                    'stock_posterior' => 3,
                    'motivo' => 'Asignación a laboratorio de pruebas técnicas',
                    'fecha' => '2026-10-26 11:20:00',
                ],
                [
                    'sku' => 'CMP-00003-03',
                    'tipo' => 'entrada',
                    'cantidad' => 40,
                    'stock_anterior' => 24,
                    'stock_posterior' => 64,
                    'motivo' => 'Ingreso de importación directa Deltron Perú',
                    'fecha' => '2026-10-29 09:30:00',
                ],
                [
                    'sku' => 'PER-00002-02',
                    'tipo' => 'salida',
                    'cantidad' => 6,
                    'stock_anterior' => 40,
                    'stock_posterior' => 34,
                    'motivo' => 'Venta corporativa para centro de diseño',
                    'fecha' => '2026-10-30 14:10:00',
                ],
                [
                    'sku' => 'CMP-00002-02',
                    'tipo' => 'salida',
                    'cantidad' => 4,
                    'stock_anterior' => 7,
                    'stock_posterior' => 3,
                    'motivo' => 'Ensamblaje de estaciones de trabajo gaming',
                    'fecha' => '2026-11-01 10:45:00',
                ],
                [
                    'sku' => 'IMP-00001-01',
                    'tipo' => 'entrada',
                    'cantidad' => 12,
                    'stock_anterior' => 15,
                    'stock_posterior' => 27,
                    'motivo' => 'Abastecimiento mensual de equipos de oficina',
                    'fecha' => '2026-11-02 16:00:00',
                ],
                [
                    'sku' => 'RED-00003-03',
                    'tipo' => 'salida',
                    'cantidad' => 3,
                    'stock_anterior' => 5,
                    'stock_posterior' => 2,
                    'motivo' => 'Instalación de gabinete de telecomunicaciones',
                    'fecha' => '2026-11-03 11:15:00',
                ],
                [
                    'sku' => 'COM-00004-04',
                    'tipo' => 'entrada',
                    'cantidad' => 6,
                    'stock_anterior' => 8,
                    'stock_posterior' => 14,
                    'motivo' => 'Recepción de orden de compra OC-2026-089',
                    'fecha' => '2026-11-04 08:50:00',
                ],
            ];

            foreach ($movementsData as $mov) {
                StockMovement::create([
                    'product_id' => $products[$mov['sku']]->id,
                    'tipo' => $mov['tipo'],
                    'cantidad' => $mov['cantidad'],
                    'stock_anterior' => $mov['stock_anterior'],
                    'stock_posterior' => $mov['stock_posterior'],
                    'motivo' => $mov['motivo'],
                    'fecha' => $mov['fecha'],
                ]);
            }
        }
    }
}
