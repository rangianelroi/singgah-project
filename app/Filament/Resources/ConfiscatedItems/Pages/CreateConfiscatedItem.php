<?php
namespace App\Filament\Resources\ConfiscatedItems\Pages;

use App\Filament\Resources\ConfiscatedItems\ConfiscatedItemResource;
use App\Models\Passenger;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateConfiscatedItem extends CreateRecord
{
    protected static string $resource = ConfiscatedItemResource::class;

    // GANTI method 'configure' menjadi 'form' dan sesuaikan return type-nya
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Data Penumpang')
                        ->schema([
                            TextInput::make('passenger_full_name')->required()->label('Nama Lengkap Penumpang'),
                            TextInput::make('passenger_identity_number')->label('No. Identitas (KTP/Paspor)'),
                            TextInput::make('passenger_phone_number')->tel()->label('No. Telepon'),
                            TextInput::make('passenger_email')->email()->label('Email'),
                            FileUpload::make('passenger_identity_image_path')
                                ->image()->disk('local')->directory('passenger-identities')
                                ->label('Foto Identitas')
                                ->required(),
                            FileUpload::make('passenger_boardingpass_image_path')
                                ->image()->disk('local')->directory('passenger-boardingpasses')
                                ->label('Foto Boarding Pass')
                                ->required(),
                        ]),

                    Step::make('Data Penerbangan & Barang')
                        ->schema([
                            Select::make('flight_id')
                                ->relationship('flight')
                                ->getOptionLabelFromRecordUsing(fn ($record) =>
                                    "{$record->airline->code} {$record->flight_number} - {$record->destination->iata_code}"
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->label('Penerbangan'),
                            TextInput::make('item_name')
                                ->required()
                                ->columnSpanFull()
                                ->label('Nama Barang'),
                            FileUpload::make('item_image_path')
                                ->image()
                                ->label('Gambar Barang')
                                ->disk('local')
                                ->directory('confiscated_items')
                                ->columnSpanFull(),
                            Select::make('category')
                                ->options([
                                    'dangerous_goods' => 'Dangerous goods',
                                    'prohibited_items' => 'Prohibited items',
                                    'security_items' => 'Security items',
                                    'other' => 'Other',
                                ])
                                ->required()
                                ->label('Kategori Barang'),
                            DateTimePicker::make('confiscation_date')
                                ->label('Tanggal Pencatatan')
                                ->default(now())
                                ->required(),
                            TextInput::make('item_quantity')
                                ->required()
                                ->numeric()
                                ->default(1)
                                ->label('Jumlah Barang'),
                            TextInput::make('item_unit')
                                ->required()
                                ->default('unit')
                                ->label('Satuan Barang'),
                            Textarea::make('notes')
                                ->label('Catatan Tambahan')
                                ->default(null)
                                ->columnSpanFull(),
                        ]),
                ])->columnSpanFull(),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Cari atau buat penumpang baru
        $passenger = Passenger::firstOrCreate(
            ['identity_number' => $data['passenger_identity_number']], // Kunci pencarian
            [ // Data jika penumpang baru dibuat
                'full_name' => $data['passenger_full_name'],
                'phone_number' => $data['passenger_phone_number'],
                'email' => $data['passenger_email'],
                'identity_image_path' => $data['passenger_identity_image_path'],
                'boardingpass_image_path' => $data['passenger_boardingpass_image_path'],
            ]
        );

        // Buat data barang sitaan
        $confiscatedItemData = [
            'passenger_id' => $passenger->id,
            'flight_id' => $data['flight_id'],
            'recorded_by_user_id' => auth()->id(),
            'item_name' => $data['item_name'],
            'item_image_path' => $data['item_image_path'],
            'category' => $data['category'],
            'confiscation_date' => $data['confiscation_date'],
            'item_quantity' => $data['item_quantity'],
            'item_unit' => $data['item_unit'],
            'notes' => $data['notes'],
        ];

        return static::getModel()::create($confiscatedItemData);
    }


    protected function afterCreate(): void
    {
        $this->getRecord()->statusLogs()->create([
            'status' => 'RECORDED',
            'user_id' => auth()->id(),
            'notes' => 'Barang dicatat oleh petugas.',
        ]);
    }
} 