<?php

namespace Tests\Browser;

use App\Api;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Class VideoPlayerTest
 * @package Tests\Browser
 */
class VideoPlayerTest extends DuskTestCase
{
    /**
    * Test video player attributes
     *
     * @throws \Throwable
     */
    public function testVideoPlayer()
    {
        $this->browse(function (Browser $browser) {
//            $browser->visit('/video/1')
//                ->assertTrue();
//                ->whenAvailable('video', function ($video) {
//                    $video->assertSourceHas(
//                        'src="https://trial10-8.assetbank-server.com/assetbank-trial10/rest/assets/232/content"'
//                    );
//                });
        });
    }
}
