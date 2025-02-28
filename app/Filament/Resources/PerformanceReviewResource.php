<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerformanceReviewResource\Pages;
use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\Contract; // Pastikan menambahkan model Contract
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class PerformanceReviewResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;
    protected static ?int $navigationSort = 3;  // Posisikan di bawah Employee
    protected static ?string $navigationGroup = 'Employee Status';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    public static function getLabel(): string
    {
        return 'Performance Reviews'; // Ganti dengan judul yang diinginkan
    }
    public static function getNavigationBadge(): ?string
    {
        $count = Employee::whereDoesntHave('performancereviews')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Tabs::make('performance_review')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('Evaluation Score')
                                ->icon('heroicon-o-chart-bar')
                                ->schema([
                                    Select::make('employee_id')
                                        ->relationship('employee', 'name')
                                        ->label('Employee Name')
                                        ->searchable()
                                        ->required()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $employee = \App\Models\Employee::find($state);
                                            $set('employee_number', $employee?->id);
                                        }),

                                    TextInput::make('employee_number')
                                        ->label('Employee ID')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->live(),

                                    DatePicker::make('review_date')
                                        ->label('Review Date')
                                        ->required(),

                                    TextInput::make('attendance')
                                        ->label('Attendance')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(
                                            fn($state, callable $set, callable $get) =>
                                            $set('total_score', (int) $state + (int) $get('productivity') + (int) $get('discipline'))
                                        ),

                                    TextInput::make('productivity')
                                        ->label('Productivity')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(
                                            fn($state, callable $set, callable $get) =>
                                            $set('total_score', (int) $get('attendance') + (int) $state + (int) $get('discipline'))
                                        ),

                                    TextInput::make('discipline')
                                        ->label('Discipline')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(
                                            fn($state, callable $set, callable $get) =>
                                            $set('total_score', (int) $get('attendance') + (int) $get('productivity') + (int) $state)
                                        ),

                                    // ✅ Total Score Sekarang Bisa Dikirim ke Database
                                    TextInput::make('total_score')
                                        ->label('Total Score')
                                        ->numeric()
                                        ->disabled()
                                        ->live()
                                        ->required() // Pastikan total_score wajib diisi
                                        ->dehydrated() // 🔹 Ini penting! Agar dikirim ke backend
                                        ->afterStateUpdated(
                                            fn($state, callable $set, callable $get) =>
                                            $set('total_score', (int) $get('attendance') + (int) $get('productivity') + (int) $get('discipline'))
                                        ),
                                ]),

                            Forms\Components\Tabs\Tab::make('Evaluation Notes')
                                ->icon('heroicon-o-document-text')
                                ->schema([
                                    RichEditor::make('evaluation')
                                        ->label('Evaluation')
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
                                        ->columnSpan('full'),
                                ]),
                        ]),
                ]),
        ]);
    }
    public static function beforeSave($record, $data)
    {
        $record->total_score = $data['attendance'] + $data['productivity'] + $data['discipline'];
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.id')
                    ->label('Employee ID')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

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

                TextColumn::make('contract.contract_status')
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

                TextColumn::make('total_score')
                    ->label('Total Score')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                TextColumn::make('review_date')
                    ->label('Review Date')
                    ->date()
                    ->toggleable()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('employee.id', 'asc'); // Urutkan berdasarkan employee ID secara naik (1, 2, 3, ...)
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformanceReviews::route('/'),
            'create' => Pages\CreatePerformanceReview::route('/create'),
            'edit' => Pages\EditPerformanceReview::route('/{record}/edit'),
        ];
    }
}
