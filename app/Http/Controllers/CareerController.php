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
        // Build query with filters - only show active jobs
        $jobsQuery = Job::query()->active();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $jobsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('long_description', 'like', "%{$search}%");
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
            ->active()
            ->with('customQuestions')
            ->firstOrFail();

        return view('careers.show', [
            'job' => $job,
            'companyColor' => get_setting('primary_color', '#e97176'),
            'companyName' => get_setting('company_name', 'Company'),
        ]);
    }

    public function apply(Request $request, $slug)
    {
        // Find the job with custom questions
        $job = Job::where('slug', $slug)->with('customQuestions')->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Build validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'years_of_experience' => 'required|integer|min:0',
            'resume' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png,gif,txt,csv|max:5120', // 5MB max
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
        ];

        // Add validation for custom questions if they exist
        $customQuestions = $job->customQuestions;
        if ($customQuestions) {
            foreach ($customQuestions as $question) {
                $fieldName = "custom_questions.{$question->id}";
                $fieldRules = [];
                
                if ($question->is_required) {
                    $fieldRules[] = 'required';
                }

                switch ($question->type) {
                    case 'text_field':
                    case 'textarea':
                        $fieldRules[] = 'string|max:2000';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    case 'file_upload':
                        $fieldRules[] = 'file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png,gif,txt,csv|max:5120'; // 5MB max
                        break;
                    case 'toggle':
                        $fieldRules[] = 'boolean';
                        break;
                    case 'multi_select':
                        $fieldRules[] = 'array';
                        break;
                }

                if (!empty($fieldRules)) {
                    $rules[$fieldName] = implode('|', $fieldRules);
                }
            }
        }

        $validator = Validator::make($request->all(), $rules);

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
                'job_id' => $job->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'linkedin_url' => $request->linkedin_url,
                'portfolio_url' => $request->portfolio_url,
                'github_url' => $request->github_url,
                'years_of_experience' => $request->years_of_experience,
                'status' => true,
            ]);

            // Handle resume upload
            if ($request->hasFile('resume')) {
                $application->addMediaFromRequest('resume')->toMediaCollection('resume');
            }

            // Handle custom questions
            if ($request->has('custom_questions')) {
                foreach ($request->custom_questions as $questionId => $answer) {
                    $question = $customQuestions->find($questionId);
                    
                    if ($question && $question->type === 'file_upload' && $request->hasFile("custom_questions.{$questionId}")) {
                        // Handle file upload for custom questions
                        $file = $request->file("custom_questions.{$questionId}");
                        $media = $application->addMedia($file)->toMediaCollection('custom_questions');
                        
                        $application->answers()->create([
                            'job_application_id' => $application->id,
                            'custom_question_id' => $questionId,
                            'answer' => $media->id, // Store media ID for file uploads
                        ]);
                    } else {
                        // Handle other question types
                        $application->answers()->create([
                            'job_application_id' => $application->id,
                            'custom_question_id' => $questionId,
                            'answer' => is_array($answer) ? json_encode($answer) : $answer,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully! We will review your application and get back to you soon.',
                'application_id' => $application->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Job application error: ' . $e->getMessage(), [
                'job_id' => $job->id,
                'user_email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
