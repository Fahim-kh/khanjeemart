<?php
// use File;

use App\Models\BranchAsignCity;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

if (!function_exists('p')) {
    function p($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
}

if (!function_exists('get_formatted_date')) {
    function get_formatted_date($data, $formate)
    {
        $formattedDate = date($formate, strtotime($data));
        return $formattedDate;
    }
}

if (!function_exists('getCurrentDate')) {
    function getCurrentDate()
    {
        return date('Y-m-d');
    }
}

if (!function_exists('getSoftwareDate')) {
    function getSoftwareDate()
    {
        return DB::table('company_detail')->whereId(1)->first()->software_date;
    }
}


if (!function_exists('getCurrentTime')) {
    function getCurrentTime()
    {
        return date('h:i A');
    }
}

if (!function_exists('getCurrentDateTime')) {
    function getCurrentDateTime()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('changeDateFormate')) {
    function changeDateFormate($date)
    {
        return $newDateFormate = date('d-m-Y', strtotime($date));
    }
}

if (!function_exists('upload_images')) {
    function upload_images($file, $location)
    {
        if ($file) {
            $filename = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
            $extension = $file->getClientOriginalExtension();
            $file->move($location, $filename);
            $filepath = url($location . '/' . $filename);
            $data['success'] = 1;
            $data['message'] = 'Uploaded Successfully!';
            $data['filepath'] = $filepath;
            $data['filename'] = $filename;
            $data['extension'] = $extension;
        } else {
            $data['success'] = 2;
            $data['message'] = 'File not uploaded.';
        }
        return $data;
    }
}

if (!function_exists('delete_image')) {
    function delete_image($path)
    {
        return File::delete($path);
    }
}

if (!function_exists('current_status')) {
    function current_status($status)
    {
        // if ($status == 0)
        //     return '<button" class="btn btn-danger btn-sm singleStatus"><i class="fa fa-hourglass" style="cursor: default;"> DEACTIVE</i></button">';
        // if ($status == 1)
        //     return '<button class="btn btn-success btn-sm singleStatus" style="cursor: default;">ACTIVE</button>';

         if ($status == 1)
                return '<span class="bg-success-focus text-success-main px-24 py-4 rounded-pill fw-medium text-sm">Active</span>';
         if ($status == 0)
                return '<span class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">Deactive</span>';
        
    }
}

if (!function_exists('image_show')) {
    function image_show($folder, $image)
    {
        if ($image == 'not found')
            return '<img src="{{ url("") }}/assets/dist/img/no-img.png" width="50" height="50" alt="no img found" class="thumbnail">';
        else
            return '<img src=" ' . url('uploads/' . $folder . '/' . $image) . ' " width="50" height="50" alt="' . $image . '" class="thumbnail">';
    }
}

if (!function_exists('checkbox_show')) {
    function checkbox_show($id)
    {
        return '<input type="checkbox" class="form-check-input chk_del"  name="chk_del[]" value="' . $id . '" >';
    }
}
if (!function_exists('table_edit_delete_button')) {
    function table_edit_delete_button($id, $url,$permission)
    {
        $user = \Auth::user();
        $editBtn = '';
        $deleteBtn = '';

        if ($user && isset($user->hasPer($permission)['pedit']) && $user->hasPer($permission)['pedit'] == 1) {
            $editBtn = '<button get_id="' . $id . '" 
                            class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center edit" 
                            title="Edit">
                            <iconify-icon icon="lucide:edit"></iconify-icon>
                        </button>';
        }

        if ($user && isset($user->hasPer($permission)['pdelete']) && $user->hasPer($permission)['pdelete'] == 1) {
            $deleteBtn = '<button get_id="' . $id . '" 
                              id="delete_record" 
                              url="' . $url . '" 
                              class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center delete" 
                              title="Delete">
                              <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                          </button>';
        }

        return $editBtn . $deleteBtn;
    }
}




if (!function_exists('table_delete_button')) {
    function table_delete_button($id, $url)
    {
        return '<button get_id="' . $id . '" id="delete_record" url="' . $url . '" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center delete ">
                 <iconify-icon icon="mingcute:delete-2-line"></iconify-icon></button>';
    }
}

if (!function_exists('table_edit_button')) {
    function table_edit_button($id, $url)
    {
        return '<button get_id="' . $id . '" 
                    class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center edit">
                                                    <iconify-icon icon="lucide:edit"></iconify-icon></button>';
    }
}


if (!function_exists('table_action_dropdown')) {
    function table_action_dropdown($id, $url, $permission)
    {
        $user = \Auth::user();
        $menuItems = '';

        // Purchase Edit
        if ($user && isset($user->hasPer($permission)['pedit']) && $user->hasPer($permission)['pedit'] == 1) {
           $menuItems .= '<li>
                <a class="dropdown-item edit" href="' . url('admin/'.$url . '/purchaseEdit/' . $id) . '">
                    <i class="bi bi-eye me-2"></i> Purchase Edit
                </a>
            </li>';
        }

        // Purchase Return
        //if ($user && isset($user->hasPer($permission)['preturn']) && $user->hasPer($permission)['preturn'] == 1) {

            $menuItems .= '<li>
                <a class="dropdown-item return" href="' . url('admin/'.$url . '/purchaseReturn/' . $id) . '">
                    <i class="bi bi-eye me-2"></i> Purchase Return
                </a>
            </li>';
        //}
        // Purchase View
        //if ($user && isset($user->hasPer($permission)['pview']) && $user->hasPer($permission)['pview'] == 1) {
            $menuItems .= '<li>
                <a class="dropdown-item view" href="' . url('admin/'.$url . '/view/detail/' . $id) . '">
                    <i class="bi bi-eye me-2"></i> Purchase View
                </a>
            </li>';
        //}

        // PDF Download
        $menuItems .= '<li>
            <a class="dropdown-item pdf-download" href="' . url($url . '/' . $id . '/pdf') . '" target="_blank">
                <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF Download
            </a>
        </li>';

        // Purchase Delete
        if ($user && isset($user->hasPer($permission)['pdelete']) && $user->hasPer($permission)['pdelete'] == 1) {
            $menuItems .= '<li>
                <a class="dropdown-item text-danger pdelete" href="javascript:void(0);" get_id="' . $id . '" url="' . $url . '">
                    <i class="bi bi-trash me-2"></i> Purchase Delete
                </a>
            </li>';
        }

        // If no menu items, return empty
        if (empty($menuItems)) {
            return '';
        }

        // Bootstrap 5 dropdown
        return '
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                ' . $menuItems . '
            </ul>
        </div>';
    }
}




if (!function_exists('row_color_change')) {
    function row_color_change($status)
    {
        if ($status == 6 || $status == 5 || $status == 9) {
            //html += '<tr bgcolor="#FF0000">';
            return 'color1';
        } else if ($status == 1) {
            //html += '<tr bgcolor="#FF9900">';
            return 'color2';
        } else if ($status == 2) {
            //html += '<tr bgcolor="#FFFF99">';
            return 'color3';
        } else if ($status == 4) {
            //html += '<tr bgcolor="#fff">';
            return 'color4';
        } else if ($status == 3 || $status == 7) {
            //html += '<tr bgcolor="green">';
            return 'color5';
        } else if ($status == 10 || $status == 12 || $status == 11 || $status == 8 || $status == 13) {
            //html += '<tr bgcolor="#993333">';
            return 'color6';
        } else if ($status == 15) {
            //html += '<tr bgcolor="#CCCCCC">';
            return 'color7';
        } else {
            //html += '<tr bgcolor="#0099FF">';
            return 'color8';
        }
    }
}

if (!function_exists('destroy')) {
    function destroy(Model $model, $id)
    {
        try {
            $table = $model::find($id);
            $table->delete();
            return response()->json(['success' => 'data is successfully deleted'], 200);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}

if (!function_exists('DeleteAll')) {
    function DeleteAll(Request $request, Model $model)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'check_all' => 'required'
            ]);
            if (!$validator->passes()) {
                return response()->json(['error' => $validator->errors()->all()]);
            }

            foreach ($request->check_all as $value) {
                $model::where('id', $value)->delete();
            }
            return response()->json(['success' => 'data is successfully deleted'], 200);
        }
        return 'false';
    }
}


