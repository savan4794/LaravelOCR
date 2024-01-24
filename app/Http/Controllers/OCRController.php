<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

class OCRController extends Controller
{
    public function googleOCRView(){
        return view('welcome');
    }
    public function googleOCR(Request $request){
        try {

            $imageAnnotatorClient  = new ImageAnnotatorClient(
                [
                    'credentials' => json_decode(file_get_contents('g-cloud.json'), true)
                ]
            );

            $image_path = $request->file('image')->getRealPath();
            $imageContent = file_get_contents($image_path);
            $response = $imageAnnotatorClient->textDetection($imageContent);
            $text = $response->getTextAnnotations();



            if ($error = $response->getError()) {
                print('API Error: ' . $error->getMessage() . PHP_EOL);
            } else {
                return view('response',['response'=>nl2br($text[0]->getDescription())]);
            }

            $imageAnnotatorClient->close();
        } catch(\Exception $e) {
            \Log::alert($e->getMessage());
        }
    }
}
