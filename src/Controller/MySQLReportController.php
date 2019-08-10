<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts;
use App\Entity;

class MySQLReportController extends AbstractController
{
    public function dashboard()
    {
        $customersByCountry = $this->makeCustomersByCountryPieChart();
        $receiptsByMonth = $this->makeReceiptsbyMonth();
        $bestSales = $this->makeBestSalesProductsBarChart();
        $mostProfitable = $this->makeMostProfitableProductsBarChart();

        return $this->render('dashboardPage.html.twig', array(
            'customersByCountry'    => $customersByCountry,
            'receiptsByMonth'       => $receiptsByMonth,
            'bestSales'             => $bestSales,
            'mostProfitable'        => $mostProfitable,
        ));
    }

    private function makeCustomersByCountryPieChart() 
    {
        $customerRepo = $this->getDoctrine()->getRepository(Entity\Customer::class);
        $repoData = $customerRepo->findNumberOfCustomersByCountry();

        $dataHead = [
            [
                'name'      => 'Country',
                'amount'    => 'Amount'
            ],
        ];
        // Convert amount string into int
        $data = array_map(function($val) 
            {
                $val['amount'] = (int)$val['amount'];
                return $val;
            }, 
            $repoData
        );
        $data = array_merge($dataHead, $data);

        $pieChart = new Charts\PieChart();
        $pieChart->getData()->setArrayToDataTable($data);
        $pieChart->getOptions()
            ->setTitle('Customers by country')
            ->setHeight('250')
            ->setWidth('500')
            ->setPieHole(0.6)
            ->setColors($this->chooseColors(count($data)-1, true))
            ->setBackgroundColor('#3E454D')
        ;
        $pieChart->getOptions()
            ->getTitleTextStyle()
                ->setColor('white')
        ;
        $pieChart->getOptions()
            ->getLegend()
                ->getTextStyle()
                    ->setColor('white')
        ;

        return $pieChart;
    }

    private function makeReceiptsbyMonth() 
    {
        $orderRepo = $this->getDoctrine()->getRepository(Entity\Order::class);
        $from = new \DateTime('now');
        $from->modify('-1 year');
        $to = new \DateTime('now');
        $repoData = $orderRepo->findTotalReceiptsByMonth($from, $to);

        $dataHead = [
            [
                'date'      => 'Date',
                'receipts'  => 'Receipts'
            ],
        ];
        $data = [];
        $total = 0;
        while ($from <= $to) {
            $monthReceiptsIndex = array_intersect(array_keys(array_column($repoData, 'month'), $from->format('n')), array_keys(array_column($repoData, 'year'), $from->format('Y')));
            $monthReceiptsValue = count($monthReceiptsIndex) == 1 ? (int)$repoData[array_pop($monthReceiptsIndex)]['sum'] : 0;
            array_push(
                $data,
                [
                    'date'      => $from->format('m/y'),
                    'receipts'  => $monthReceiptsValue,
                ]
            );

            $from->modify('+1 month');
        }
        $data = array_merge($dataHead, $data);

        $lineChart = new Charts\LineChart();
        $lineChart->getData()->setArrayToDataTable($data);
        $lineChart->getOptions()
            ->setTitle('Receipts by month')
            ->setHeight('250')
            ->setWidth('500')
            ->setLineWidth(4)
            ->setColors($this->chooseColors(count($data)-1, true))
            ->setBackgroundColor('#3E454D')
        ;
        $lineChart->getOptions()
            ->getTitleTextStyle()
                ->setColor('white')
        ;
        $lineChart
            ->getOptions()
                ->getLegend()
                    ->setPosition('none')
        ;
        $lineChart
            ->getOptions()
                ->getHAxis()
                    ->setSlantedText(true)
                    ->setSlantedTextAngle(45)
                    ->getTextStyle()
                        ->setColor('white')
        ;

        return $lineChart;
    }

    public function makeBestSalesProductsBarChart() 
    {
        $orderRepo = $this->getDoctrine()->getRepository(Entity\Order::class);
        $from = new \DateTime('now');
        $from->modify('-1 year');
        $to = new \DateTime('now');
        $limit = 10;
        $repoData = $orderRepo->findBestSalesProducts($limit, $from, $to);
        
        $dataHead = [
            [
                'product'   => 'Product name',
                'amount'    => 'Number sold'
            ],
        ];
        // Convert amount string into int
        $data = array_map(function($val) 
            {
                $val['amount'] = (int)$val['amount'];
                return $val;
            }, 
            $repoData
        );
        $data = array_merge($dataHead, $data);

        $barChart = new Charts\BarChart();
        $barChart->getData()->setArrayToDataTable($data);
        $barChart->getOptions()
            ->setTitle('Best selling products')
            ->setHeight('250')
            ->setWidth('500')
            ->setColors($this->chooseColors(count($data)-1, true))
            ->setBackgroundColor('#3E454D')
        ;
        $barChart->getOptions()
            ->getTitleTextStyle()
                ->setColor('white')
        ;

        return $barChart;
    }

    public function makeMostProfitableProductsBarChart() 
    {
        $orderRepo = $this->getDoctrine()->getRepository(Entity\Order::class);
        $from = new \DateTime('now');
        $from->modify('-1 year');
        $to = new \DateTime('now');
        $limit = 10;
        $repoData = $orderRepo->findMostProfitableProducts($limit, $from, $to);
        
        $dataHead = [
            [
                'product'   => 'Product name',
                'amount'    => 'Gain'
            ],
        ];
        // Convert amount string into int
        $data = array_map(function($val) 
            {
                $val['amount'] = (int)$val['amount'];
                return $val;
            }, 
            $repoData
        );
        $data = array_merge($dataHead, $data);

        $barChart = new Charts\BarChart();
        $barChart->getData()->setArrayToDataTable($data);
        $barChart->getOptions()
            ->setTitle('Most profitable products')
            ->setHeight('250')
            ->setWidth('500')
            ->setColors($this->chooseColors(count($data)-1, true))
            ->setBackgroundColor('#3E454D')
        ;
        $barChart->getOptions()
            ->getTitleTextStyle()
                ->setColor('white')
        ;

        return $barChart;
    }

    private function chooseColors($elementNumber, $roundChart) 
    {
        $colorsList = [
            '#009EFE',
            '#004F80',
            '#4DBBFF',
            '#265D80',
            '#007ECC',
        ];

        //Prevent rounded chart like pieChart to have 2 items of same color side by side
        if($roundChart) {
            while($elementNumber % count($colorsList) == 1) {
                array_pop($colorsList);
            };
        }

        return $colorsList;
    }
}