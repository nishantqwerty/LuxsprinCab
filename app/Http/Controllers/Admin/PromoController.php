<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{

    public function index()
    {
        $faqs = Faq::get();
        return view('admin.support.index', compact('faqs'));
    }

    public function add()
    {
        return view('admin.promo.add');
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'question'      =>  'required',
            'description'   =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $faq_data = [
                'question'  =>  $data['question'],
                'answer'    =>  $data['description']
            ];
            $faq = Faq::create($faq_data);
            if ($faq) {
                return redirect()->to('admin/support')->with('success', 'FAQ Added Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function edit($id)
    {
        $faq    =   Faq::find($id);
        if ($faq) {
            return view('admin.support.edit', compact('faq'));
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'question'      =>  'required',
            'description'   =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $faq = Faq::find($id);
            if ($faq) {
                $faq->update([
                    'question'  =>  $data['question'],
                    'answer'    =>  $data['description']
                ]);
                return redirect()->to('admin/support')->with('success', 'FAQ Updated Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function delete($id)
    {
        $faq    =   Faq::find($id);
        if ($faq) {
            $faq->delete();
            return back()->with('success', 'FAQ deleted Successfully.');
        } else {
            return back()->with('error', 'SOmething Went Wrong.');
        }
    }
}
