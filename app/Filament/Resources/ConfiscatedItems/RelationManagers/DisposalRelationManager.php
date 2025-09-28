<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use App\Models\DisposalRecord;

class DisposalRelationManager extends RelationManager
{
    protected static string $relationship = 'disposal';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $userRole = auth()->user()->role;
        return in_array($userRole, ['team_leader_avsec', 'admin', 'department_head_avsec']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('disposal_method')
                    ->options([
                        'destroyed' => 'Dimusnahkan',
                        'handed_to_police' => 'Diserahkan ke Polisi',
                        'other' => 'Lainnya',
                    ])
                    ->required()->label('Metode Pemusnahan'),

                DatePicker::make('disposal_date')
                    ->required()->default(now())->label('Tanggal Pemusnahan'),

                Textarea::make('witnesses')
                    ->label('Saksi (jika ada, pisahkan dengan koma)')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('disposal_method')
            ->columns([
                TextColumn::make('disposal_method')->label('Metode'),
                TextColumn::make('disposal_date')->date('d M Y')->label('Tanggal'),
                TextColumn::make('authorizedBy.name')->label('Ditorisasi Oleh'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                        $data['authorized_by_user_id'] = auth()->id();
                        return $data;
                }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('downloadReport')
                ->label('Cetak PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(fn (DisposalRecord $record) => route('disposal.report.download', $record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => auth()->user()->role === 'department_head_avsec'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
