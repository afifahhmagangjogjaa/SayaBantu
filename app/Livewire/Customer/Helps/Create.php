<?php

namespace App\Livewire\Customer\Helps;

use App\Models\City;
use App\Models\Help;
use App\Models\PartnerActivity;
use App\Models\UserBalance;
use App\Models\BalanceTransaction;
use App\Models\AppSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class Create extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $equipment_provided = '';
    public $amount = '';
    public $category_id = '';
    public $city_id = '';
    public $cityQuery = '';
    public $searchResults = [];
    // Req tables selectors
    public $req_province_id = '';
    public $req_regency_id = '';
    public $req_district_id = '';
    public $req_provinces = [];
    public $req_regencies = [];
    public $req_districts = [];
    public $location = '';
    public $full_address = '';
    public $latitude = null;
    public $longitude = null;
    // Scheduling
    public $help_type = 'scheduled'; // 'urgent' or 'scheduled'
    public $auto_cancel_minutes = 30; // Batas waktu tunggu auto-cancel untuk bantuan urgent (menit)
    public $confirmAutoCancelMinutes = 30;
    public $scheduled_date = null; // YYYY-MM-DD
    public $scheduled_time = null; // HH:MM
    public $timezoneLabel = 'WIB';
    public $timezoneIana = 'Asia/Jakarta';
    public $photo;
    public $showInsufficientModal = false;
    public $insufficientMessage = '';
    public $topupAmount = 10000;
    public $topupAdminFee = 0;
    public $topupTotalTransfer = 10000;
    public $topupDeficit = 0;
    public $topupMethod = 'qris';
    public $topupReceipt;
    public $availableBanks = [];
    public $qrisEnabled = true;
    public $showConfirmModal = false;
    public $showSuccessModal = false;
    public $hasActivePendingTopup = false;
    public $activePendingTopup = null;
    public $confirmAmount = 0;
    public $confirmAdminFee = 0;
    public $confirmCustomerFee = 0;
    public $confirmCustomerFeePercent = 10;
    public $confirmTotal = 0;
    public $currentBalance = 0;
    public $confirmScheduled = null;
    public $confirmHelpType = 'scheduled';
    public $isProfileComplete = true;
    public $isKtpVerified = true;
    public $missingProfileFields = [];
    public $hasReachedHelpLimit = false;
    public $activeHelpsCount = 0;
    public $maxHelpsLimit = 2;
    public $minNominal = 10000;
    public $maxNominal = 10000000;

    protected $listeners = [
        'citySelected' => 'setCityId',
        'topupCompleted' => 'onTopupCompleted',
    ];

    public function mount()
    {
        $this->minNominal = (int) AppSetting::get('min_help_nominal', 10000);
        $this->maxNominal = (int) AppSetting::get('max_help_nominal', 10000000);

        $user = auth()->user();
        if ($user && !$user->isProfileComplete()) {
            $this->isProfileComplete = false;
            $this->missingProfileFields = array_values($user->getMissingProfileFields());
            return;
        }

        if ($user && !$user->verified) {
            $this->isKtpVerified = false;
            return;
        }

        if ($user) {
            $this->activeHelpsCount = $user->getActiveCustomerHelpsCount();
            $this->maxHelpsLimit = $user->getMaxActiveCustomerHelpsLimit();
            if (!$user->canCreateMoreHelps()) {
                $this->hasReachedHelpLimit = true;
                return;
            }
        }

        if ($user && $user->city_id) {
            $this->city_id = (string) $user->city_id;
            $city = City::find($user->city_id);
            if ($city) {
                $zone = $this->computeTimezoneLabelFromCity($city);
                $this->timezoneLabel = $zone;
                $this->timezoneIana = $this->ianaForZone($zone);
            }
        }

        if (Schema::hasTable('req_provinces')) {
            $this->req_provinces = \Illuminate\Support\Facades\DB::table('req_provinces')->orderBy('province')->get()->toArray();
        } else {
            $this->req_provinces = [];
        }

        $this->loadPaymentSettings();
        $this->checkPendingTopup();
    }

    public function updatedCityId($value)
    {
        if (empty($value)) {
            return;
        }

        $city = City::find($value);
        if (! $city) {
            return;
        }

        $zone = $this->computeTimezoneLabelFromCity($city);
        $iana = $this->ianaForZone($zone);
        $this->timezoneLabel = $zone;
        $this->timezoneIana = $iana;
        $this->dispatch('help:timezone-changed', zone: $zone, iana: $iana);
    }

    public function updatedAmount($value)
    {
        $minNominal = $this->help_type === 'urgent'
            ? (float) AppSetting::get('default_urgent_nominal', 50000)
            : (float) AppSetting::get('min_help_nominal', 10000);
        $this->minNominal = (int) $minNominal;
        $maxNominal = (float) AppSetting::get('max_help_nominal', 10000000);
        $this->maxNominal = (int) $maxNominal;

        if ($value === '' || $value === null) {
            $this->addError('amount', 'Nominal wajib diisi.');
            return;
        }

        if (is_numeric($value)) {
            $num = (float) $value;
            if ($num > $this->maxNominal) {
                $this->amount = $this->maxNominal;
                $this->addError('amount', 'Nominal maksimal Rp ' . number_format($this->maxNominal, 0, ',', '.'));
            } elseif ($num < $this->minNominal) {
                $msg = $this->help_type === 'urgent'
                    ? 'Nominal bantuan mendesak (urgent) minimal Rp ' . number_format($this->minNominal, 0, ',', '.')
                    : 'Nominal minimal Rp ' . number_format($this->minNominal, 0, ',', '.');
                $this->addError('amount', $msg);
            } else {
                $this->resetErrorBag('amount');
            }
        }
    }

    public function updatedHelpType($value)
    {
        $tz = $this->timezoneIana ?: 'Asia/Jakarta';
        $now = Carbon::now($tz);

        if ($value === 'urgent') {
            $urgentMin = (int) AppSetting::get('default_urgent_nominal', 50000);
            $this->minNominal = $urgentMin;

            // Default dari setting jika kosong
            if (empty($this->amount)) {
                $this->amount = $urgentMin;
            }

            // Tanggal otomatis diset ke hari ini
            $this->scheduled_date = $now->format('Y-m-d');

            // Jam langsung diset ke 15 menit setelah waktu sekarang
            $this->scheduled_time = $now->copy()->addMinutes(15)->format('H:i');
            $this->auto_cancel_minutes = $this->auto_cancel_minutes ?: 30;

            if (!empty($this->amount) && (float) $this->amount < $urgentMin) {
                $this->addError('amount', 'Nominal minimal untuk bantuan urgent adalah Rp ' . number_format($urgentMin, 0, ',', '.'));
            } else {
                $this->resetErrorBag(['scheduled_date', 'scheduled_time', 'amount', 'auto_cancel_minutes']);
            }
        } else {
            $this->minNominal = (int) AppSetting::get('min_help_nominal', 10000);

            // Bersihkan kembali tanggal, jam, dan nominal default jika beralih ke terjadwal
            $this->scheduled_date = null;
            $this->scheduled_time = null;

            $urgentMin = (int) AppSetting::get('default_urgent_nominal', 50000);
            if ((float) $this->amount === (float) $urgentMin) {
                $this->amount = '';
            }

            if (!empty($this->amount) && (float) $this->amount < $this->minNominal) {
                $this->addError('amount', 'Nominal minimal Rp ' . number_format($this->minNominal, 0, ',', '.'));
            } else {
                $this->resetErrorBag(['scheduled_date', 'scheduled_time', 'amount', 'auto_cancel_minutes']);
            }
        }
    }

    public function updatedReqProvinceId($value)
    {
        if (! Schema::hasTable('req_regencies') || empty($value)) {
            $this->req_regencies = [];
            $this->req_regency_id = '';
            $this->req_districts = [];
            $this->req_district_id = '';
            return;
        }
        $this->req_regencies = \Illuminate\Support\Facades\DB::table('req_regencies')
            ->where('province_id', $value)
            ->orderBy('regency')
            ->get()
            ->toArray();
        $this->req_regency_id = '';
        $this->req_districts = [];
        $this->req_district_id = '';
    }

    public function updatedReqRegencyId($value)
    {
        if (! Schema::hasTable('req_districts') || empty($value)) {
            $this->req_districts = [];
            $this->req_district_id = '';
            return;
        }
        $this->req_districts = \Illuminate\Support\Facades\DB::table('req_districts')
            ->where('regency_id', $value)
            ->orderBy('district')
            ->get()
            ->toArray();
        $this->req_district_id = '';
    }

    public function selectReqDistrict($districtId)
    {
        if (! Schema::hasTable('req_districts') || ! Schema::hasTable('req_regencies') || ! Schema::hasTable('req_provinces')) {
            return;
        }

        $row = \Illuminate\Support\Facades\DB::table('req_districts')
            ->join('req_regencies', 'req_districts.regency_id', '=', 'req_regencies.id')
            ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
            ->where('req_districts.id', $districtId)
            ->select('req_districts.id as district_id', 'req_districts.district', 'req_regencies.id as regency_id', 'req_regencies.regency', 'req_provinces.province')
            ->first();

        if (! $row) {
            return;
        }

        $regencyCode = 'reqr-' . $row->regency_id;
        $city = City::where('code', $regencyCode)->first();
        if (! $city) {
            $city = City::create([
                'name' => $row->regency,
                'province' => $row->province,
                'type' => null,
                'is_active' => true,
                'code' => $regencyCode,
            ]);
        }

        $this->city_id = $city->id;
        $this->cityQuery = $row->district . ', ' . $row->regency . ', ' . $row->province;
        $this->searchResults = [];

        $zone = $this->computeTimezoneLabelFromCity($city);
        $iana = $this->ianaForZone($zone);
        $this->timezoneLabel = $zone;
        $this->timezoneIana = $iana;
        $this->dispatch('help:timezone-changed', zone: $zone, iana: $iana);

        $this->req_district_id = $row->district_id;
        $reg = \Illuminate\Support\Facades\DB::table('req_regencies')->where('regency', $row->regency)->first();
        if ($reg) {
            $this->req_regency_id = $reg->id;
            $this->req_regencies = \Illuminate\Support\Facades\DB::table('req_regencies')->where('province_id', $reg->province_id)->orderBy('regency')->get()->toArray();
            $this->req_province_id = $reg->province_id;
            $this->req_provinces = \Illuminate\Support\Facades\DB::table('req_provinces')->orderBy('province')->get()->toArray();
            $this->req_districts = \Illuminate\Support\Facades\DB::table('req_districts')->where('regency_id', $reg->id)->orderBy('district')->get()->toArray();
        }
    }

    public function updatedCityQuery($value)
    {
        $q = trim($value);

        if ($q === '') {
            $this->searchResults = [];
            return;
        }

        $limit = 10;

        $results = City::where('is_active', true)
            ->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                        ->orWhere('province', 'like', "%{$q}%")
                        ->orWhere('code', 'like', "%{$q}%");
            })
            ->whereRaw("COALESCE(code,'') NOT LIKE 'reqd-%' AND COALESCE(code,'') NOT LIKE 'regd-%'")
            ->select('id', 'name', 'province', 'code')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->toArray();

        if (count($results) < $limit) {
            $remaining = $limit - count($results);
            $regRows = collect();

            if (Schema::hasTable('req_regencies') && Schema::hasTable('req_provinces')) {
                $regRows = \Illuminate\Support\Facades\DB::table('req_regencies')
                    ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                    ->where(function ($builder) use ($q) {
                        $builder->where('req_regencies.regency', 'like', "%{$q}%")
                                ->orWhere('req_provinces.province', 'like', "%{$q}%");
                    })
                    ->select('req_regencies.id as regency_id', 'req_regencies.regency', 'req_regencies.type', 'req_provinces.province')
                    ->orderBy('req_regencies.regency')
                    ->limit($remaining)
                    ->get();
            }

            if (count($regRows) < $remaining && Schema::hasTable('req_districts') && Schema::hasTable('req_regencies') && Schema::hasTable('req_provinces')) {
                $remaining2 = $remaining - count($regRows);
                $distRows = \Illuminate\Support\Facades\DB::table('req_districts')
                    ->join('req_regencies', 'req_districts.regency_id', '=', 'req_regencies.id')
                    ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                    ->where(function ($builder) use ($q) {
                        $builder->where('req_districts.district', 'like', "%{$q}%")
                                ->orWhere('req_regencies.regency', 'like', "%{$q}%")
                                ->orWhere('req_provinces.province', 'like', "%{$q}%");
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw("CONCAT('reqd-', req_districts.id) as regency_id"), 'req_districts.district as regency', 'req_regencies.regency as parent_regency', \Illuminate\Support\Facades\DB::raw('null as type'), 'req_provinces.province')
                    ->orderBy('req_districts.district')
                    ->limit($remaining2)
                    ->get();

                foreach ($distRows as $r) {
                    $regRows->push($r);
                }
            }

            foreach ($regRows as $r) {
                $regencyIdStr = is_string($r->regency_id) ? $r->regency_id : (string) $r->regency_id;
                $display = null;
                $targetCity = null;

                if (strpos($regencyIdStr, 'reqd-') === 0) {
                    try {
                        $did = substr($regencyIdStr, 5);
                        $parentRow = \Illuminate\Support\Facades\DB::table('req_districts')
                            ->join('req_regencies', 'req_districts.regency_id', '=', 'req_regencies.id')
                            ->join('req_provinces', 'req_regencies.province_id', '=', 'req_provinces.id')
                            ->where('req_districts.id', $did)
                            ->select('req_districts.district as district', 'req_regencies.id as parent_regency_id', 'req_regencies.regency as parent_regency', 'req_provinces.province')
                            ->first();
                        if ($parentRow) {
                            $parentCode = 'reqr-' . $parentRow->parent_regency_id;
                            $targetCity = City::firstOrCreate(
                                ['code' => $parentCode],
                                ['name' => $parentRow->parent_regency, 'province' => $parentRow->province, 'is_active' => true]
                            );
                            $display = $parentRow->district . ', ' . $parentRow->parent_regency . ', ' . $parentRow->province;
                        }
                    } catch (\Throwable $e) {
                    }
                }

                if ($targetCity && !$targetCity->is_active) {
                    continue;
                }

                if (! $targetCity) {
                    $existing = City::where('code', $r->regency_id)->orWhere('name', $r->regency)->first();
                    if ($existing && !$existing->is_active) {
                        continue;
                    }

                    $targetCity = $existing ?: City::create(
                        ['code' => $r->regency_id, 'name' => $r->regency, 'province' => $r->province, 'type' => $r->type ?? null, 'is_active' => true]
                    );
                    if (! $display && ! empty($r->parent_regency)) {
                        $display = $r->regency . ', ' . $r->parent_regency . ', ' . $r->province;
                    }
                }

                if ($targetCity && !$targetCity->is_active) {
                    continue;
                }

                if (! $display) {
                    $display = $targetCity->name . ', ' . $targetCity->province;
                }

                $exists = false;
                foreach ($results as $res) {
                    if ($res['id'] == $targetCity->id) {
                        $exists = true;
                        break;
                    }
                }
                if (! $exists && $targetCity->is_active) {
                    $item = ['id' => $targetCity->id, 'name' => $targetCity->name, 'province' => $targetCity->province, 'code' => $targetCity->code];
                    if ($display) {
                        $item['display'] = $display;
                    }
                    $results[] = $item;
                }
            }
        }

        $this->searchResults = $results;
    }

    public function updatedScheduledDate($value)
    {
        $this->validateScheduleDateTime();
    }

    public function updatedScheduledTime($value)
    {
        $this->validateScheduleDateTime();
    }

    public function validateScheduleDateTime(): bool
    {
        $this->resetErrorBag(['scheduled_date', 'scheduled_time']);

        if (empty($this->scheduled_date)) {
            $this->addError('scheduled_date', 'Tanggal pelaksanaan bantuan wajib diisi');
            return false;
        }

        if (empty($this->scheduled_time)) {
            $this->addError('scheduled_time', 'Jam pelaksanaan bantuan wajib diisi');
            return false;
        }

        if (!preg_match('/^(?:[0-1]?\d|2[0-3]):[0-5]\d$/', $this->scheduled_time)) {
            $this->addError('scheduled_time', 'Format waktu tidak valid. Gunakan format HH:MM (contoh: 09:30 atau 14:00)');
            return false;
        }

        $tz = $this->timezoneIana ?: 'Asia/Jakarta';
        $now = Carbon::now($tz);
        $todayStr = $now->format('Y-m-d');

        if ($this->scheduled_date < $todayStr) {
            $this->addError('scheduled_date', 'Tanggal pelaksanaan tidak boleh sebelum hari ini');
            return false;
        }

        if ($this->help_type === 'urgent') {
            $maxUrgentDate = $now->copy()->addDay()->format('Y-m-d');
            if ($this->scheduled_date > $maxUrgentDate) {
                $this->addError('scheduled_date', 'Untuk bantuan mendesak (urgent), tanggal hanya bisa hari ini atau besok');
                return false;
            }

            try {
                $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $this->scheduled_date . ' ' . $this->scheduled_time, $tz);
                $minTime = $now->copy()->addMinutes(15);
                if ($scheduledAt->lt($minTime)) {
                    if ($this->scheduled_date === $todayStr && $scheduledAt->gte($now->copy()->addMinutes(8))) {
                        $this->scheduled_time = $minTime->format('H:i');
                    } else {
                        $this->addError('scheduled_time', 'Untuk bantuan mendesak (urgent), jadwal minimal 15 menit setelah waktu sekarang (minimal pukul ' . $minTime->format('H:i') . ' ' . $this->timezoneLabel . ')');
                        return false;
                    }
                }
            } catch (\Exception $e) {
                $this->addError('scheduled_time', 'Format jam tidak valid');
                return false;
            }
        } else {
            if ($this->scheduled_date === $todayStr) {
                try {
                    $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $this->scheduled_date . ' ' . $this->scheduled_time, $tz);
                    if ($scheduledAt->lt($now)) {
                        $this->addError('scheduled_time', 'Jam pelaksanaan tidak boleh sebelum jam sekarang (' . $now->format('H:i') . ')');
                        return false;
                    }
                } catch (\Exception $e) {
                    $this->addError('scheduled_time', 'Format jam tidak valid');
                    return false;
                }
            }
        }

        return true;
    }

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'equipment_provided' => 'nullable|string|max:1000',
        'amount' => 'required|numeric|min:10000|max:10000000',
        'category_id' => 'required|exists:categories,id',
        'city_id' => 'required|exists:cities,id,is_active,1',
        'location' => 'required|string|max:255',
        'full_address' => 'required|string|max:1000',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'photo' => 'nullable|image|max:2048',
        'scheduled_date' => 'required|date',
        'scheduled_time' => ['required', 'regex:/^(?:[0-1]?\d|2[0-3]):[0-5]\d$/'],
    ];

    protected $messages = [
        'title.required' => 'Judul bantuan wajib diisi',
        'description.required' => 'Deskripsi bantuan wajib diisi',
        'amount.required' => 'Nominal uang harus diisi',
        'amount.numeric' => 'Nominal harus berupa angka',
        'amount.min' => 'Nominal tidak boleh kurang dari nilai minimal yang ditetapkan',
        'amount.max' => 'Nominal maksimal Rp 10.000.000',
        'category_id.required' => 'Kategori bantuan wajib dipilih',
        'city_id.required' => 'Kota wajib dipilih',
        'city_id.exists' => 'Kota yang dipilih saat ini sedang tidak aktif.',
        'location.required' => 'Detail patokan lokasi bantuan wajib diisi',
        'full_address.required' => 'Alamat lengkap wajib diisi',
        'latitude.required' => 'Silakan tentukan titik lokasi pada peta',
        'longitude.required' => 'Silakan tentukan titik lokasi pada peta',
        'scheduled_date.required' => 'Tanggal pelaksanaan bantuan wajib diisi',
        'scheduled_date.date' => 'Format tanggal tidak valid',
        'scheduled_time.required' => 'Jam pelaksanaan bantuan wajib diisi',
        'scheduled_time.regex' => 'Format waktu tidak valid. Gunakan format 24-jam HH:MM, contoh: 09:30 atau 14:00',
    ];

    public function save()
    {
        $user = auth()->user();
        if (!$user || !$user->isProfileComplete()) {
            $missing = implode(', ', optional($user)->getMissingProfileFields() ?? []);
            session()->flash('error', "Harap lengkapi profil Anda ({$missing}) terlebih dahulu sebelum mengajukan bantuan.");
            return;
        }

        if (!$user->verified) {
            session()->flash('error', 'Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi disetujui sebelum mengajukan bantuan.');
            return;
        }

        if (!$user->canCreateMoreHelps()) {
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', 'Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 permintaan bantuan. Silakan verifikasi email Anda terlebih dahulu untuk membuat bantuan baru.');
            } else {
                session()->flash('error', 'Anda telah mencapai batas maksimal permintaan bantuan yang dapat dibuat.');
            }
            return;
        }

        $minNominal = $this->help_type === 'urgent'
            ? (float) AppSetting::get('default_urgent_nominal', 50000)
            : (float) AppSetting::get('min_help_nominal', 10000);
        $maxNominal = (float) AppSetting::get('max_help_nominal', 10000000);
        $custPercent = (float) AppSetting::get('customer_service_fee_percent', 10);
        $mitraPercent = (float) AppSetting::get('mitra_platform_fee_percent', 10);
        $adminFee = 0;

        $this->rules['amount'] = 'required|numeric|min:' . $minNominal . '|max:' . $maxNominal;
        $this->messages['amount.min'] = 'Nominal minimal ' . ($this->help_type === 'urgent' ? 'untuk bantuan urgent adalah ' : '') . 'Rp ' . number_format($minNominal, 0, ',', '.');
        $this->messages['amount.max'] = 'Nominal maksimal Rp ' . number_format($maxNominal, 0, ',', '.');

        if ($this->help_type === 'urgent') {
            $this->rules['auto_cancel_minutes'] = 'required|integer|min:30|max:180';
            $this->messages['auto_cancel_minutes.required'] = 'Batas waktu tunggu pembatalan otomatis wajib ditentukan';
            $this->messages['auto_cancel_minutes.min'] = 'Batas waktu tunggu minimal 30 menit';
            $this->messages['auto_cancel_minutes.max'] = 'Batas waktu tunggu maksimal 180 menit';
        }

        $this->validate();

        if (!$this->validateScheduleDateTime()) {
            return;
        }

        $userId = auth()->id();
        $userBalance = UserBalance::firstOrCreate(['user_id' => $userId], ['balance' => 0]);

        $baseAmount = (float) $this->amount;
        $custFeeAmount = round(($baseAmount * $custPercent) / 100);
        $totalPaid = $baseAmount + $custFeeAmount;

        $mitraFeeAmount = round(($baseAmount * $mitraPercent) / 100);
        $netMitra = $baseAmount - $mitraFeeAmount;

        if ($userBalance->balance < $totalPaid) {
            $this->checkPendingTopup();
            $this->insufficientMessage = 'Saldo Anda tidak cukup. Total yang harus dibayar: Rp ' . number_format($totalPaid, 0, ',', '.');
            $this->showInsufficientModal = true;
            return;
        }

        DB::transaction(function () use ($userId, $baseAmount, $custPercent, $custFeeAmount, $adminFee, $totalPaid, $mitraPercent, $mitraFeeAmount, $netMitra) {
            $photoPath = null;
            if ($this->photo) {
                $photoPath = $this->photo->store('helps', 'public');
            }

            $orderId = $this->generateOrderId();
            $scheduledAt = date('Y-m-d H:i:s', strtotime($this->scheduled_date . ' ' . $this->scheduled_time));

            $lat = $this->latitude;
            $lng = $this->longitude;
            if (empty($lat) || empty($lng)) {
                $city = City::find($this->city_id);
                $lat = $city?->latitude ?: -7.7956;
                $lng = $city?->longitude ?: 110.3695;
            }

            $isUrgent = ($this->help_type === 'urgent');
            $minutes = $isUrgent ? (int) ($this->auto_cancel_minutes ?: 30) : null;
            $autoCancelAt = ($isUrgent && $minutes) ? now()->addMinutes($minutes) : null;

            $help = Help::create([
                'user_id'              => $userId,
                'order_id'             => $orderId,
                'help_type'            => $this->help_type ?: 'scheduled',
                'auto_cancel_minutes'  => $minutes,
                'auto_cancel_at'       => $autoCancelAt,
                'category_id'          => $this->category_id,
                'city_id'              => $this->city_id,
                'title'                => $this->title,
                'amount'               => $baseAmount,
                'base_amount'          => $baseAmount,
                'customer_fee_percent' => $custPercent,
                'customer_fee_amount'  => $custFeeAmount,
                'total_customer_paid'  => $totalPaid,
                'mitra_fee_percent'    => $mitraPercent,
                'mitra_fee_amount'     => $mitraFeeAmount,
                'net_mitra_amount'     => $netMitra,
                'admin_fee'            => 0,
                'total_amount'         => $totalPaid,
                'description'          => $this->description,
                'equipment_provided'   => $this->equipment_provided,
                'location'             => $this->location,
                'full_address'         => $this->full_address,
                'scheduled_at'         => $scheduledAt,
                'latitude'             => $lat,
                'longitude'            => $lng,
                'photo'                => $photoPath,
                'status'               => 'menunggu_mitra',
            ]);

            $userBalance = UserBalance::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
            $userBalance->deductBalance($totalPaid, 'Pembayaran bantuan #' . $help->id, $help->id);

            $customerUser = auth()->user();
            if ($customerUser && !$customerUser->isShadowBanned()) {
                $mitras = \App\Models\User::where('role', 'mitra')
                    ->where('status', 'active')
                    ->where('verified', true)
                    ->where('is_shadow_banned', false)
                    ->where('city_id', $this->city_id)
                    ->get();

                if ($mitras->count() > 0) {
                    \Illuminate\Support\Facades\Notification::send($mitras, new \App\Notifications\NewHelpRequestNotification($help));
                }
            }
        });

        $this->showConfirmModal = false;
        $this->showSuccessModal = true;
        session()->flash('message', 'Permintaan bantuan berhasil dibuat! Permintaan sedang menunggu mitra.');
    }

    public function prepareConfirm()
    {
        $user = auth()->user();

        if ($user && !$user->canCreateMoreHelps()) {
            if (!$user->hasVerifiedEmail()) {
                session()->flash('error', 'Akun Anda belum verifikasi email dan telah mencapai batas maksimal 2 permintaan bantuan. Silakan verifikasi email Anda terlebih dahulu untuk membuat bantuan baru.');
            } else {
                session()->flash('error', 'Anda telah mencapai batas maksimal permintaan bantuan yang dapat dibuat.');
            }
            return;
        }

        if (!$user || !$user->isProfileComplete()) {
            $missing = implode(', ', optional($user)->getMissingProfileFields() ?? []);
            session()->flash('error', "Harap lengkapi profil Anda ({$missing}) terlebih dahulu sebelum mengajukan bantuan.");
            return;
        }

        if (!$user->verified) {
            session()->flash('error', 'Akun Anda belum terverifikasi KTP oleh Admin. Silakan tunggu proses verifikasi disetujui sebelum mengajukan bantuan.');
            return;
        }

        $minNominal = $this->help_type === 'urgent'
            ? (float) AppSetting::get('default_urgent_nominal', 50000)
            : (float) AppSetting::get('min_help_nominal', 10000);
        $this->minNominal = (int) $minNominal;
        $maxNominal = (float) AppSetting::get('max_help_nominal', 10000000);
        $custPercent = (float) AppSetting::get('customer_service_fee_percent', 10);
        $adminFee = 0;

        // Fallback koordinat jika belum dipilih di peta
        if (empty($this->latitude) || empty($this->longitude)) {
            if ($this->city_id) {
                $city = City::find($this->city_id);
                if ($city && $city->latitude && $city->longitude) {
                    $this->latitude = (float) $city->latitude;
                    $this->longitude = (float) $city->longitude;
                }
            }
            if (empty($this->latitude) || empty($this->longitude)) {
                $this->latitude = -7.7956;
                $this->longitude = 110.3695;
            }
        }

        $this->rules['amount'] = 'required|numeric|min:' . $minNominal . '|max:' . $maxNominal;
        $this->messages['amount.min'] = 'Nominal minimal ' . ($this->help_type === 'urgent' ? 'untuk bantuan urgent adalah ' : '') . 'Rp ' . number_format($minNominal, 0, ',', '.');
        $this->messages['amount.max'] = 'Nominal maksimal Rp ' . number_format($maxNominal, 0, ',', '.');

        if ($this->help_type === 'urgent') {
            $this->rules['auto_cancel_minutes'] = 'required|integer|min:30|max:180';
            $this->messages['auto_cancel_minutes.required'] = 'Batas waktu tunggu pembatalan otomatis wajib ditentukan';
            $this->messages['auto_cancel_minutes.min'] = 'Batas waktu tunggu minimal 30 menit';
            $this->messages['auto_cancel_minutes.max'] = 'Batas waktu tunggu maksimal 180 menit';
        }

        $this->validate();

        if (!$this->validateScheduleDateTime()) {
            return;
        }

        $baseAmount = (float) $this->amount;
        $custFeeAmount = round(($baseAmount * $custPercent) / 100);
        $totalPaid = $baseAmount + $custFeeAmount;

        $userId = auth()->id();
        $userBalance = UserBalance::firstOrCreate(['user_id' => $userId], ['balance' => 0]);

        if ($userBalance->balance < $totalPaid) {
            $this->checkPendingTopup();
            $deficit = max(0, $totalPaid - (float) $userBalance->balance);
            $this->topupDeficit = $deficit;
            $this->topupAmount = $deficit > 10000 ? (int) (ceil($deficit / 1000) * 1000) : 10000;
            $this->currentBalance = (float) $userBalance->balance;
            $this->confirmAmount = $baseAmount;
            $this->confirmAdminFee = 0;
            $this->confirmCustomerFee = $custFeeAmount;
            $this->confirmCustomerFeePercent = $custPercent;
            $this->confirmTotal = $totalPaid;
            $this->insufficientMessage = 'Saldo Anda saat ini Rp ' . number_format($userBalance->balance, 0, ',', '.') . ', sedangkan total yang harus dibayar adalah Rp ' . number_format($totalPaid, 0, ',', '.') . ' (Kurang Rp ' . number_format($deficit, 0, ',', '.') . ').';
            $this->loadPaymentSettings();
            $this->calculateTopupFee();
            $this->showInsufficientModal = true;
            return;
        }

        $this->confirmAmount = $baseAmount;
        $this->confirmAdminFee = 0;
        $this->confirmCustomerFee = $custFeeAmount;
        $this->confirmCustomerFeePercent = $custPercent;
        $this->confirmTotal = $totalPaid;

        $this->confirmScheduled = Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_time)->translatedFormat('d F Y, H:i');
        $this->confirmHelpType = $this->help_type;
        $this->confirmAutoCancelMinutes = (int) ($this->auto_cancel_minutes ?: 30);
        $this->currentBalance = $userBalance->balance ?? 0;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
    }

    public function closeInsufficientModal()
    {
        $this->showInsufficientModal = false;
        $this->insufficientMessage = '';
    }

    public function setTopupQuickAmount($amount)
    {
        $this->topupAmount = (int) $amount;
        $this->calculateTopupFee();
        $this->dispatch('topup-amount-updated', $this->topupAmount);
    }

    public function updatedTopupAmount()
    {
        $this->topupAmount = (int) preg_replace('/\D/', '', (string) $this->topupAmount);
        $this->calculateTopupFee();
    }

    public function calculateTopupFee()
    {
        $amount = (float) $this->topupAmount;
        if ($amount <= 0) {
            $this->topupAdminFee = 0;
            $this->topupTotalTransfer = 0;
            return;
        }

        $tier1_limit = (int) AppSetting::get('topup_tier1_limit', 50000);
        $tier1_fee   = (int) AppSetting::get('topup_tier1_fee', 9000);
        $tier2_limit = (int) AppSetting::get('topup_tier2_limit', 100000);
        $tier2_fee   = (int) AppSetting::get('topup_tier2_fee', 7500);
        $tier3_pct   = (float) AppSetting::get('topup_tier3_percentage', 3);
        $tier3_max   = (int) AppSetting::get('topup_tier3_max', 15000);

        if ($amount < $tier1_limit) {
            $fee = $tier1_fee;
        } elseif ($amount < $tier2_limit) {
            $fee = $tier2_fee;
        } else {
            $fee = min($amount * ($tier3_pct / 100), $tier3_max);
        }

        $this->topupAdminFee      = (int) round($fee);
        $this->topupTotalTransfer = (int) round($amount + $fee);
    }

    public function loadPaymentSettings()
    {
        $raw = AppSetting::get('topup_payment_methods', '{}');
        $methods = json_decode((string) $raw, true) ?: [];

        $this->qrisEnabled = $methods['qris']['enabled'] ?? true;

        $defaultBanks = [
            ['code' => 'bca', 'name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'mandiri', 'name' => 'Mandiri', 'account_number' => '0987654321', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bni', 'name' => 'BNI', 'account_number' => '5555666677', 'account_name' => 'PT sayabantu', 'enabled' => true],
            ['code' => 'bri', 'name' => 'BRI', 'account_number' => '8888999900', 'account_name' => 'PT sayabantu', 'enabled' => true],
        ];

        $banks = $methods['banks'] ?? $defaultBanks;

        $this->availableBanks = collect($banks)
            ->filter(fn ($bank) => $bank['enabled'] ?? false)
            ->map(fn ($bank) => array_merge($bank, ['value' => 'bank_' . ($bank['code'] ?? '')]))
            ->values()
            ->toArray();

        if (empty($this->topupMethod) || $this->topupMethod === 'all') {
            $this->topupMethod = $this->qrisEnabled ? 'qris' : ($this->availableBanks[0]['value'] ?? 'bank_bca');
        }
    }

    public function checkPendingTopup()
    {
        $userId = auth()->id();
        if ($userId) {
            $this->activePendingTopup = BalanceTransaction::where('user_id', $userId)
                ->where('type', 'topup')
                ->where('status', 'waiting_approval')
                ->latest()
                ->first();
            $this->hasActivePendingTopup = !is_null($this->activePendingTopup);
        } else {
            $this->hasActivePendingTopup = false;
            $this->activePendingTopup = null;
        }
    }

    public function selectTopupMethod($method)
    {
        $this->topupMethod = $method;
    }

    public function processDirectTopup()
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        if (!$user->canTopup()) {
            $this->addError('topupAmount', $user->getCannotTopupReason() ?? 'Akun Anda belum memenuhi syarat untuk melakukan top up.');
            return;
        }

        $this->checkPendingTopup();
        if ($this->hasActivePendingTopup) {
            $this->addError('topupAmount', 'Anda masih memiliki permintaan top-up (' . ($this->activePendingTopup->request_code ?? '#' . $this->activePendingTopup->id) . ') yang sedang menunggu persetujuan admin. Harap tunggu hingga diproses.');
            session()->flash('topup_error', 'Anda masih memiliki permintaan top-up yang menunggu persetujuan admin. Harap tunggu hingga diproses.');
            return;
        }

        $topupVal = (float) $this->topupAmount;
        if ($topupVal < 10000) {
            $this->addError('topupAmount', 'Minimal top up adalah Rp 10.000');
            return;
        }

        $this->validate([
            'topupAmount' => 'required|numeric|min:10000|max:10000000',
            'topupMethod' => 'required|string',
            'topupReceipt' => 'required|image|max:2048',
        ], [
            'topupAmount.required' => 'Nominal top up wajib diisi',
            'topupAmount.min' => 'Minimal top up adalah Rp 10.000',
            'topupAmount.max' => 'Maksimal top up adalah Rp 10.000.000',
            'topupMethod.required' => 'Metode pembayaran wajib dipilih',
            'topupReceipt.required' => 'Bukti transfer wajib diunggah',
            'topupReceipt.image' => 'File bukti harus berupa gambar',
            'topupReceipt.max' => 'Ukuran maksimal gambar 2MB',
        ]);

        try {
            $user = auth()->user();
            $orderId = 'TOPUP-MANUAL-' . $user->id . '-' . time();
            $receiptPath = $this->topupReceipt->store('proof-of-payment', 'public');

            $methodName = 'Transfer Bank';
            if ($this->topupMethod === 'qris') {
                $methodName = 'QRIS';
            } else {
                foreach ($this->availableBanks as $b) {
                    if ($b['value'] === $this->topupMethod) {
                        $methodName = 'Transfer ' . $b['name'];
                        break;
                    }
                }
            }

            $date = now()->format('Ymd');
            $lastCode = BalanceTransaction::where('request_code', 'like', "TPU-{$date}-%")
                ->orderBy('id', 'desc')
                ->first();
            $sequence = 1;
            if ($lastCode) {
                $parts = explode('-', $lastCode->request_code);
                $sequence = intval($parts[2] ?? 0) + 1;
            }
            $requestCode = "TPU-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);

            $this->calculateTopupFee();

            $transaction = BalanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $topupVal,
                'admin_fee' => $this->topupAdminFee,
                'total_payment' => $this->topupTotalTransfer,
                'type' => 'topup',
                'description' => 'Top up saldo via ' . $methodName . ' saat buat bantuan',
                'order_id' => $orderId,
                'request_code' => $requestCode,
                'status' => 'waiting_approval',
                'customer_name' => $user->name,
                'customer_phone' => $user->phone,
                'customer_email' => $user->email,
                'payment_method' => $this->topupMethod,
                'proof_of_payment' => $receiptPath,
                'expired_at' => now()->addHours(24),
            ]);

            try {
                $cityAdmins = \App\Models\User::where('role', 'admin')
                    ->where('status', 'active')
                    ->when($user->city_id, function ($query, $cityId) {
                        $query->where('city_id', $cityId);
                    })
                    ->get();

                $superAdmins = \App\Models\User::whereIn('role', ['superadmin', 'super_admin'])
                    ->where('status', 'active')
                    ->get();

                $allAdmins = $cityAdmins->merge($superAdmins)->unique('id');
                foreach ($allAdmins as $admin) {
                    $admin->notify(new \App\Notifications\NewTopupRequest($transaction));
                }
            } catch (\Throwable $err) {
                \Log::warning('Gagal kirim notifikasi topup admin: ' . $err->getMessage());
            }

            $this->dispatch('topupRequestCreated');
            $this->showInsufficientModal = false;
            $this->reset(['topupReceipt']);

            session()->flash('message', 'Bukti transfer berhasil dikirim! Kode request: ' . $requestCode . '. Silakan tunggu verifikasi admin.');
        } catch (\Throwable $e) {
            \Log::error('Manual Topup error: ' . $e->getMessage());
            $this->addError('topupAmount', 'Gagal mengirim bukti: ' . $e->getMessage());
        }
    }

    public function onTopupCompleted()
    {
        $userId = auth()->id();
        $newBalance = UserBalance::recalculateForUser($userId);
        $this->currentBalance = $newBalance;
        $this->showInsufficientModal = false;

        $baseAmount = (float) $this->amount;
        $custPercent = (float) AppSetting::get('customer_service_fee_percent', 10);
        $custFeeAmount = ($baseAmount * $custPercent) / 100;
        $total = $baseAmount + $custFeeAmount;

        if ($newBalance >= $total) {
            session()->flash('topup_success', '🎉 Top up berhasil! Saldo Anda sekarang: Rp ' . number_format($newBalance, 0, ',', '.') . '. Silakan konfirmasi permintaan bantuan.');
            $this->prepareConfirm();
        } else {
            session()->flash('topup_success', 'Top up berhasil! Saldo saat ini: Rp ' . number_format($newBalance, 0, ',', '.'));
        }
    }

    public function setCityId($id)
    {
        $this->city_id = $id;
        $city = City::find($id);
        if ($city) {
            $usedDisplay = false;
            if (! empty($this->searchResults)) {
                foreach ($this->searchResults as $res) {
                    if (isset($res['id']) && $res['id'] == $id) {
                        if (! empty($res['display'])) {
                            $this->cityQuery = $res['display'];
                            $this->searchResults = [];
                            $usedDisplay = true;
                            break;
                        }
                        break;
                    }
                }
            }

            if (! $usedDisplay) {
                $this->cityQuery = $city->name . ', ' . $city->province;
            }

            $zone = $this->computeTimezoneLabelFromCity($city);
            $iana = $this->ianaForZone($zone);
            $this->timezoneLabel = $zone;
            $this->timezoneIana = $iana;
            $this->dispatch('help:timezone-changed', zone: $zone, iana: $iana);
        }
        $this->searchResults = [];
    }

    private function computeTimezoneLabelFromCity(City $city)
    {
        if (! empty($city->longitude)) {
            $lon = floatval($city->longitude);
            if ($lon >= 130) {
                return 'WIT';
            }
            if ($lon >= 115) {
                return 'WITA';
            }
            return 'WIB';
        }

        $prov = strtolower($city->province ?? '');
        $eastern = ['papua', 'papua barat', 'maluku', 'maluku utara'];
        foreach ($eastern as $p) {
            if (strpos($prov, $p) !== false) {
                return 'WIT';
            }
        }

        $centralKeywords = ['bali', 'nusa tenggara', 'sulawesi', 'kalimantan tengah', 'kalimantan timur', 'kalimantan selatan'];
        foreach ($centralKeywords as $p) {
            if (strpos($prov, $p) !== false) {
                return 'WITA';
            }
        }

        return 'WIB';
    }

    private function ianaForZone($zone)
    {
        switch ($zone) {
            case 'WITA':
                return 'Asia/Makassar';
            case 'WIT':
                return 'Asia/Jayapura';
            default:
                return 'Asia/Jakarta';
        }
    }

    public function clearCity()
    {
        $this->city_id = '';
        $this->cityQuery = '';
        $this->searchResults = [];
    }

    private function generateOrderId()
    {
        for ($i = 0; $i < 5; $i++) {
            $candidate = 'HELP-' . date('YmdHis') . '-' . random_int(1000, 9999);
            if (!Help::where('order_id', $candidate)->exists()) {
                return $candidate;
            }
            usleep(200);
        }

        return 'HELP-' . uniqid();
    }

    public function render()
    {
        $this->checkPendingTopup();

        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = \App\Models\Category::where('is_active', true)
            ->orderByRaw("name = 'Lainnya' ASC")
            ->orderBy('name')
            ->get();

        return view('livewire.customer.helps.create', [
            'cities' => $cities,
            'categories' => $categories,
        ]);
    }
}