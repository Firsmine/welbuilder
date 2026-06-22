<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request){
        $pages = $request->user()->pages;
        return response()->json([
            'status' => 'success',
            'message' => 'Get all pages successful',
            'data' => ['pages' => $pages]
        ], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required|string|unique:pages|regex:/^[a-z-0-9-]+$/',
            'summary' => 'nullable|string'
        ]);
        if ($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }
        $page = $request->user()->pages()->create($validator->validated());

        return response()->json([
            'status'=>'success',
            'message'=>'Page created successful',
            'data'=>$page
        ], 200);
    }

    public function show(Request $request, $slug){
        $page = Page::with(['sections.template', 'sections.fieldValues.templateField'])
            ->where('slug', $slug)->first();
        
        if(!$page){
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if ($page->user_id !== $request->user()->id){
            return response()->json([
                'status'=>'error',
                'message'=>'Forbidden access'
            ], 403);
        }
        // mapping format
        $formattedSections = $page->sections->map(function ($section){
            return [
                'id'=>$section->id,
                'position'=>$section->section,
                'template'=>$section->template,
                'field'=>$section->template->fields->map(function ($tplField) use ($section){
                    $val = $section->fieldValues->where('template_field_id', $tplField->id)->first();
                    return [
                        'id'=>$tplField->id,
                        'name'=>$tplField->name,
                        'slug'=>$tplField->slug,
                        'type'=>$tplField->type,
                        'value'=>$val ? $val->value : null
                    ];
                })->values()
            ];
        });
        $pageData = $page->toArray();
        $pageData['sections'] = $formattedSections;

        return response()->json([
            'status'=>'success',
            'message'=>'Get page successful',
            'data'=>$pageData
        ], 200);
    }

    public function update(Request $request, $slug){
        $page = Page::where('slug', $slug)->first();

        if (!$page)
            return response()->json([
            'status'=>'error',
            'message'=>'Not found'
        ], 404);
        if ($page->user_id !== $request->user()->id)
            return response()->json([
            'status'=>'error',
            'message'=>'Forbidden access'
        ], 403);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'slug' => ['nullable', 'string', 'regex:/^[a-z-0-9-]+$/', Rule::unique('pages')->ignore($page->id)],
            'summary' => 'nullable|string'
        ]);
        if ($validator->fails())
            return response()->json([
            'status'=>'error',
            'message'=>'Invalid field',
            'errors'=>$validator->errors()
        ], 422);
        $page->update($validator->validated());

        return response()->json([
            'status'=>'success',
            'message'=>'Page updated successful',
            'data'=>$page
        ], 200);
    }

    public function destroy(Request $request, $slug){
        $page = Page::where('slug', $slug)->first();

        if (!$page)
            return response()->json([
            'status'=>'error',
            'message'=>'Not found'
        ], 404);
        if ($page->user_id !== $request->user()->id)
            return response()->json([
            'status'=>'error',
            'message'=>'Forbidden access'
        ], 403);
        $page->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'Page deleted successful'
        ], 200);
    }
}
