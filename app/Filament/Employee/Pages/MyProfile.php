<?php

namespace App\Filament\Employee\Pages;

use App\Rules\CurrentPassword;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.employee.pages.my-profile';

    protected static ?string $navigationLabel = 'My Profile';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    
    public $record;

    public function mount(): void
    {
        $this->record = Auth::guard('employee')->user();

        $this->form->fill([
            'name' => $this->record->name,
            'email' => $this->record->email,
            'phone' => $this->record->phone,
            'address' => $this->record->address,
            'date_of_birth' => $this->record->date_of_birth,
            'gender' => $this->record->gender,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Photo')
                    ->description('Update your profile photo')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('profile')
                            ->label('Profile Photo')
                            ->collection('profile')
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth(300)
                            ->imageResizeTargetHeight(300)
                            ->avatar()
                            ->alignCenter()
                            ->helperText('Upload a profile photo (recommended size: 300x300px)')
                            ->model($this->record),
                    ])
                    ->columnSpan(1),

                Forms\Components\Section::make('Personal Information')
                    ->description('Your personal details (read-only)')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Radio::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ])
                            ->disabled()
                            ->dehydrated(false)
                            ->inline(),

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpan(1),

                Forms\Components\Section::make('Change Password')
                    ->description('Optionally update your account password')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password')
                            ->rules([
                                'nullable',
                                'required_with:new_password',
                                new CurrentPassword()
                            ]),

                        Forms\Components\TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->rules([
                                'nullable',
                                'required_with:current_password',
                                Password::min(8)
                                    ->letters()
                                    ->numbers()
                                    ->symbols()
                                    ->uncompromised(),
                            ])
                            ->confirmed(),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->requiredWith('new_password'),
                    ])
                    ->columnSpan(1)
                    ->collapsed(),
            ])
            ->columns(1)
            ->model($this->record)
            ->statePath('data');
    }

    public function updateProfile(): void
    {
        $data = $this->form->getState();

        try {
            $hasPasswordChange = false;

            // Handle password change if provided
            if (!empty($data['new_password'])) {
                $this->record->update([
                    'password' => Hash::make($data['new_password']),
                    'password_set_at' => now(),
                ]);
                $hasPasswordChange = true;
            }

            // Save the form (this will handle media uploads automatically)
            $this->form->model($this->record)->saveRelationships();

            // Show appropriate success message
            if ($hasPasswordChange) {
                Notification::make()
                    ->title('Profile updated successfully')
                    ->body('Your profile photo and password have been updated.')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Profile photo updated successfully')
                    ->success()
                    ->send();
            }

            // Clear password fields after successful update
            $this->data['current_password'] = '';
            $this->data['new_password'] = '';
            $this->data['new_password_confirmation'] = '';
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error updating profile')
                ->body('Please try again or contact support if the problem persists.')
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label('Save Changes')
                ->color('primary')
                ->action('updateProfile'),
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::guard('employee')->check();
    }
}
