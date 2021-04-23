<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Api;
use App\Library\MetatagHelper;

/**
 * Class VideoController
 * @package App\Http\Controllers
 */
class VideoController extends Controller
{
    /** @var Api */
    protected $api;

    /** @var MetatagHelper */
    protected $metatagHelper;

    /**
     * VideoController constructor.
     * @param Api $api
     * @param MetatagHelper $metatagHelper
     */
    public function __construct(
        Api $api,
        MetatagHelper $metatagHelper
    ) {
        $this->api = $api;
        $this->metatagHelper = $metatagHelper;
    }

    /**
     * Returns an iframe response for video embed.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function container(Request $request, $id)
    {
        $data = $this->api->request('videos/' . $id);

        if (empty($data['data'])) {
            abort(404);
        }

        return view('embed', [
            'video' => $data['data'][0],
        ]);
    }

    /**
     * View for a single video.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view(Request $request, $id, $slug)
    {
        $data = $this->api->request('videos/' . $id);

        if (empty($data['data'])) {
            abort(404);
        }

        $video = $data['data'][0];

        if (!$this->validateSlug($video['title_slug'], $slug)) {
            return redirect()->action('VideoController@view', [
                'id' => $id,
                'slug' => $video['title_slug'],
            ]);
        }

        $this->setMeta($video);

        return view('video', [
            'state' => $this->getAppState($id, $data),
            'src' => $video['src'],
            'thumbnailUrl' => '/images/d/large/' . $video['thumbnailId'] . '.jpg',
            'description' => $video['description'],
            'title' => $video['title'],
            'date' => date('M d, Y', strtotime($video['date_recorded'])),
            'trackUrl' => '/api/videos/' . $id . '/transcript?format=vtt',
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewJson(Request $request, $id, $slug)
    {
        $data = $this->api->request('videos/' . $id);

        if (empty($data['data'])) {
            abort(404);
        }

        $video = $data['data'][0];

        if (!$this->validateSlug($video['title_slug'], $slug)) {
            return redirect()->action('VideoController@viewJson', [
                'id' => $id,
                'slug' => $video['title_slug'],
            ]);
        }


        $state = $this->getAppState($id, $data);

        return response()->json($state);
    }

    /**
     * @param $data
     * @return array
     */
    public function getAppState($id, $data)
    {
        $state = [];
        if (!empty($data['data'])) {
            $state['video'] = $data['data'][0];
            $path = '/video/' . $id . '/' . $state['video']['title_slug'];
            $state['path'] = $path;
        }
        return $state;
    }

    /**
     * @param $data
     * @return array
     */
    public function setMeta($data)
    {
        $pageUrl = $this->metatagHelper->getCurrentUrl();
        $description = $data['description'] ? htmlentities(strip_tags($data['description'])) : false;
        $name = config('app.name');
        $title = $data['title'] ? $data['title'] .' | '. $name : $name;

        meta()
            ->set('title', $title)
            ->set('canonical', $pageUrl)
            ->set('description', $description)
            ->set('og:description', $description)
            ->set('twitter:url', $pageUrl)
            ->set('twitter:title', $title)
            ->set('twitter:description', $description)
            ->set('og:url', $pageUrl)
            ->set('og:title', $title)
            ->set('og:description', $description)
            ->set('og:type', 'video.other');

        if (isset($data['thumbnailId'])) {
            $imageUrl = $this->metatagHelper->getImageUrl($data['thumbnailId']);
            meta()
                ->set('twitter:image', $imageUrl)
                ->set('og:image', $imageUrl)
                ->set('og:image:type', 'image/jpg')
                ->set('og:image:width', 480)
                ->set('og:image:height', 270);
        }

        if (isset($data['asset_id'])) {
            $playerUrl = $this->metatagHelper->getPlayerUrl($data['asset_id']);
            meta()
                ->set('twitter:player', $playerUrl)
                ->set('twitter:card', 'player')
                ->set('twitter:player:width', 480)
                ->set('twitter:player:height', 480)
                ->set('og:video', $playerUrl)
                ->set('og:video:secure_url', $playerUrl)
                ->set('og:video:type', 'video/mp4')
                ->set('og:video:width', 480)
                ->set('og:video:height', 480);
        }
    }

    /**
     * Ensure pages are returned with the correct slug value.
     *
     * @param string $valid
     * @param string $submitted
     * @return bool
     */
    private function validateSlug(string $valid, string $submitted)
    {
        return $valid === $submitted;
    }
}