if (!function_exists('attribute_select_type_selected')) {
    function attribute_select_type_selected($type_id)
    {
        switch ($type_id) {
            case "1":
                return "Text";
                break;
            case "2":
                return "TextArea";
                break;
            case "3":
                return "Date";
                break;
            case "4":
                return "Number";
                break;
            case "5":
                return "Select";
                break;
            case "6":
                return "CheckBox";
                break;
            default:
                return "Select";
        }
    }
}
if (!function_exists('branch_filter')) {
    function branch_filter($branch_filter) {
        $branch_city_filter = "";
        if ($branch_filter != null && $branch_filter != 0) {
            $city_list = BranchAsignCity::wherein('branch_id', [$branch_filter])->select('city_id')->get();            
            foreach ($city_list as $city) {
                if ($branch_city_filter == "")
                    $branch_city_filter = $branch_city_filter."".$city->city_id."";
                else
                    $branch_city_filter = $branch_city_filter.",".$city->city_id."";                                 
            }
        }
        
        return $branch_city_filter;
    }
}

if(!function_exists('MyRound'))
{
    function MyRound($number,$precision)
    {
        return round($number, $precision);
    }
}

// if (!function_exists('isAdmin')) {
//     function isAdmin()
//     {
//         if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
//             return true;
//         }
//         return false;
//     }
// }

// if (!function_exists('isSeller')) {
//     function isSeller()
//     {
//         if (Auth::check() && Auth::user()->user_type == 'seller') {
//             return true;
//         }
//         return false;
//     }
// }

// if (!function_exists('isCustomer')) {
//     function isCustomer()
//     {
//         if (Auth::check() && Auth::user()->user_type == 'customer') {
//             return true;
//         }
//         return false;
//     }
// }