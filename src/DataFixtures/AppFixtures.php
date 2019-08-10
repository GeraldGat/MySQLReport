<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Country;
use App\Entity\Customer;
use App\Entity\Order;

class AppFixtures extends Fixture
{
    const NBR_PRODUCTS = 100;
    const NBR_CUSTOMERS = 100;
    const NBR_MIN_ORDER_BY_CUSTOMERS = 0;
    const NBR_MAX_ORDER_BY_CUSTOMERS = 10;

    protected $manager;
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $products = $this->makeProducts($manager);
        $countries = $this->makeCountries($manager);
        $customers = $this->makeCustomers($manager, $countries);
        $oders = $this->makeOrders($manager, $customers, $products);

        $manager->flush();
    }

    private function makeProducts($manager) 
    {
        $products = [];

        for ($i = 0; $i < self::NBR_PRODUCTS; $i++) {
            $product = new Product();
            $product->setName('Product_'.$i);
            $product->setPrice(mt_rand(2, 2000));
            $manager->persist($product);
            array_push($products, $product);
        }

        return $products;
    }

    private function makeCountries($manager) 
    {
        $name = [
            'France',
            'Germany',
            'United Kingdom',
            'Luxembourg',
            'Spain',
        ];

        $countries = [];

        for ($i = 0; $i < count($name); $i++) {
            $country = new Country();
            $country->setName($name[$i]);
            $manager->persist($country);
            array_push($countries, $country);
        }

        return $countries;
    }

    private function makeCustomers($manager, $countries)
    {
        $customers = [];

        for ($i = 0; $i < self::NBR_CUSTOMERS; $i++) {
            $customer = new Customer();
            $customer->setFirstName($this->faker->firstName());
            $customer->setLastName($this->faker->lastName());
            $customer->setCountry($countries[mt_rand(0, count($countries)-1)]);
            $manager->persist($customer);
            array_push($customers, $customer);
        }

        return $customers;
    }

    public function makeOrders($manager, $customers, $products)
    {
        $orders = [];

        foreach ($customers as $customer) {
            $order = new Order();
            $order->setCustomer($customer);
            $order->setDate($this->faker->dateTimeInInterval('now', '-1 years'));
            $articleAmount = mt_rand(self::NBR_MIN_ORDER_BY_CUSTOMERS, self::NBR_MAX_ORDER_BY_CUSTOMERS);
            for ($j = 0; $j < $articleAmount; $j++) {
                $order->addProduct($products[mt_rand(0, count($products)-1)]);
            }
            $manager->persist($order);
            array_push($orders, $order);
        }

        return $orders;
    }
}
