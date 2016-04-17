<?php


use App\Controllers\ProductsDbService;
use App\Kernel\IoC;

class ProductsCest
{
    public function _before(IntegrationTester $I)
    {
    }

    public function _after(IntegrationTester $I)
    {
    }


    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_creates_product(IntegrationTester $I)
    {
        $expectedData = [
            'slug'        => 'expected-slug',
            'name'        => 'expected-name',
            'price'       => 'expected-price',
            'description' => 'expected-description',
        ];

        $I->dontSeeInDatabase('products', $expectedData);

        $productsDbService = IoC::resolve(ProductsDbService::class);

        $I->assertNotSame(false, $actualProductId = $productsDbService->create($expectedData));

        $I->seeInDatabase('products', $expectedData);
    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_finds_category_by_slug(IntegrationTester $I)
    {
        $expectedData = [
            'slug'        => 'expected-slug',
            'name'        => 'expected-name',
            'price'       => 'expected-price',
            'description' => 'expected-description',
        ];

        $expectedProductId = $I->haveInDatabase('products', $expectedData);

        $productsDbService = IoC::resolve(ProductsDbService::class);
        $actualProduct = $productsDbService->findBySlug('expected-slug');

        $I->assertEquals($expectedProductId, $actualProduct['id']);

        $I->assertEquals($expectedData,
            array_intersect_key($actualProduct, array_flip(['slug', 'name', 'price', 'description'])));
    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_updates_product_if_slug_is_found(IntegrationTester $I)
    {
        $productDbService = IoC::resolve(ProductsDbService::class);

        $expectedData = [
            'old' =>
                [
                    'slug'        => 'expected-slug',
                    'name'        => 'expected-name-old',
                    'price'       => 'expected-price-old',
                    'description' => 'expected-description-old',
                ],
            'new' =>
                [
                    'slug'        => 'expected-slug',
                    'name'        => 'expected-name-new',
                    'price'       => 'expected-price-new',
                    'description' => 'expected-description-new',
                ],
        ];

        $I->haveInDatabase('products', $expectedData['old']);

        $I->assertNotSame(false, $actualProduct = $productDbService->updateOrCreate($expectedData['new']));

        $I->dontSeeInDatabase('products', $expectedData['old']);
        $I->seeInDatabase('products', $expectedData['new']);
    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_creates_product_if_slug_is_not_found(IntegrationTester $I)
    {
        $productDbService = IoC::resolve(ProductsDbService::class);

        $expectedData = [
            'slug'        => 'expected-slug',
            'name'        => 'expected-name-old',
            'price'       => 'expected-price-old',
            'description' => 'expected-description-old',
        ];

        $I->dontSeeInDatabase('products', $expectedData);

        $I->assertNotSame(false, $actualProduct = $productDbService->updateOrCreate($expectedData));

        $I->seeInDatabase('products', $expectedData);
    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_updates_a_product(IntegrationTester $I)
    {
        $productDbService = IoC::resolve(ProductsDbService::class);

        $expectedData = [
            'old' =>
                [
                    'slug'        => 'slug-old',
                    'name'        => 'name-old',
                    'price'       => 'price-old',
                    'description' => 'description-old',
                ],
            'new' =>
                [
                    'slug'        => 'slug-new',
                    'name'        => 'name-new',
                    'price'       => 'price-new',
                    'description' => 'description-new',
                ],
        ];

        $I->haveInDatabase('products', $expectedData['old']);

        $I->assertNotEquals(false,
            $productDbService->updateBySlug($expectedData['new'], $expectedData['old']['slug']));

        $I->dontSeeInDatabase('products', $expectedData['old']);

        $I->seeInDatabase('products', $expectedData['new']);
    }

    /**
     * @test
     * @param IntegrationTester $I
     */
    public function it_finds_category_by_id(IntegrationTester $I)
    {
        $expectedData = [
            'slug'        => 'expected-slug',
            'name'        => 'expected-name',
            'price'       => 'expected-price',
            'description' => 'expected-description',
        ];

        $productId = $I->haveInDatabase('products', $expectedData);

        $productsDbService = IoC::resolve(ProductsDbService::class);

        $actualProduct = $productsDbService->findById($productId);

        $I->assertEquals($productId, $actualProduct['id']);

        $I->assertEquals($expectedData,
            array_intersect_key($actualProduct, array_flip(['slug', 'name', 'price', 'description'])));
    }

}