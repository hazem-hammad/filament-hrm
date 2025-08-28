<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use App\Models\JobApplicationAnswer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobApplication extends EditRecord
{
    protected static string $resource = JobApplicationResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load custom question answers
        $answers = $this->record->answers()->with('customQuestion')->get();

        foreach ($answers as $answer) {
            $questionKey = "question_{$answer->custom_question_id}";

            // Handle JSON values (multi-select)
            if ($answer->customQuestion->type === 'multi_select') {
                $data[$questionKey] = json_decode($answer->answer, true) ?: [];
            } else {
                $data[$questionKey] = $answer->answer;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove custom question fields from data before saving the record
        // They will be handled separately in afterSave
        foreach (array_keys($data) as $key) {
            if (str_starts_with($key, 'question_')) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->saveCustomQuestionAnswers();
    }

    protected function saveCustomQuestionAnswers(): void
    {
        $data = $this->form->getState();
        $jobApplication = $this->record;

        // Delete existing answers
        JobApplicationAnswer::where('job_application_id', $jobApplication->id)->delete();

        // Save new answers
        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questionId = str_replace('question_', '', $key);

                if ($value !== null && $value !== '' && $value !== []) {
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

    protected function getHeaderActions(): array
    {
        return [];
    }
}
