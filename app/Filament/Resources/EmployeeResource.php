<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?int $navigationSort = 1;  // Posisikan lebih atas
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'List Employees';
    protected static ?string $navigationGroup = 'Employee Status';
    public static function getLabel(): string
    {
        return 'List Employee'; // Ganti dengan judul yang diinginkan
    }

    /**
     * Menambahkan badge jumlah total employees di sidebar
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) Employee::count(); // Pastikan return dalam bentuk string
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Employee Details')
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make('Employee Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->unique(Employee::class, 'email', ignoreRecord: true) // Hanya berlaku untuk create, saat edit tidak validasi duplikat
                                    ->required(),
                                TextInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->required(),
                                TextInput::make('address')
                                    ->label('Address')
                                    ->required(),
                                Select::make('position')
                                    ->label('Position')
                                    ->options([
                                        'staff' => 'Staff',
                                        'hr' => 'HR',
                                        'manager' => 'Manager',
                                    ])
                                    ->required(),
                            ]),
                    ])

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Employee ID')
                    ->sortable(), // Memastikan ID asli dari database yang ditampilkan
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('position')
                    ->label('Position')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'staff' => 'gray',
                        'hr' => 'info',
                        'manager' => 'success',
                    }),
            ])
            ->filters([
                SelectFilter::make('position')
                    ->label('Filter by Position')
                    ->options([
                        'staff' => 'Staff',
                        'hr' => 'HR',
                        'manager' => 'Manager',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalHeading('Confirm Deletion')
                    ->modalDescription('If you delete this data, everything related to this data will also be deleted.')
                    ->modalButton('Yes, Delete'),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
