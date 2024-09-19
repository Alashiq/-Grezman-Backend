<?php

namespace App\Features\Admin\v1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Features\Admin\v1\Resources\AdminResource;
use App\Features\Admin\v1\Models\Admin;
use App\Features\Admin\v1\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{


    // Load Home Statisic
    public function index()
    {

                // First 
                $newStudentsToday = User::notDeleted()
                ->whereDate('created_at', Carbon::today())
                ->get()
                ->count();
    
            // Second 
            $newStudentsLast7Days = User::notDeleted()
                ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()])
                ->get()
                ->count();
    
    
            // Third 
            $newStudentsLast30Days = User::notDeleted()
                ->whereBetween('created_at', [Carbon::now()->subDays(29), Carbon::now()])
                ->get()
                ->count();
    
    
                        // Fourth 
    
    
            $allStudent = User::notDeleted()->get()->count();
    
            $allStudentAndroid = User::notDeleted()->where('platform', 'Android')->get()->count();
    
    
    
            $allStudentIOS = User::notDeleted()->where('platform', 'IOS')->get()->count();
    
    
    
    
    
    
    
    
            return response()->json([
                'success' => true, 'message' => 'ليس لديك الصلاحية للقيام بهذه المهمة', 'data' => [
                    'users' => 20,
                    'roles' => 30,
                    'new_student_today' => $newStudentsToday,
                    'new_student_week' => $newStudentsLast7Days,
                    'new_student_month' => $newStudentsLast30Days,
                    'all_student' => $allStudent,
                    'all_student_android' => $allStudentAndroid,
                    'all_student_ios' => $allStudentIOS,
                ]
            ], 200);

    }
    

    // End of Controller
}
