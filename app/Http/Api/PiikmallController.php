<?php

namespace App\Http\Controllers;

class PiikmallController extends Controller {
    public function piikmall() {
        return response([
            'message'=>'Welcome to Piikmall, the top 1 real e-commerce platform in Cambodia. Please visit https://piikmall.com to know more.',
        ]);
    }

    public function version() {
        return response([
            'version'=> config('hydra.version'),
        ]);
    }
}
