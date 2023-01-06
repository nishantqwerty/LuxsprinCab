<?php

namespace App\Http\Controllers\Driver;

use App\Models\User;
use App\Models\CarDetail;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\DriverDocument;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ApiController;
use App\Models\RejectDocument;
use Illuminate\Support\Facades\Validator;

class DashboardController extends ApiController
{

    public function onlineOffline($status)
    {
        $user = User::find(auth('api')->user()->id);
        if ($user) {
            $user->update([
                'is_online' =>  $status
            ]);
            return $this->result_ok('Status Updated Successfully.');
        } else {
            return $this->result_fail('Something Went Wrong.');
        }
    }

    public function bankAccount(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'bank_name'                 =>  'required',
            'account_number'            =>  'required|unique:bank_accounts,account_number',
            'confirm_account_number'    =>  'required|same:account_number',
            'ifsc_code'                 =>  'required',
            'beneficiary_name'          =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $details = array(
                'user_id'   =>  auth('api')->user()->id,
                'bank_name' =>  $data['bank_name'],
                'account_number' =>  $data['account_number'],
                'ifsc_code' =>  $data['ifsc_code'],
                'beneficiary_name' =>  $data['beneficiary_name'],
            );
            if (BankAccount::create($details)) {
                $user = User::find(auth('api')->user()->id);
                if ($user) {
                    $user->update([
                        'is_validated'  =>  DRIVER_UNDER_VERIFICATION
                    ]);
                }
                return $this->result_message('Bank Account Added Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function documents(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'brand'         =>  'required',
            'category_id'   =>  'required',
            'brand_model'   =>  'required',
            'model_year'    =>  'required|numeric',
            'color'         =>  'required',
            'car_number'    =>  'required|unique:car_details,car_number',
            'capacity'      =>  'required',
            'vin'           =>  'required',
            'license_number' =>  'required|unique:driver_documents,license_number',
            'expiry_date'   =>  'required|date',
            'license_front_side'    =>  'required',
            'license_back_side'     =>  'required',
            'insurance_number'     =>  'required',
            'insurance_expiry_date'     =>  'required|date',
            'insurance_image'     =>  'required',
            'car_registeration'     =>  'required|unique:driver_documents,car_registeration',
            'registeration_expiry_date' =>  'required|date',
            'registeration_image'   =>  'required',
            'inspection_date'       =>  'required|date',
            'inspection_photo'      =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $car_details = [
                'user_id'       =>  auth('api')->user()->id,
                'category_id'   =>  $data['category_id'],
                'brand'         =>  $data['brand'],
                'brand_model'   =>  $data['brand_model'],
                'model_year'    =>  $data['model_year'],
                'color'         =>  $data['color'],
                'car_number'    =>  $data['car_number'],
                'capacity'      =>  $data['capacity'],
                'vin'           =>  $data['vin']
            ];

            $license_details = [
                'user_id'       =>  auth('api')->user()->id,
                'license_number'            =>  $data['license_number'],
                'expiry_date'               =>  date('Y-m-d', strtotime($data['expiry_date'])),
                'insurance_number'          =>  $data['insurance_number'],
                'insurance_expiry_date'     =>  date('Y-m-d', strtotime($data['insurance_expiry_date'])),
                'car_registeration'         =>  $data['car_registeration'],
                'car_registeration_expiry_date' =>  date('Y-m-d', strtotime($data['registeration_expiry_date'])),
                'car_inspection_date'       =>  date('Y-m-d', strtotime($data['inspection_date'])),
            ];
            if ($request->has('license_front_side')) {
                $filename = time() . '.' . $request->license_front_side->extension();
                $request->license_front_side->storeAs('public/license_front', $filename);
                $license_details['license_front_side'] = $filename;
            }
            if ($request->has('license_back_side')) {
                $filename = time() . '.' . $request->license_back_side->extension();
                $request->license_back_side->storeAs('public/license_back', $filename);
                $license_details['license_back_side'] = $filename;
            }
            if ($request->has('registeration_image')) {
                $filename = time() . '.' . $request->registeration_image->extension();
                $request->registeration_image->storeAs('public/registeration_image', $filename);
                $license_details['car_registeration_photo'] = $filename;
            }
            if ($request->has('inspection_photo')) {
                $filename = time() . '.' . $request->inspection_photo->extension();
                $request->inspection_photo->storeAs('public/inspection_photo', $filename);
                $license_details['car_inspection_photo'] = $filename;
            }
            if ($request->has('insurance_image')) {
                $filename = time() . '.' . $request->insurance_image->extension();
                $request->insurance_image->storeAs('public/insurance', $filename);
                $license_details['insurance_image'] = $filename;
            }
            $license = DriverDocument::create($license_details);
            if ($license) {
                $car = CarDetail::create($car_details);
            }

            if ($car && $license) {
                $user = User::find(auth('api')->user()->id);
                if ($user) {
                    $user->update([
                        'is_validated' => BANK_DETAILS_PENDING,
                    ]);
                }
                return $this->result_message('Documents Uploaded Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function bankAccountDetail()
    {
        $user = BankAccount::where('user_id', auth('api')->user()->id)->first();
        if ($user) {
            return $this->result_ok('Bank Detail', $user);
        } else {
            return $this->result_fail('No Bank Account detail exists.');
        }
    }

    public function vehicleInfo()
    {
        $info = CarDetail::where('user_id', auth('api')->user()->id)->with(['documents', 'category', 'brand', 'brandModel'])->first();
        if ($info) {
            $info['documents']['license_front_side'] = asset('storage/license_front/' . $info->documents->license_front_side);
            $info['documents']['license_back_side'] = asset('storage/license_back/' . $info->documents->license_back_side);
            $info['documents']['car_registeration_photo'] = asset('storage/registeration_image/' . $info->documents->car_registeration_photo);
            $info['documents']['car_inspection_photo'] = asset('storage/inspection_photo/' . $info->documents->car_inspection_photo);
            $info['documents']['insurance_image'] = asset('storage/insurance/' . $info->documents->insurance_image);
            return $this->result_ok('Car Detail', $info);
        } else {
            return $this->result_fail('No car added for the driver.');
        }
    }

    public function updateBankAccount(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'bank_name' => 'required',
            'account_number' => [
                'required',
                Rule::unique('bank_accounts')->ignore(auth('api')->user()->id, 'user_id')
            ],
            // 'account_number'    =>  'required|unique:bank_accounts,account_number,'.auth('api')->user()->id,
            'confirm_account_number'    =>  'required|same:account_number',
            'ifsc_code'     =>  'required',
            'beneficiary_name'  =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $account = BankAccount::where('user_id', auth('api')->user()->id)->first();
            if ($account) {
                $details = [
                    'bank_name'     =>  $data['bank_name'],
                    'account_number'    =>  $data['account_number'],
                    'ifsc_code'     =>  $data['ifsc_code'],
                    'beneficiary_name'  =>  $data['beneficiary_name']
                ];
                $account->update($details);
                return $this->result_message('Bank Details Updated Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }

    public function updateDocuments(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'category_id'   =>  'required',
            'brand'         =>  'required',
            'brand_model'   =>  'required',
            'model_year'    =>  'required|numeric',
            'color'         =>  'required',
            'car_number'    =>  [
                'required',
                Rule::unique('car_details')->ignore(auth('api')->user()->id, 'user_id')
            ],
            'capacity'      =>  'required',
            'vin'           =>  'required',
            // 'license_number' =>  'required|unique:driver_documents,license_number',
            'license_number' =>  [
                'required',
                Rule::unique('driver_documents')->ignore(auth('api')->user()->id, 'user_id')
            ],
            'expiry_date'   =>  'required|date',
            'license_front_side'    =>  'required',
            'license_back_side'     =>  'required',
            'insurance_number'     =>  'required',
            'insurance_expiry_date'     =>  'required|date',
            'insurance_image'     =>  'required',
            // 'car_registeration'     =>  'required|unique:driver_documents,car_registeration',
            'car_registeration'     =>  [
                'required',
                Rule::unique('driver_documents')->ignore(auth('api')->user()->id, 'user_id')
            ],
            'registeration_expiry_date' =>  'required|date',
            'registeration_image'   =>  'required',
            'inspection_date'       =>  'required|date',
            'inspection_photo'      =>  'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (!empty($errors)) {
                foreach ($errors->all() as $error) {
                    return $this->result_fail($error);
                }
            }
        } else {
            $car_details = [
                'brand'         =>  $data['brand'],
                'category_id'   =>  $data['category_id'],
                'brand_model'   =>  $data['brand_model'],
                'model_year'    =>  $data['model_year'],
                'color'         =>  $data['color'],
                'car_number'    =>  $data['car_number'],
                'capacity'      =>  $data['capacity'],
                'vin'           =>  $data['vin'],
                'available_seats'      =>  $data['capacity'],
            ];

            $license_details = [
                'license_number'            =>  $data['license_number'],
                'expiry_date'               =>  date('Y-m-d', strtotime($data['expiry_date'])),
                'insurance_number'          =>  $data['insurance_number'],
                'insurance_expiry_date'     =>  date('Y-m-d', strtotime($data['insurance_expiry_date'])),
                'car_registeration'         =>  $data['car_registeration'],
                'car_registeration_expiry_date' =>  date('Y-m-d', strtotime($data['registeration_expiry_date'])),
                'car_inspection_date'       =>  date('Y-m-d', strtotime($data['inspection_date'])),
            ];
            if (strpos($request->license_front_side, "http") !== 0) {
                if ($request->has('license_front_side')) {
                    $filename = time() . '.' . $request->license_front_side->extension();
                    $request->license_front_side->storeAs('public/license_front', $filename);
                    $license_details['license_front_side'] = $filename;
                }
            }
            if (strpos($request->license_back_side, "http") !== 0) {
                if ($request->has('license_back_side')) {
                    $filename = time() . '.' . $request->license_back_side->extension();
                    $request->license_back_side->storeAs('public/license_back', $filename);
                    $license_details['license_back_side'] = $filename;
                }
            }
            if (strpos($request->registeration_image, "http") !== 0) {
                if ($request->has('registeration_image')) {
                    $filename = time() . '.' . $request->registeration_image->extension();
                    $request->registeration_image->storeAs('public/registeration_image', $filename);
                    $license_details['car_registeration_photo'] = $filename;
                }
            }

            if (strpos($request->inspection_photo, "http") !== 0) {
                if ($request->has('inspection_photo')) {
                    $filename = time() . '.' . $request->inspection_photo->extension();
                    $request->inspection_photo->storeAs('public/inspection_photo', $filename);
                    $license_details['car_inspection_photo'] = $filename;
                }
            }
            if (strpos($request->insurance_image, "http") !== 0) {
                if ($request->has('insurance_image')) {
                    $filename = time() . '.' . $request->insurance_image->extension();
                    $request->insurance_image->storeAs('public/insurance', $filename);
                    $license_details['insurance_image'] = $filename;
                }
            }
            $license = DriverDocument::where('user_id', auth('api')->user()->id);
            if ($license) {
                $license->update($license_details);
                $car = CarDetail::where('user_id', auth('api')->user()->id);
                if ($car) {
                    $car->update($car_details);
                }
            }

            if ($car && $license) {
                $user = User::find(auth('api')->user()->id);
                if ($user) {
                    $user->update([
                        'is_validated' => RESUBMIT_DOCUMENT,
                    ]);
                }
                $reject = RejectDocument::where('user_id', auth('api')->user()->id)->first();
                if ($reject) {
                    $reject->delete();
                }
                return $this->result_message('Documents Uploaded Successfully.');
            } else {
                return $this->result_fail('Something Went Wrong.');
            }
        }
    }
}
