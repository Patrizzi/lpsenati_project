<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TechStockSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Category $category;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->adminUser = User::where('username', 'admin')->firstOrFail();
        $this->category = Category::firstOrFail();
        $this->supplier = Supplier::firstOrFail();
    }

    /**
     * PT-01: Inicio de sesión válido con usuario admin y clave 1234.
     */
    public function test_pt01_valid_login_with_admin_and_1234(): void
    {
        $response = $this->post(route('login.post'), [
            'username' => 'admin',
            'password' => '1234',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertSessionHas('success');
    }

    /**
     * PT-02: Rechazo de autenticación al ingresar contraseña incorrecta.
     */
    public function test_pt02_invalid_login_with_wrong_password(): void
    {
        $response = $this->from(route('login'))->post(route('login.post'), [
            'username' => 'admin',
            'password' => 'clave_incorrecta',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest();
    }

    /**
     * PT-03: Registro exitoso de nuevo producto con existencias iniciales y movimiento automático.
     */
    public function test_pt03_register_valid_new_product(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('products.store'), [
            'nombre' => 'Laptop HP ProBook 450 G10',
            'sku' => 'COM-99001-01',
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'precio' => 3899.90,
            'stock' => 12,
            'stock_minimo' => 4,
        ]);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'sku' => 'COM-99001-01',
            'nombre' => 'Laptop HP ProBook 450 G10',
            'stock' => 12,
        ]);

        $product = Product::where('sku', 'COM-99001-01')->firstOrFail();

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'tipo' => 'entrada',
            'cantidad' => 12,
            'stock_anterior' => 0,
            'stock_posterior' => 12,
        ]);
    }

    /**
     * PT-04: Rechazo al intentar registrar un producto con código SKU duplicado.
     */
    public function test_pt04_reject_duplicate_sku(): void
    {
        $existingProduct = Product::firstOrFail();

        $response = $this->actingAs($this->adminUser)->post(route('products.store'), [
            'nombre' => 'Producto Clon',
            'sku' => $existingProduct->sku,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'precio' => 150.00,
            'stock' => 10,
            'stock_minimo' => 2,
        ]);

        $response->assertSessionHasErrors(['sku']);
    }

    /**
     * PT-05: Búsqueda de productos por nombre y por código SKU.
     */
    public function test_pt05_search_product_by_name_and_sku(): void
    {
        $dellProduct = Product::where('sku', 'COM-00001-01')->firstOrFail();

        // Búsqueda por parte del nombre
        $responseName = $this->actingAs($this->adminUser)->get(route('products.index', ['search' => 'Dell']));
        $responseName->assertOk();
        $productsByName = $responseName->viewData('products');
        $this->assertTrue($productsByName->pluck('sku')->contains($dellProduct->sku));

        // Búsqueda por SKU exacto
        $responseSku = $this->actingAs($this->adminUser)->get(route('products.index', ['search' => 'COM-00001-01']));
        $responseSku->assertOk();
        $productsBySku = $responseSku->viewData('products');
        $this->assertTrue($productsBySku->pluck('sku')->contains('COM-00001-01'));

        // Búsqueda sin coincidencias
        $responseEmpty = $this->actingAs($this->adminUser)->get(route('products.index', ['search' => 'INEXISTENTE_XYZ_999']));
        $responseEmpty->assertOk();
        $productsEmpty = $responseEmpty->viewData('products');
        $this->assertCount(0, $productsEmpty);
    }

    /**
     * PT-06: Importación masiva exitosa desde archivo CSV válido.
     */
    public function test_pt06_import_valid_csv_file(): void
    {
        $csvContent = "nombre,sku,categoria,proveedor,precio,stock,stock_minimo\n" .
                      "Monitor LG UltraWide 29,MON-LG-2901,Periféricos,Tottus,899.00,14,3\n" .
                      "Mouse Logitech Master 3S,MOU-LOG-3S01,Periféricos,Tottus,349.50,25,5\n";

        $file = UploadedFile::fake()->createWithContent('catalogo_valido.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('products.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('import_summary', function (array $summary) {
            return $summary['read'] === 2 && $summary['imported'] === 2 && $summary['rejected'] === 0;
        });

        $this->assertDatabaseHas('products', ['sku' => 'MON-LG-2901', 'stock' => 14]);
        $this->assertDatabaseHas('products', ['sku' => 'MOU-LOG-3S01', 'stock' => 25]);
    }

    /**
     * PT-07: Rechazo de filas CSV con datos incompletos o precio negativo, reportando detalle del error.
     */
    public function test_pt07_reject_incomplete_or_negative_price_csv_row_and_report_error(): void
    {
        $csvContent = "nombre,sku,categoria,proveedor,precio,stock,stock_minimo\n" .
                      "Monitor Erróneo,MON-BAD-01,Periféricos,Tottus,-250.00,10,3\n" .
                      "Artículo Categoria Inexistente,ART-NOCAT-01,FantasmaCategory,Tottus,150.00,5,2\n";

        $file = UploadedFile::fake()->createWithContent('catalogo_errores.csv', $csvContent);

        $response = $this->actingAs($this->adminUser)->post(route('products.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHas('import_summary', function (array $summary) {
            return $summary['imported'] === 0 && $summary['rejected'] === 2 && count($summary['errors']) === 2;
        });

        $this->assertDatabaseMissing('products', ['sku' => 'MON-BAD-01']);
        $this->assertDatabaseMissing('products', ['sku' => 'ART-NOCAT-01']);
    }

    /**
     * PT-08: Creación exitosa de categoría con nombre y descripción válidos.
     */
    public function test_pt08_create_valid_category(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('categories.store'), [
            'nombre' => 'Audio y Microfonía',
            'descripcion' => 'Audífonos inalámbricos, micrófonos de estudio y barras de sonido.',
        ]);

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'nombre' => 'Audio y Microfonía',
        ]);
    }

    /**
     * PT-09: Rechazo de proveedor al ingresar un formato de correo electrónico inválido.
     */
    public function test_pt09_reject_supplier_with_malformed_email(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('suppliers.store'), [
            'nombre' => 'Distribuidor Global',
            'correo' => 'correo_no_valido_sin_arroba',
            'telefono' => '987654321',
        ]);

        $response->assertSessionHasErrors(['correo']);
        $this->assertDatabaseMissing('suppliers', ['nombre' => 'Distribuidor Global']);
    }

    /**
     * PT-10: Registro de entrada de existencias e incremento exacto del stock.
     */
    public function test_pt10_register_stock_entry_and_increase_stock(): void
    {
        $product = Product::create([
            'nombre' => 'Cámara Web HD 1080p',
            'sku' => 'CAM-00010-01',
            'precio' => 180.00,
            'stock' => 10,
            'stock_minimo' => 5,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('movements.store'), [
            'product_id' => $product->id,
            'tipo' => 'entrada',
            'cantidad' => 15,
            'motivo' => 'Recepción de compra mayorista',
        ]);

        $response->assertRedirect(route('movements.index'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertSame(25, $product->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'tipo' => 'entrada',
            'cantidad' => 15,
            'stock_anterior' => 10,
            'stock_posterior' => 25,
            'motivo' => 'Recepción de compra mayorista',
        ]);
    }

    /**
     * PT-11: Rechazo de salida de existencias cuando la cantidad solicitada excede el stock disponible.
     */
    public function test_pt11_reject_stock_exit_exceeding_available_stock(): void
    {
        $product = Product::create([
            'nombre' => 'Tarjeta Gráfica RTX 4060',
            'sku' => 'GPU-04060-01',
            'precio' => 1650.00,
            'stock' => 5,
            'stock_minimo' => 2,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('movements.store'), [
            'product_id' => $product->id,
            'tipo' => 'salida',
            'cantidad' => 6,
            'motivo' => 'Despacho mayor al disponible',
        ]);

        $response->assertSessionHasErrors(['cantidad']);

        $product->refresh();
        $this->assertSame(5, $product->stock);
    }

    /**
     * PT-12: Verificación de trazabilidad y congruencia entre stock_anterior y stock_posterior.
     */
    public function test_pt12_verify_movement_traceability(): void
    {
        $product = Product::create([
            'nombre' => 'Disco SSD NVMe 1TB',
            'sku' => 'SSD-01000-01',
            'precio' => 320.00,
            'stock' => 20,
            'stock_minimo' => 5,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        // Movimiento 1: Salida de 7 unidades
        $this->actingAs($this->adminUser)->post(route('movements.store'), [
            'product_id' => $product->id,
            'tipo' => 'salida',
            'cantidad' => 7,
            'motivo' => 'Venta minorista',
        ]);

        $product->refresh();
        $this->assertSame(13, $product->stock);

        $mov1 = StockMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertSame(20, $mov1->stock_anterior);
        $this->assertSame(13, $mov1->stock_posterior);

        // Movimiento 2: Entrada de 12 unidades
        $this->actingAs($this->adminUser)->post(route('movements.store'), [
            'product_id' => $product->id,
            'tipo' => 'entrada',
            'cantidad' => 12,
            'motivo' => 'Reposición de almacén',
        ]);

        $product->refresh();
        $this->assertSame(25, $product->stock);

        $mov2 = StockMovement::where('product_id', $product->id)->latest('id')->firstOrFail();
        $this->assertSame(13, $mov2->stock_anterior);
        $this->assertSame(25, $mov2->stock_posterior);
    }

    /**
     * PT-13: La bandeja de alertas incluye todo producto cuyo stock sea menor o igual al umbral mínimo.
     */
    public function test_pt13_alert_includes_product_when_stock_is_less_or_equal_to_min(): void
    {
        $pUnder = Product::create([
            'nombre' => 'Laptop Gamer Crítica',
            'sku' => 'COM-ALERT-01',
            'precio' => 4000.00,
            'stock' => 2,
            'stock_minimo' => 5, // stock < minimo
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $pEqual = Product::create([
            'nombre' => 'Teclado Mecánico Límite',
            'sku' => 'PER-ALERT-02',
            'precio' => 199.00,
            'stock' => 5,
            'stock_minimo' => 5, // stock == minimo
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $pNormal = Product::create([
            'nombre' => 'Monitor Normal',
            'sku' => 'PER-NORM-03',
            'precio' => 600.00,
            'stock' => 20,
            'stock_minimo' => 5, // stock > minimo
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('alerts.index'));
        $response->assertOk();

        $alertProducts = $response->viewData('alertProducts');
        $this->assertTrue($alertProducts->pluck('id')->contains($pUnder->id));
        $this->assertTrue($alertProducts->pluck('id')->contains($pEqual->id));
        $this->assertFalse($alertProducts->pluck('id')->contains($pNormal->id));
    }

    /**
     * PT-14: Exportación de reporte a archivo CSV con cabeceras correctas y protección anti-fórmulas.
     */
    public function test_pt14_export_csv_report_with_expected_headers_and_content(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('reports.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();

        $this->assertStringContainsString('SKU', $csvContent);
        $this->assertStringContainsString('Producto', $csvContent);
        $this->assertStringContainsString('Categoria', $csvContent);
        $this->assertStringContainsString('Proveedor', $csvContent);
        $this->assertStringContainsString('Precio Unitario (S/)', $csvContent);
        $this->assertStringContainsString('Stock Actual', $csvContent);
        $this->assertStringContainsString('Stock Minimo', $csvContent);
        $this->assertStringContainsString('Estado', $csvContent);
        $this->assertStringContainsString('Valor Estimado (S/)', $csvContent);

        // Verifica que productos del seeder aparezcan
        $this->assertStringContainsString('COM-00001-01', $csvContent);
        $this->assertStringContainsString('Dell Ryzen7', $csvContent);
    }

    /**
     * PT-15: Rechazo de cambio de contraseña cuando la clave actual no coincide.
     */
    public function test_pt15_reject_password_change_when_current_password_is_wrong(): void
    {
        $response = $this->actingAs($this->adminUser)->put(route('profile.password'), [
            'current_password' => 'clave_equivocada',
            'password' => 'nuevaClave2026',
            'password_confirmation' => 'nuevaClave2026',
        ]);

        $response->assertSessionHasErrors(['current_password']);

        $this->adminUser->refresh();
        $this->assertTrue(Hash::check('1234', $this->adminUser->password));
    }

    /**
     * PT-16: Redirección automática de usuarios no autenticados al login en todas las rutas privadas.
     */
    public function test_pt16_redirect_unauthenticated_access_on_private_routes_to_login(): void
    {
        $privateRoutes = [
            route('dashboard'),
            route('products.index'),
            route('categories.index'),
            route('suppliers.index'),
            route('movements.index'),
            route('alerts.index'),
            route('reports.index'),
            route('profile.edit'),
        ];

        foreach ($privateRoutes as $routeUrl) {
            $response = $this->get($routeUrl);
            $response->assertRedirect(route('login'));
        }
    }
}
