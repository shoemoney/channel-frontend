<?php

namespace Tests\Feature;

use App\Library\Pagination;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCorrectPagerQueriesAreGeneratedFromPageInformation()
    {
        $pagination = new Pagination();

        $fakePageInfo = ["previous" => "", "next" => "page=2"];
        $additionalParams = [];
        $output = $pagination->generatePagerQueries($fakePageInfo, $additionalParams);
        $expected = ['previous' => "", "next" => "page=2"];
        $this->assertEquals($output, $expected);

        $fakePageInfo = ["previous" => "page=1", "next" => "page=3"];
        $additionalParams = [];
        $output = $pagination->generatePagerQueries($fakePageInfo, $additionalParams);
        $expected = ['previous' => "page=1", "next" => "page=3"];
        $this->assertEquals($output, $expected);

        $fakePageInfo = ["previous" => "page=1", "next" => "page=3"];
        $additionalParams = ['term' => 'hello'];
        $output = $pagination->generatePagerQueries($fakePageInfo, $additionalParams);
        $expected = ['previous' => "term=hello&page=1", "next" => "term=hello&page=3"];
        $this->assertEquals($output, $expected);

        $fakePageInfo = ["previous" => "page=1", "next" => "page=3"];
        $additionalParams = ['term' => 'hello'];
        $output = $pagination->generatePagerQueries($fakePageInfo, $additionalParams);
        $expected = ['previous' => "term=hello&page=1", "next" => "term=hello&page=3"];
        $this->assertEquals($output, $expected);

        $pagination = new Pagination();
        $fakePageInfo = ["previous" => "page=1", "next" => "page=3"];
        $additionalParams = ['term' => 'hello', 'sort' => 'my_sort'];
        $output = $pagination->generatePagerQueries($fakePageInfo, $additionalParams);
        $expected = ['previous' => "term=hello&sort=my_sort&page=1", "next" => "term=hello&sort=my_sort&page=3"];
        $this->assertEquals($output, $expected);
    }
}
