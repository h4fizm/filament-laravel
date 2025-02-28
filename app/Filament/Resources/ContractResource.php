<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Models\Contract;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;
    protected static ?int $navigationSort = 2;  // Posisikan di bawah Employee
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationLabel = 'List Employee Contracts';
    protected static ?string $navigationGroup = 'Employee Status';

    public static function getLabel(): string
    {
        return 'Employee Contracts '; // Ganti dengan judul yang diinginkan
    }
    public static function getNavigationBadge(): ?string
    {
        $count = Employee::whereDoesntHave('contract')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Card::make() // 🔹 Semua dalam satu Card
                ->schema([
                    Forms\Components\Tabs::make('contract_details') // 🔹 Menggunakan Tabs
                        ->tabs([
                            // ✅ TAB 1: INFORMASI KONTRAK 📄
                            Forms\Components\Tabs\Tab::make('Contract Information')
                                ->icon('heroicon-o-clipboard-document') // Ikon dokumen 📄
                                ->schema([
                                    Select::make('employee_id')
                                        ->relationship('employee', 'name')
                                        ->label('Employee Name')
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state) {
                                                $employee = Employee::find($state);
                                                if ($employee) {
                                                    $set('employee_number', $employee->id);
                                                } else {
                                                    $set('employee_number', null); // Hindari ID yang tidak valid
                                                }
                                            }
                                        }),
                                    TextInput::make('employee_number')
                                        ->label('Employee ID')
                                        ->disabled()
                                        ->dehydrated(false) // Agar tidak dikirim ke backend
                                        ->live(),

                                    DatePicker::make('start_date')
                                        ->label('Start Date')
                                        ->required(),

                                    DatePicker::make('end_date')
                                        ->label('End Date')
                                        ->required(),

                                    Select::make('contract_status')
                                        ->label('Contract Status')
                                        ->options([
                                            'active' => 'Active',
                                            'expiring_soon' => 'Expiring Soon',
                                            'not_renewed' => 'Not Renewed',
                                        ])
                                        ->required(),
                                ]),

                            // ✅ TAB 2: DESKRIPSI 📝
                            Forms\Components\Tabs\Tab::make('Description')
                                ->icon('heroicon-o-document-text') // Ikon dokumen 📝
                                ->schema([
                                    RichEditor::make('description')
                                        ->label('Description')
                                        ->required()
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'underline',
                                            'strike',
                                            'h2',
                                            'h3',
                                            'bulletList',
                                            'orderedList',
                                            'link',
                                            'codeBlock',
                                            'blockquote'
                                        ])
                                        ->columnSpan('full'), // Lebar penuh
                                ]),
                        ]),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.id')
                    ->label('Employee ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('employee.name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('employee.position')
                    ->label('Position')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'staff' => 'gray',
                        'hr' => 'info',
                        'manager' => 'success',
                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('contract_status')
                    ->label('Contract Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'active' => 'success',
                        'expiring_soon' => 'warning',
                        'not_renewed' => 'danger',
                    })
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('contract_status')
                    ->label('Filter by Contract Status')
                    ->options([
                        'active' => 'Active',
                        'expiring_soon' => 'Expiring Soon',
                        'not_renewed' => 'Not Renewed',
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
            'index' => Pages\ListContracts::route('/'),
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
