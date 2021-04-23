<?php

namespace App;

/**
 * Class FakeApi
 *
 * A fake class used for mocking an API response in dusk tests.
 * @codingStandardsIgnoreFile
 *
 * @package App
 */
class FakeApi extends Api
{
    /**
     * @param $type
     * @param bool $id
     * @return array
     */
    public function request($type, $id = false, $queryString = '')
    {
        $returnData = [
            'success' => true,
            'data' => [json_decode($this->getJsonData(), true)],
            'pages' => [],
            'aggregations' => []
        ];

        return $returnData;
    }

    /**
     *
     * @return string
     */
    public function getJsonData()
    {
        return '{
        "asset_id": 100,
  "title": "Sister Spit: The Next Generation",
  "description": "From scrappy beginnings in 1994, Sister Spit tours have since gathered a rotating roster of critically acclaimed participants. Courageous, hilarious, outspoken – these fierce feminists offer multifaceted approaches to passionate stage presentations. Tonight, writer and activist Virgie Tovar hosts author and zinemaker Myriam Gurba, “Self-Made Man” Thomas Page McBee, Rad American Women A-Z author Kate Schatz and illustrator Miriam Klein Stahl, and drag artist Mica Sigourney. Our special L.A. guests are writers Francesca Lia Block (the Weetzie Bat series) and Nikki Darling (Pink Trumpet and the Purple Prose), and artist/performer Zackary Drucker. Check out the participants’ publications, for sale at the merchandise table, and enjoy refreshments from the cash bar all night!",
  "thumbnail_url": null,
  "src": "https://trial10-8.assetbank-server.com/assetbank-trial10/rest/assets/232/content",
  "date_recorded": "2019-12-09 12:05:57",
  "duration": "00:01:43",
  "created_at": "2019-12-09 11:52:24",
  "updated_at": "2019-12-09 11:52:24",
  "transcription": "",
  "tags": []
}';
    }
}
