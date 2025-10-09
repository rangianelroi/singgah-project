<?php

namespace App\Filament\Resources\ConfiscatedItems\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class CommunicationLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'communications';

    protected static ?string $title = 'Log Komunikasi';

    public function form(Schema $schema): schema
    {
        return $schema
            ->components([
                Select::make('channel')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'email' => 'Email',
                        'phone_call' => 'Telepon',
                        'other' => 'Lainnya',
                    ])
                    ->required()
                    ->label('Media Komunikasi'),

                DateTimePicker::make('sent_at')
                    ->label('Waktu Komunikasi')
                    ->default(now())
                    ->required(),

                Textarea::make('message_summary')
                    ->label('Ringkasan Komunikasi')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Contoh: Penumpang setuju untuk dikirim, meminta info ongkir ke Jakarta.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('channel')
            ->columns([
                TextColumn::make('channel')
                    ->label('Media')
                    ->badge(),
                TextColumn::make('user.name')
                    ->label('Dicatat Oleh'),
                TextColumn::make('message_summary')
                    ->label('Ringkasan')
                    ->limit(50),
                TextColumn::make('sent_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()
                ->label('Buat Log Baru')
                ->mutateFormDataUsing(function (array $data): array {
                    // Secara otomatis mengisi user_id dengan ID pengguna yang sedang login
                    $data['user_id'] = auth()->id();
                    return $data;
                }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}