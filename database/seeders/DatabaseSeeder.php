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
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[$sup['nombre']] = Supplier::updateOrCreate(
                ['nombre' => $sup['nombre']],
                ['correo' => $sup['correo'], 'telefono' => $sup['telefono']]
            );
        }

        $productsData = [
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
                'nombre' => 'Samsung Oled',
                'sku' => 'TV-00001-01',
                'precio' => 4399.90,
                'stock' => 98,
                'stock_minimo' => 10,
                'categoria' => 'Televisores',
                'proveedor' => 'Ripley',
            ],
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
                'nombre' => 'Toshiba Satellite',
                'sku' => 'COM-00003-03',
                'precio' => 6499.00,
                'stock' => 463,
                'stock_minimo' => 20,
                'categoria' => 'Computadoras',
                'proveedor' => 'Ripley',
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

        if (StockMovement::count() === 0) {
            StockMovement::create([
                'product_id' => $products['CON-00001-01']->id,
                'tipo' => 'salida',
                'cantidad' => 10,
                'stock_anterior' => 160,
                'stock_posterior' => 150,
                'motivo' => 'Despacho de pedido mayorista a sucursal central',
                'fecha' => '2026-10-28 23:47:00',
            ]);

            StockMovement::create([
                'product_id' => $products['COM-00003-03']->id,
                'tipo' => 'entrada',
                'cantidad' => 63,
                'stock_anterior' => 400,
                'stock_posterior' => 463,
                'motivo' => 'Reposición de lote trimestral Ripley',
                'fecha' => '2026-10-28 18:15:00',
            ]);

            StockMovement::create([
                'product_id' => $products['CON-00002-02']->id,
                'tipo' => 'salida',
                'cantidad' => 5,
                'stock_anterior' => 5,
                'stock_posterior' => 0,
                'motivo' => 'Liquidación de unidades en vitrina principal',
                'fecha' => '2026-10-27 16:30:00',
            ]);

            StockMovement::create([
                'product_id' => $products['COM-00001-01']->id,
                'tipo' => 'salida',
                'cantidad' => 2,
                'stock_anterior' => 5,
                'stock_posterior' => 3,
                'motivo' => 'Asignación a laboratorio de pruebas técnicas',
                'fecha' => '2026-10-26 11:20:00',
            ]);
        }
    }
}
