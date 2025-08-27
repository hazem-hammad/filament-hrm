<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Setting;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        // Build query with filters
        $jobsQuery = Job::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $jobsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply experience level filter
        if ($request->filled('experience_level')) {
            $jobsQuery->where('experience_level', $request->experience_level);
        }

        // Apply work mode filter
        if ($request->filled('work_mode')) {
            $jobsQuery->where('work_mode', $request->work_mode);
        }

        // Apply work type filter
        if ($request->filled('work_type')) {
            $jobsQuery->where('work_type', $request->work_type);
        }

        // Execute query and get results
        $jobs = $jobsQuery->orderBy('created_at', 'desc')->get();

        return view('careers.index', [
            'jobs' => $jobs,
        ]);
    }

    public function show($slug)
    {

        $job = Job::where('slug', $slug)
            ->firstOrFail();

        return view('careers.show', [
            'job' => $job,
            'companyColor' => '#e97176',
            'companyName' => "Intcore",
        ]);
    }

    public function apply(Request $request, $slug)
    {
        // Find the job
        $job = Job::where('slug', $slug)->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'experience_years' => 'required|integer|min:0',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'linkedin' => 'nullable|url|max:255',
            'portfolio' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create job application
            $application = JobApplication::create([
                'job' => $job->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'linkedin_profile_url' => $request->linkedin,
                'portfolio_url' => $request->portfolio,
                'years_of_experience' => $request->experience_years,
                'stage' => 1,
                'is_archive' => 0,
                'created_by' => $company->created_by,
            ]);

            if ($request->hasFile('resume')) {
                $application->addMedia($request->file('resume'))->toMediaCollection('resume');
            }

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully! We will review your application and get back to you soon.',
                'application_id' => $application->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
