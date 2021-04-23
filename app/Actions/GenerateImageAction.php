<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;
use Intervention\Image\Exception\NotReadableException;

class GenerateImageAction
{
    /**
     * The base URL for remote images.
     * @var string $basUrl
     */
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('constants.imagesPath');
    }
    /**
     * Generates a thumbnail image.
     *
     * @param string $template
     *  The requested image template.
     *
     * @param string $id
     *  The requested image identifier.
     */
    public function execute($template, $id)
    {
        $pathinfo = pathinfo($id);
        $filename = $pathinfo['filename'];
        $extension = $pathinfo['extension'];
        $directory = "images/d/$template/";
        $cachePath = "$directory/$filename.$extension";

        if (empty($this->baseUrl)) {
            Log::error('Images base URL has not been defined.');
            abort(503);
        }

        $allowedExtensions = ['webp', 'jpg'];
        if (!in_array($extension, $allowedExtensions)) {
            abort(404);
        }

        $filter = $this->getFilter($template);
        if (!$filter) {
            abort(404);
        }

        $exists = Storage::disk('local')->has($cachePath);
        if ($exists) {
            $path = Storage::path($cachePath);
            return response()->file($path);
        }

        $image = $this->getImage($id, $filter);
        if (!$image) {
            abort(404);
        }

        Storage::put($cachePath, $image->encode($extension, 60), 'public');

        return $image->response();
    }

    /**
     * Get a template object from given template name
     *
     * @param  string $name
     * @return mixed
     */
    private function getFilter($name)
    {
        $className = "App\\Filters\\" . ucfirst($name);
        if (class_exists($className)) {
            return new $className();
        } else {
            return false;
        }
    }

    /**
     * Returns the full image path for a given filename.
     *
     * @param  string $filename
     * @return string
     */
    private function getImage($id, $filter)
    {
        if (!$filter) {
            return false;
        }

        $url = "{$this->baseUrl}{$id}";
        try {
            $img = Image::make($url)->filter($filter);
            return $img;
        } catch (NotReadableException $e) {
            Log::error('failed to read source image.', ['context' => $url]);
            return false;
        }
    }
}
