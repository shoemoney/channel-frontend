<?php

namespace App\Http\Controllers;

use App\Actions\GenerateImageAction;

class ImageController extends Controller
{
    /**
     * Handle the incoming request.

     * @return \Illuminate\Http\Response
     */
    public function __invoke($template, $filename, GenerateImageAction $generateImageAction)
    {
        return $generateImageAction->execute($template, $filename);
    }
}
