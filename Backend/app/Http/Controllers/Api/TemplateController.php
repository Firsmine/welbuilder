<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(){
        $templates = Template::with('fields')->get();
        return response()->json([
            'status'=>'success',
            'message'=>'Get all templates successful',
            'data'=>['templates'=>$templates]
        ], 200);
    }
    public function show($slug){
        $template = Template::with('fields')->where('slug', $slug)->first();

        if (!$template){
            return response()->json([
                'status'=>'error',
                'message'=>'Not found'
            ], 404);
        }
        return response()->json([
            'status'=>'success',
            'message'=>'Get template successful',
            'data'=>$template
        ], 200);
    }
}
