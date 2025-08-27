<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Models\JobApplicationAnswer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJobApplication extends CreateRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove custom question fields from data before creating the record
        // They will be handled separately in afterCreate
        foreach (array_keys($data) as $key) {
            if (str_starts_with($key, 'question_')) {
                unset($data[$key]);
            }
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->saveCustomQuestionAnswers();
    }

    protected function saveCustomQuestionAnswers(): void
    {
        $data = $this->form->getState();
        $jobApplication = $this->record;

        // Process custom question answers
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questionId = str_replace('question_', '', $key);
                
                if ($value !== null && $value !== '') {
                    // Handle array values (multi-select)
                    if (is_array($value)) {
                        $value = json_encode($value);
                    }
                    
                    JobApplicationAnswer::create([
                        'job_application_id' => $jobApplication->id,
                        'custom_question_id' => $questionId,
                        'answer' => $value,
                    ]);
                }
            }
        }
    }
}
