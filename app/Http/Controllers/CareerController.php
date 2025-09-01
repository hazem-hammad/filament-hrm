<?php

namespace App\Http\Controllers;

use App\Http\Requests\SecureJobApplicationRequest;
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

    public function apply(SecureJobApplicationRequest $request, $slug)
    {
        // Find the job with custom questions
        $job = Job::where('slug', $slug)->with('customQuestions')->first();

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found'
            ], 404);
        }

        // Get sanitized data from secure form request
        $data = $request->sanitized();
        $customQuestions = $job->customQuestions;

        try {
            // Create job application
            $application = JobApplication::create([
                'job_id' => $job->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'portfolio_url' => $data['portfolio_url'] ?? null,
                'github_url' => $data['github_url'] ?? null,
                'years_of_experience' => $data['years_of_experience'],
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
