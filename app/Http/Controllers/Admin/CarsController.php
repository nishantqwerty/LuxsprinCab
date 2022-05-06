<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarCategory;
use App\Models\CarFare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarsController extends Controller
{
    public function index()
    {
        $categories = CarFare::with('carCategory')->get();
        return view('admin.cars.index', compact('categories'));
    }

    public function add()
    {
        $categories = CarCategory::get();
        return view('admin.cars.add', compact('categories'));
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'car_cat'   =>  'required',
            'fare'      =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors);
        } else {
            $fare = [
                'category_id'   =>  $data['car_cat'],
                'fare'          =>  $data['fare']
            ];
            $carcheck = CarFare::where('category_id', $data['car_cat'])->first();
            if ($carcheck) {
                $carcheck->update([
                    'fare'  => $data['fare']
                ]);
                return redirect()->to('admin/car-fare')->with('success', 'Fare Updated Successfully.');
            } else {
                $car_fare   =   CarFare::create($fare);
                if ($car_fare) {
                    return redirect()->to('admin/car-fare')->with('success', 'Fare Added Successfully.');
                } else {
                    return back()->with('error', 'Something Went Wrong.');
                }
            }
        }
    }

    public function edit($id)
    {
        $fare   =   CarFare::with('carCategory')->find($id);
        return view('admin.cars.edit', compact('fare'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'fare'      =>  'required'
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors);
        } else {
            $carcheck = CarFare::find($id);
            if ($carcheck) {
                $carcheck->update([
                    'fare'  => $data['fare']
                ]);
                return redirect()->to('admin/car-fare')->with('success', 'Fare Updated Successfully.');
            }
        }
    }

    public function delete($id)
    {
        $car_fare = CarFare::find($id);
        if ($car_fare) {
            $car_fare->delete();
            return back()->with('success', 'Fare Deleted Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }
}
