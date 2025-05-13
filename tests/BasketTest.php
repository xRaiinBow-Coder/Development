<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../cmp214/basketFunctions.php';
require_once __DIR__ . '/../cmp214/Product.php';
require_once __DIR__ . '/../cmp214/DB.php';

class ProductPageTest extends TestCase
{
    protected $dbMock;
    protected $pdoMock;
    protected $stmtMock;

    protected function setUp(): void
    {
        $_SESSION = [];

        // Mock DB, PDO, and PDOStatement
        $this->dbMock = $this->createMock(DB::class);
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);

        // Connect() returns PDO
        $this->dbMock->method('connect')->willReturn($this->pdoMock);

        // Prepare() returns statement mock
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
    }

    public function testProductFetchSuccess()
    {
        $this->stmtMock->method('fetch')->willReturnOnConsecutiveCalls(
            [
                'id' => 1,
                'name' => 'Example Product',
                'image' => 'img.jpg',
                'description' => 'Cool item',
                'price' => '9.99'
            ],
            false // End of fetch loop
        );

        $this->stmtMock->expects($this->once())->method('execute');

        // Simulate sorting
        $filterColumn = 'price';
        $sortOrder = 'ASC';
        $query = $this->dbMock->connect()->prepare("SELECT * FROM tbl_Productss ORDER BY CAST(price AS DECIMAL(10, 2)) $sortOrder");

        $query->execute();

        $products = [];
        while ($row = $this->stmtMock->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['image'],
                $row['description'],
                (float)$row['price']
            );
        }

        $this->assertNotEmpty($products);
        $this->assertEquals('Example Product', $products[0]->name());
    }

    public function testAddProductToBasket()
    {
        $_SESSION['basket'] = [];

        // Simulate product fetch by ID
        $this->stmtMock->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'Sample',
            'image' => 'image.jpg',
            'price' => 10.00
        ]);

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);

        add($this->dbMock, 1);

        $this->assertArrayHasKey('basket', $_SESSION);
        $this->assertCount(1, $_SESSION['basket']);
        $this->assertEquals(1, $_SESSION['basket'][0]['id']);
        $this->assertEquals(1, $_SESSION['basket'][0]['quantity']);
    }
}
