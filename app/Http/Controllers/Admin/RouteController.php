<?php

namespace App\Http\Controllers\Admin;

use App\Models\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Stops;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::get();
        return view('admin.routes.index', compact('routes'));
    }

    public function add()
    {
        return view('admin.routes.add');
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'      =>  'required',
            'source'    =>  'required',
            'dest'      =>  'required',
            'source_lat'       =>  'required',
            'source_long'      =>  'required',
            'dest_lat'       =>  'required',
            'dest_long'      =>  'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $route = [
                'name'          =>  $data['name'],
                'source'        =>  $data['source'],
                'destination'   =>  $data['dest'],
                'source_lat'    =>  $data['source_lat'],
                'source_long'   =>  $data['source_long'],
                'dest_lat'      =>  $data['dest_lat'],
                'dest_long'     =>  $data['dest_long'],
            ];
            $routes = Route::create($route);
            if ($routes) {
                $stop = [];
                foreach ($data['stop_name'] as $key => $name) {
                    $stop[$key]['name']   =   $name;
                }
                foreach ($data['stop_address'] as $key1 => $address) {
                    $stop[$key1]['address']   =   $address;
                }
                foreach ($data['stop_lat'] as $key2 => $stop_lat) {
                    $stop[$key2]['lat']   =   $stop_lat;
                }
                foreach ($data['stop_long'] as $key4 => $stop_long) {
                    $stop[$key4]['long']   =   $stop_long;
                }
            }
            foreach ($stop as $datastop) {
                $stops = Stops::create($datastop);
                $stops->update([
                    'route_id' => $routes->id,
                ]);
            }

            if ($routes && $stops) {
                return redirect()->to('admin/route-stops')->with('success', 'Route Added Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }

    public function delete($id)
    {
        $route = Route::find($id);
        if ($route) {
            $stops = Stops::where('route_id', $id)->get();
            $route->delete();
            $stops->each->delete();
            return back()->with('success', 'Route Deleted Successfully.');
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }

    public function edit($id)
    {
        $route = Route::find($id);
        if ($route) {
            $stops = Stops::where('route_id', $route->id)->get();
            if (!empty($stops)) {
                return view('admin.routes.edit', compact('route', 'stops'));
            }
        } else {
            return back()->with('error', 'Something Went Wrong.');
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name'      =>  'required',
            'source'    =>  'required',
            'dest'      =>  'required',
            'lat'       =>  'required',
            'long'      =>  'required',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        } else {
            $route = [
                'name'          =>  $data['name'],
                'source'        =>  $data['source'],
                'destination'   =>  $data['dest'],
                'lat'           =>  $data['lat'],
                'long'          =>  $data['long'],
            ];
            $routes = Route::find($id);
            if ($routes) {
                $prev_stops = Stops::where('route_id', $routes->id)->get();
                $prev_stops->each->delete();
                $routes->update($route);
                $stop = [];
                foreach ($data['stop_name'] as $key => $name) {
                    $stop[$key]['name']   =   $name;
                }
                foreach ($data['stop_address'] as $key1 => $address) {
                    $stop[$key1]['address']   =   $address;
                }
                foreach ($data['stop_lat'] as $key2 => $stop_lat) {
                    $stop[$key2]['lat']   =   $stop_lat;
                }
                foreach ($data['stop_long'] as $key4 => $stop_long) {
                    $stop[$key4]['long']   =   $stop_long;
                }
            }
            foreach ($stop as $datastop) {
                $stops = Stops::create($datastop);
                $stops->update([
                    'route_id' => $routes->id,
                ]);
            }

            if ($routes && $stops) {
                return redirect()->to('admin/route-stops')->with('success', 'Route Added Successfully.');
            } else {
                return back()->with('error', 'Something Went Wrong.');
            }
        }
    }
}
