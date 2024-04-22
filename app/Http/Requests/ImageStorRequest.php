<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageStorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(){
        if(request()->isMethod( 'post')){
        return [
        'product_id'=>'required',
        'name'=>'required|string|max:258',
        'image'=>'required|image|mimes:jpeg,png,gif,svg|max:2048',
        'description'=>'required|string'
        ];

        }else{
        return[
        'product_id'=>'required',
        'name'=>'required|string|max:258',
        'image'=>'nullable|image|mimes:jpeg,png,gif,svg|max:2048',
        'description'=>'required|string',
        ];
        }
    }

    public function messages(){
        if(request()->isMethod( 'post')){
        return [
        'Product_id'=>'Product_id is required',
        'name.required'=>'name is required',
        'image.required'=>'image is required',
        'description.required'=>'description is required'
        ];

        }else{
        return[
            'name.required'=>'name is required',
            'description.required'=>'description is required'
        ];

        }
    }
}
