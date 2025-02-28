<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\PerformanceReview;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Group;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;

class ReportResource extends Resource
{
    protected static ?string $model = PerformanceReview::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Export Report File';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Employee Status';

    public static function getLabel(): string
    {
        return 'Export Employee Report File';
    }
    public static function getNavigationBadge(): ?string
    {
        $count = PerformanceReview::count();
        return $count > 0 ? (string) $count : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.id')->label('Employee ID')->sortable()->toggleable()->searchable(),
                TextColumn::make('employee.name')->label('Name')->sortable()->searchable()->toggleable(),
                BadgeColumn::make('employee.position')->label('Position')->colors([
                    'gray' => 'staff',
                    'info' => 'hr',
                    'success' => 'manager',
                ])->sortable()->toggleable(),
                BadgeColumn::make('employee.contract.contract_status')->label('Contract Status')->colors([
                    'success' => 'active',
                    'warning' => 'expiring_soon',
                    'danger' => 'not_renewed',
                ])->sortable()->searchable()->toggleable()->formatStateUsing(fn($state) => $state ? ucfirst(str_replace('_', ' ', $state)) : 'Unknown'),
                TextColumn::make('total_score')->label('Total Score')->sortable()->toggleable()->searchable(),
                TextColumn::make('review_date')->label('Review Date')->date('d M Y')->sortable()->toggleable(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->modalHeading('Performance Review Preview')
                    ->modalWidth('3xl')
                    ->modalSubmitAction(false)
                    ->infolist(
                        fn($record) => Infolist::make()->record($record)
                            ->schema([
                                Group::make([
                                    // Employee Information
                                    Section::make('Employee Information')->schema([
                                        TextEntry::make('employee.id')->label('Employee ID'),
                                        TextEntry::make('employee.name')->label('Name'),
                                        TextEntry::make('employee.email')->label('Email'),
                                        TextEntry::make('employee.position')
                                            ->label('Position')
                                            ->formatStateUsing(fn($state) => ucfirst($state)),
                                    ]),

                                    // Contract Details
                                    Section::make('Contract Details')->schema([
                                        TextEntry::make('employee.contract.start_date')->label('Start Date')->default('-'),
                                        TextEntry::make('employee.contract.end_date')->label('End Date')->default('-'),
                                    ]),

                                    // Performance Review
                                    Section::make('Performance Review')->schema([
                                        TextEntry::make('attendance')->label('Attendance'),
                                        TextEntry::make('productivity')->label('Productivity'),
                                        TextEntry::make('discipline')->label('Discipline'),
                                        TextEntry::make('total_score')->label('Total Score'),
                                        TextEntry::make('employee.contract.contract_status')
                                            ->label('Contract Status')
                                            ->formatStateUsing(fn($state) => ucfirst($state ?? '-')),
                                        TextEntry::make('evaluation')
                                            ->label('Evaluation Notes')
                                            ->formatStateUsing(fn($state) => $state) // Langsung menampilkan HTML yang sudah benar
                                            ->html(), // Penting agar HTML tidak di-escape
                                    ]),
                                ])
                            ])
                    ),


                Action::make('export_pdf')
                    ->label('Export PDF')
                    ->url(fn(PerformanceReview $record) => route('reports.exportPdf', $record->id))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('employee.id', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
        ];
    }
}
