<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\SectionFieldValue;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageSectionController extends Controller
{
    private function checkAccess($slug, $user){
        $page = Page::where('slug', $slug)->first();

        if (!$page) return [
            'status'=>404,
            'message'=>'Not found'
        ];
        if ($page->user_id !== $user->id) return [
            'status'=>403,
            'message'=>'Forbidden access'
        ];


        return [
            'status'=>200,
            'page'=>$page
        ];
    }

    public function addSection(Request $request, $slug){
        $check = $this->checkAccess($slug, $request->user());
        if ($check['status'] !== 200)
            return response()->json([
            'status'=>'error',
            'message'=>$check['message']
        ], $check['status']);

        $validator = Validator::make($request->all(), [
            'template_id'=>'required|exists:templates,id',
            'position'=>'required|integer|min:1'
        ]);
        if ($validator->fails())
            return response()->json([
            'status'=>'error',
            'message'=>'Invalid field',
            'errors'=>$validator->errors()
        ], 442);

        $page = $check['page'];

        DB::beginTransaction();
        try{
            $template = Template::with('fields')->find($request->template_id);

            if (!$template) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Template not found'
                ], 404);
            }
        
            $section = PageSection::create([
                'page_id'=>$page->id,
                'template_id'=>$request->template_id,
                'position'=>$request->position,
                'name' => $template->name
            ]);
            foreach ($template->fields as $field) {
                SectionFieldValue::create([
                    'page_section_id' => $section->id,
                    'template_field_id' => $field->id,
                    'value' => null
                ]);
            }
            DB::commit();

            // $section->load(['template', 'fieldValues']);

            // Format output
            $formattedFields = $template->fields->map(function ($f) {
                return [
                    'id' => $f->id, 
                    'name' => $f->name, 
                    'slug' => $f->slug, 
                    'type' => $f->type, 
                    'value' => null
                ];
            })->values();

            $resData = $section->toArray();
            $resData['fields'] = $formattedFields;

            return response()->json([
                'status' => 'success', 
                'message' => 'Section added successful', 
                'data' => $resData
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error', 
                'message' => 'Server error',
                'error'=>$e->getMessage()
            ], 500);
        }
    }

    public function updateFields(Request $request, $slug, $sectionId){
        $check = $this->checkAccess($slug, $request->user());
        if ($check['status']!==200)
            return response()->json([
            'status'=>'error',
            'message'=>$check['message']
        ], $check['status']);

        $section = PageSection::where('id', $sectionId)->where('page_id', $check['page']->id)->first();
        if (!$section)
            return response()->json([
            'status'=>'error',
            'message'=>'Not found'
        ], 404);
        $validator = Validator::make($request->all(), [
            'fields' => 'required|array',
            'fields.*.field_id' => 'required|integer|exists:template_fields,id',
            'fields.*.value' => 'nullable|string'
        ]);
        if ($validator->fails())
            return response()->json([
            'status'=>'error',
            'message'=>'Invalid field',
            'errors'=>$validator->errors()
        ], 422);

        foreach ($request->fields as $fieldInput){
            SectionFieldValue::updateOrCreate([
                    'page_section_id' => $section->id,
                    'template_field_id' => $fieldInput['field_id']
                ],
                ['value' => $fieldInput['value']]
            );
        }
        return $this->buildSectionResponse($section, 'Section fields updated successful');
    }

    public function reorderSections(Request $request, $slug){
        $check = $this->checkAccess($slug, $request->user());
        if ($check['status']!==200)
            return response()->json([
            'status' => 'error',
            'message'=>$check['message']
        ], $check['status']);

        $validator = Validator::make($request->all(), [
            'sections' => 'required|array',
            'sections.*' => 'integer|exists:page_sections,id'
        ]);
        if ($validator->fails())
            return response()->json([
            'status'=>'error',
            'message'=>'Invalid field',
            'error'=>$validator->errorS()
        ], 422);
        foreach ($request->sections as $index => $secId){
            PageSection::where('id', $secId)
                ->where('page_id', $check['page']->id)->update(['position'=>$index+1]);
        }
        return response()->json([
            'status'=>'success',
            'message'=>'Sections reordered successful'
        ], 200);
    }

    public function removeSection(Request $request, $slug, $sectionId){
        $check = $this->checkAccess($slug, $request->user());
        if ($check['status'] !== 200) 
            return response()->json([
            'status' => 'error', 
            'message' => $check['msg']
        ], $check['status']);

        $section = PageSection::where('id', $sectionId)->where('page_id', $check['page']->id)->first();
        
        if (!$section) 
            return response()->json([
            'status' => 'error', 
            'message' => 'Not found'
        ], 404);

        $section->delete();

        return response()->json([
            'status' => 'success', 
            'message' => 'Section removed successful'
        ], 200);
    }

    public function buildSectionResponse($section, $message){
        $section->load(['template.fields', 'fieldValues']);

        $formattedFields = $section->template->fields->map(function ($f) use ($section) {
            $val = $section->fieldValues->where('template_field_id', $f->id)->first();
            return [
                'id' => $f->id, 
                'name' => $f->name, 
                'slug' => $f->slug, 
                'type' => $f->type, 
                'value' => $val ? $val->value : null
            ];
        })->values();

        $data = $section->toArray();
        $data['fields'] = $formattedFields;

        return response()->json([
            'status' => 'success', 
            'message' => $message, 
            'data' => $data
        ], 200);
    }
}
