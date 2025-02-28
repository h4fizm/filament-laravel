<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification; // Tambahkan ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilePage extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'User Profile';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'User Management';

    protected static string $view = 'filament.pages.profile-page';

    public ?array $data = [];

    public function mount()
    {
        $this->form->fill(Auth::user()->only(['name', 'email', 'role']));
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Profile Information')
                ->description("Update your account's profile information and email address.")
                ->schema([
                    TextInput::make('name')->label('Name')->required(),
                    TextInput::make('email')->label('Email')->email(),
                    Select::make('role')->label('Role')->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                        'editor' => 'Editor',
                    ])->disabled(),
                ])
                ->columns(2),

            Section::make('Update Password')
                ->description("Change your password securely.")
                ->schema([
                    TextInput::make('password')->label('New Password')->password()->nullable(),
                ])
                ->columns(2),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->model(Auth::user());
    }

    public function save()
    {
        $user = Auth::user();
        $data = $this->form->getState();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        // 🔔 Tampilkan notifikasi berhasil
        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }
}
