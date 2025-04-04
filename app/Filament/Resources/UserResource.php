<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 5; //posisi urutan menu sidebar
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'List Users';

    protected static ?string $navigationGroup = 'User Management';
    public static function getLabel(): string
    {
        return 'List User'; // Ganti dengan judul yang diinginkan
    }
    public static function getNavigationBadge(): ?string
    {
        $count = User::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Details')
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->afterStateUpdated(
                                        fn($state, callable $set) =>
                                        $set('name', trim($state))
                                    )
                                    ->validationMessages([
                                        'required' => 'The name field is required!',
                                        'max' => 'The name must not exceed 255 characters!',
                                    ]),

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(User::class, 'email', ignoreRecord: true)
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'The email field is required!',
                                        'email' => 'Please enter a valid email address!',
                                        'unique' => 'This email is already in use!',
                                    ]),

                                Select::make('role')
                                    ->label('Role')
                                    ->options([
                                        'admin' => 'Admin',
                                        'hr' => 'HR',
                                        'manager' => 'Manager',
                                        'staff' => 'Staff',
                                    ])
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'Please select a role!',
                                    ]),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->nullable()
                                    ->minLength(8)
                                    ->rule(Password::min(8))
                                    ->dehydrateStateUsing(fn($state) => !empty($state) ? bcrypt($state) : null)
                                    ->afterStateHydrated(fn($state, callable $set) => $set('password', ''))
                                    ->dehydrated(fn($state) => !empty($state))
                                    ->validationMessages([
                                        'min' => 'Password must be at least 8 characters!',
                                    ]),
                            ]),
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(), // Menggunakan built-in method untuk nomor urut

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'admin' => 'danger',
                        'hr' => 'info',
                        'manager' => 'success',
                        'staff' => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Sort Data by Role')
                    ->options([
                        'admin' => 'Admin',
                        'hr' => 'HR',
                        'manager' => 'Manager',
                        'staff' => 'Staff',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
